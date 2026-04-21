<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ $subheading ?? __('Enter the details for the education point below.') }}</flux:subheading>
        </div>

        <flux:button href="{{ route('student.group.index') }}" wire:navigate variant="ghost" icon="list-bullet">
            {{ __('Education Points List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="{{ $type }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- Basic Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2">
                <flux:heading size="lg">{{ __('Basic Information') }}</flux:heading>
            </div>

            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Name') }}</flux:label>
                <flux:input type="text" wire:model="name" :placeholder="__('Enter point name')"  style="height: auto" />
                <flux:error name="name" />
            </flux:field>

            {{-- Batch No --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Batch No') }}</flux:label>
                <flux:input type="number" wire:model="batch_no" min="1" :placeholder="__('Enter batch number')" />
                <flux:error name="batch_no" />
            </flux:field>


            
            {{-- Partner Institution --}}
            <flux:select wire:model="partner_institutions_id" :label="__('Partner Institution')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Partner Institution') }}</option>
                @foreach ($partners as $partner)
                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                @endforeach
            </flux:select>

            
            {{-- Dates --}}
            <flux:field>
                <flux:label>{{ __('Start Date') }}</flux:label>
                <flux:input type="date" wire:model="start_date" />
                <flux:error name="start_date" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('End Date') }}</flux:label>
                <flux:input type="date" wire:model="end_date" />
                <flux:error name="end_date" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Start Time') }}</flux:label>
                <flux:input type="time" wire:model="start_time" />
                <flux:error name="start_time" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('End Time') }}</flux:label>
                <flux:input type="time" wire:model="end_time" />
                <flux:error name="end_time" />
            </flux:field>
            {{-- Max Students --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Max Students') }}</flux:label>
                <flux:input type="number" wire:model="max_students" min="0" />
                <flux:error name="max_students" />
            </flux:field>

            {{-- Min Students --}}
            <flux:field>
                <flux:label>{{ __('Min Students') }}</flux:label>
                <flux:input type="number" wire:model="min_students" min="0" />
                <flux:error name="min_students" />
            </flux:field>

            {{-- Region --}}
            <flux:select wire:model.live="region_id" :label="__('Region')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Region') }}</option>
                @foreach ($regions as $region)
                    {{-- Assuming region has id and name --}}
                    <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                @endforeach
            </flux:select>

            {{-- City --}}
            <flux:select wire:model.live="city_id" :label="__('City')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select City') }}</option>
                @foreach ($cities as $city)
                    {{-- Assuming city has id and name --}}
                    <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                @endforeach
            </flux:select>

            {{-- Neighbourhood --}}
            <flux:select wire:model.live="neighbourhood_id" :label="__('Neighbourhood')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Neighbourhood') }}
                </option>
                @foreach ($neighbourhoods as $neighbourhood)
                    <option value="{{ $neighbourhood->id }}">
                        {{ $neighbourhood->neighbourhood_name ?? 'Neighbourhood ' . $neighbourhood->id }}</option>
                @endforeach
            </flux:select>

            {{-- Location --}}
            <flux:select wire:model="location_id" :label="__('Location')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Location') }}</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->location_name ?? 'Location ' . $location->id }}
                    </option>
                @endforeach
            </flux:select>


            {{-- Address Details --}}
            <flux:field class="md:col-span-2 lg:col-span-3">
                <flux:label>{{ __('Address Details') }}</flux:label>
                <flux:input type="text" wire:model="address_details" :placeholder="__('Enter address details')" />
                <flux:error name="address_details" />
            </flux:field>


            {{-- Status/Category (status_id) --}}
            {{-- <flux:select
                wire:model="status_id"
                :label="__('Group Status/Category')"
            >
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Status') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select> --}}



            {{-- Subjects --}}
            <div class="col-span-1 md:col-span-2 lg:col-span-3" x-data="{
                selected: @entangle('subject_to_learn_id'),
                options: {{ json_encode($subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name])) }},
                search: '',
                open: false,
                get filteredOptions() {
                    if (this.search === '') {
                        return this.options.filter(i => !this.selected.includes(i.id));
                    }
                    return this.options.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase()) && !this.selected.includes(i.id));
                },
                add(id) {
                    if (!this.selected.includes(id)) {
                        this.selected.push(id);
                    }
                    this.search = '';
                    this.open = true; // Keep open to add more
                },
                remove(id) {
                    this.selected = this.selected.filter(i => i !== id);
                },
                getName(id) {
                    const opt = this.options.find(i => i.id == id);
                    return opt ? opt.name : 'Unknown';
                }
            }" class="relative">
                <flux:label>{{ __('Subjects') }}</flux:label>

                {{-- Selected Tags Container --}}
                <div class="flex flex-wrap gap-2 mb-2">
                    <template x-for="id in selected" :key="id">
                        <div
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-600">
                            <span x-text="getName(id)"></span>
                            <button type="button" @click="remove(id)"
                                class="ml-1.5 inline-flex items-center justify-center text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300 focus:outline-none">
                                <flux:icon name="x-mark" class="size-3" />
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Search/Dropdown Trigger --}}
                <div class="relative">
                    <div class="relative">
                        <flux:input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                            @keydown.escape="open = false" placeholder="{{ __('Search and add subjects...') }}"
                            icon="magnifying-glass" />
                        <div class="absolute right-0 top-0 h-full flex items-center pr-2">
                            <button type="button" @click="open = !open"
                                class="text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300">
                                <flux:icon name="chevron-down" class="size-4" />
                            </button>
                        </div>
                    </div>

                    {{-- Dropdown Menu --}}
                    <div x-show="open && filteredOptions.length > 0"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                        style="display: none;">
                        <ul class="py-1">
                            <template x-for="option in filteredOptions" :key="option.id">
                                <li @click="add(option.id)"
                                    class="px-4 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer flex items-center justify-between">
                                    <span x-text="option.name"></span>
                                    <flux:icon name="plus" class="size-3 text-zinc-400" />
                                </li>
                            </template>
                        </ul>
                    </div>
                    <div x-show="open && filteredOptions.length === 0 && search !== ''"
                        class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg px-4 py-2 text-sm text-zinc-500"
                        style="display: none;">
                        {{ __('No subjects found.') }}
                    </div>
                </div>
            </div>


            {{-- Moderator Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('Moderator Details') }}</flux:heading>
            </div>

            {{-- Moderator Name --}}
            <flux:field>
                <flux:label>{{ __('Moderator Name') }}</flux:label>
                <flux:input type="text" wire:model="Moderator" :placeholder="__('Enter moderator name')" />
                <flux:error name="Moderator" />
            </flux:field>

            {{-- Moderator Phone --}}
            <flux:field>
                <flux:label>{{ __('Moderator Phone') }}</flux:label>
                <flux:input type="text" wire:model="Moderator_phone" :placeholder="__('000-000-000')" />
                <flux:error name="Moderator_phone" />
            </flux:field>

            {{-- Moderator Email --}}
            <flux:field>
                <flux:label>{{ __('Moderator Email') }}</flux:label>
                <flux:input type="email" wire:model="Moderator_email" :placeholder="__('example@domain.com')" />
                <flux:error name="Moderator_email" />
            </flux:field>

            {{-- Description --}}
            <flux:field class="md:col-span-2 lg:col-span-3">
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" :placeholder="__('Enter description')" />
                <flux:error name="description" />
            </flux:field>


            {{-- System Settings Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('System Settings') }}</flux:heading>
            </div>

            {{-- Activation --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Status') }}</flux:label>
                <flux:select wire:model="activation">
                    @foreach ($activations as $a)
                        <option value="{{ $a['value'] }}">{{ $a['label'] }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="activation" />
            </flux:field>

            {{-- Submit Button --}}
            <div class="md:col-span-2 lg:col-span-3 flex items-center justify-end gap-2 mt-6">
                <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $heading }}</span>
                    <span wire:loading>{{ __('Saving...') }}</span>
                </flux:button>
            </div>
            @include('layouts._show_all_input_error')
        </form>
    </div>
</div>
