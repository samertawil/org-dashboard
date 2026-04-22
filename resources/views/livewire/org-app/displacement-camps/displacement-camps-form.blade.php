<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ $subheading ?? __('Enter the details for the Displacement Camp below.') }}</flux:subheading>
        </div>

        <flux:button href="{{ route('displacement.camps.index') }}" wire:navigate variant="ghost" icon="list-bullet">
            {{ __('Camps List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center {{ session('type') == 'error' ? 'text-red-500' : '' }}"
        :status="session('message')" />

    {{-- Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="{{ isset($type) ? $type : 'save' }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- Basic Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2">
                <flux:heading size="lg" class="text-blue-600 dark:text-blue-400">{{ __('Basic Information') }}</flux:heading>
            </div>

            {{-- Name --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Camp Name') }}</flux:label>
                <flux:input type="text" wire:model="name" :placeholder="__('Enter Camp Name')" />
                <flux:error name="name" />
            </flux:field>

            {{-- Region --}}
            <flux:select wire:model.live="region_id" :label="__('Region')">
                <option value="">{{ __('Select Region') }}</option>
                @foreach ( $regions as $region)
                    <option value="{{ $region->id }}">{{ $region->region_name}}</option>
                @endforeach
            </flux:select>
            
            {{-- City --}}
            <flux:select wire:model.live="city_id" :label="__('City')">
                <option value="">{{ __('Select City') }}</option>
                @foreach ( $this->cities as $city)
                    <option value="{{ $city->id }}">{{ $city->city_name  }}</option>
                @endforeach
            </flux:select>

            {{-- Neighbourhood --}}
            <flux:select wire:model.live="neighbourhood_id" :label="__('Neighbourhood')">
                <option value="">{{ __('Select Neighbourhood') }}</option>
                @foreach ( $this->neighbourhoods as $neighbourhood)
                    <option value="{{ $neighbourhood->id }}">{{ $neighbourhood->neighbourhood_name }}</option>
                @endforeach
            </flux:select>
            
            {{-- Location --}}
            <flux:select wire:model="location_id" :label="__('Location')">
                <option value="">{{ __('Select Location') }}</option>
                @foreach ( $this->locations as $location)
                    <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                @endforeach
            </flux:select>

            {{-- Address Details --}}
            <div class="md:col-span-2 lg:col-span-3">
                <flux:field>
                    <flux:label>{{ __('Address Details') }}</flux:label>
                    <flux:textarea wire:model="address_details" rows="2" />
                    <flux:error name="address_details" />
                </flux:field>
            </div>

            {{-- Coordinates Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2 mt-4">
                <flux:heading size="lg" class="text-blue-600 dark:text-blue-400">{{ __('Coordinates & Population') }}</flux:heading>
            </div>

            {{-- Longitudes --}}
            <flux:field>
                <flux:label>{{ __('Longitudes') }}</flux:label>
                <flux:input type="text" wire:model="longitudes" :placeholder="__('e.g. 45.123456')" />
                <flux:error name="longitudes" />
            </flux:field>

            {{-- Latitude --}}
            <flux:field>
                <flux:label>{{ __('Latitude') }}</flux:label>
                <flux:input type="text" wire:model="latitude" :placeholder="__('e.g. 34.123456')" />
                <flux:error name="latitude" />
            </flux:field>
            
            <div class="hidden lg:block"></div> {{-- Spacer --}}

            {{-- Number of Families --}}
            <flux:field>
                <flux:label>{{ __('Number of Families') }}</flux:label>
                <flux:input type="number" wire:model="number_of_families" :placeholder="__('0')" />
                <flux:error name="number_of_families" />
            </flux:field>

            {{-- Number of Individuals --}}
            <flux:field>
                <flux:label>{{ __('Number of Individuals') }}</flux:label>
                <flux:input type="number" wire:model="number_of_individuals" :placeholder="__('0')" />
                <flux:error name="number_of_individuals" />
            </flux:field>
            
            <div class="hidden lg:block"></div> {{-- Spacer --}}

            {{-- Management Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2 mt-4">
                <flux:heading size="lg" class="text-blue-600 dark:text-blue-400">{{ __('Management & Needs') }}</flux:heading>
            </div>

            {{-- Moderator --}}
            <flux:field>
                <flux:label>{{ __('Moderator') }}</flux:label>
                <flux:input type="text" wire:model="Moderator" :placeholder="__('Enter Moderator Name')" />
                <flux:error name="Moderator" />
            </flux:field>

            {{-- Moderator Phone --}}
            <flux:field>
                <flux:label>{{ __('Moderator Phone') }}</flux:label>
                <flux:input type="text" wire:model="Moderator_phone" :placeholder="__('Enter Moderator Phone')" />
                <flux:error name="Moderator_phone" />
            </flux:field>

            {{-- Needs Tags --}}
            <div class="md:col-span-2 lg:col-span-3" x-data="{
                selected: @entangle('camp_main_needs') || [],
                options: {{ json_encode(collect($this->needsList)->values()) }},
                search: '',
                open: false,
                get filteredOptions() {
                    if (! Array.isArray(this.options)) return [];
                    const selectedItems = Array.isArray(this.selected) ? this.selected : [];
                    if (this.search === '') {
                        return this.options.filter(i => !selectedItems.includes(i.need));
                    }
                    return this.options.filter(i => i.need.toLowerCase().includes(this.search.toLowerCase()) && !selectedItems.includes(i.need));
                },
                add(val) {
                    if (!this.selected) this.selected = [];
                    if (val && !this.selected.includes(val)) {
                        this.selected.push(val);
                    }
                    this.search = '';
                    this.open = false; 
                },
                remove(val) {
                    if(this.selected) {
                        this.selected = this.selected.filter(i => i !== val);
                    }
                }
            }">
                <flux:label>{{ __('Camp Main Needs') }}</flux:label>

                {{-- Selected Tags Container --}}
                <div class="flex flex-wrap gap-2 mb-2" x-show="selected && selected.length > 0">
                    <template x-for="need in selected" :key="need">
                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-600">
                            <span x-text="need"></span>
                            <button type="button" @click="remove(need)" class="ml-1.5 inline-flex items-center justify-center text-zinc-400 hover:text-red-500 focus:outline-none">
                                <flux:icon name="x-mark" class="size-3" />
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Search/Input Trigger --}}
                <div class="relative">
                    <div class="relative flex items-center">
                        <flux:input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                            @keydown.enter.prevent="add(search)" @keydown.escape="open = false" 
                            placeholder="{{ __('Type a need and press Enter, or select below...') }}"
                            icon="plus" />
                    </div>

                    {{-- Dropdown Menu --}}
                    <div x-show="open && filteredOptions.length > 0"
                        class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                        style="display: none;">
                        <ul class="py-1">
                            <template x-for="option in filteredOptions" :key="option.need">
                                <li @click="add(option.need)"
                                    class="px-4 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer flex items-center justify-between">
                                    <span x-text="option.need"></span>
                                    <flux:icon name="plus" class="size-3 text-zinc-400" />
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <flux:error name="camp_main_needs" />
            </div>

            {{-- Notes --}}
            <div class="md:col-span-2 lg:col-span-3">
                <flux:field>
                    <flux:label>{{ __('Notes') }}</flux:label>
                    <flux:textarea wire:model="notes" :placeholder="__('Additional information...')" rows="3" />
                    <flux:error name="notes" />
                </flux:field>
            </div>

            {{-- Submit Button --}}
            <div class="md:col-span-2 lg:col-span-3 flex items-center justify-end gap-2 mt-6 relative z-0">
                <flux:button type="submit" variant="primary" icon="{{ isset($type) && $type === 'save' ? 'plus' : 'check' }}" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $heading ?? 'Submit' }}</span>
                    <span wire:loading>{{ isset($type) && $type === 'save' ? __('Saving...') : __('Updating...') }}</span>
                </flux:button>
            </div>
            <div class="md:col-span-2 lg:col-span-3 flex justify-end w-full text-end">
                <div class="flex flex-col items-end gap-2">
                    @include('layouts._show_all_input_error')
                    <x-auth-session-status class="{{ session('type') == 'error' ? 'text-red-500' : '' }}"
                        :status="session('message')" />
                </div>
            </div>
           
        </form>
    </div>
</div>
