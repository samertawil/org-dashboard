<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ $subheading ?? __('Enter the details for the Purchase Requisition below.') }}
            </flux:subheading>
        </div>

        <flux:button href="{{ route('purchase_request.index') }}" wire:navigate variant="ghost" icon="list-bullet">
            {{ __('Purchase List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center {{ session('type') == 'error' ? 'text-red-500' : '' }}"
        :status="session('message')" />

    {{-- Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="{{ isset($type) ? $type : 'save' }}"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- Basic Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2">
                <flux:heading size="lg" class="text-blue-600 dark:text-blue-400">{{ __('Basic Information') }}
                </flux:heading>
            </div>

            {{-- Request Number --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Request Number') }}</flux:label>
                <flux:input type="number" wire:model="request_number" :placeholder="__('Enter Request Number')" />
                <flux:error name="request_number" />
            </flux:field>



            {{-- Request Date --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Request Date') }}</flux:label>
                <flux:input type="date" wire:model.live.lazy="request_date" />
                <flux:error name="request_date" />
            </flux:field>


            {{-- Description --}}
            <div class="md:col-span-2 lg:col-span-3">
                <flux:field>
                    <flux:label>{{ __('Description') }}</flux:label>
                    <flux:textarea wire:model="description"
                        :placeholder="__('General description of the required supply...')" rows="3" />
                    <flux:error name="description" />
                </flux:field>
            </div>

            {{-- Justification --}}
            <div class="md:col-span-2 lg:col-span-3">
                <flux:field>
                    <flux:label>{{ __('Justification') }}</flux:label>
                    <flux:textarea wire:model="justification" :placeholder="__('Why is this supply needed?')"
                        rows="3" />
                    <flux:error name="justification" />
                </flux:field>
            </div>

            {{-- quotation_deadline --}}
            <flux:field class="relative z-40">
                <flux:label>{{ __('ََQuotation Deadline') }}</flux:label>
                <flux:input type="date" wire:model="quotation_deadline" />
                <flux:error name="quotation_deadline" />
            </flux:field>

            {{-- Budget Details --}}
            <flux:field class="relative z-30">
                <flux:label>{{ __('Budget Details') }}</flux:label>
                <flux:input type="text" wire:model="budget_details" :placeholder="__('Budget Source')" />
                <flux:error name="budget_details" />
            </flux:field>

            {{-- Estimated Total --}}
            <flux:field class="relative z-20">
                <div class="flex items-center  ">
                    <flux:label badge="Auto-sync ({{ $exchange_rate }}) " badgeColor="text-yellow-600">
                        {{ __('Estimated Total Dollar') }}</flux:label><span class="ml-2"
                        style="color:red; font-size:12px; font-weight:bold">Required</span>
                </div>
                <flux:input type="number" step="0.01" wire:model.live.debounce.500ms="estimated_total_dollar"
                    :placeholder="__('0.00')" />
                <flux:error name="estimated_total_dollar" />
            </flux:field>

            <flux:field class="relative z-20">
                <div class="flex items-center  ">
                    <flux:label badge="Auto-sync ({{ $exchange_rate }}) " badgeColor="text-yellow-600">
                        {{ __('Estimated Total Nis') }}</flux:label><span class="ml-2"
                        style="color:red; font-size:12px; font-weight:bold">Required</span>
                </div>

                <flux:input type="number" step="0.01" wire:model.live.debounce.500ms="estimated_total_nis"
                    :placeholder="__('0.00')" />
                <flux:error name="estimated_total_nis" />
            </flux:field>


            <flux:subheading class="md:col-span-2 lg:col-span-3 text-sm text-zinc-500 dark:text-zinc-400">
                To add new value for curreny or list all values <a href="{{ route('currency.index') }}"
                    target="_blank"><span class="text-blue-500">Press Here</span></a> </flux:subheading>

            {{-- Items Section --}}
            <div class="md:col-span-2 lg:col-span-3 mt-6 relative z-0">
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg" class="text-blue-600 dark:text-blue-400">{{ __('Items') }}
                    </flux:heading>
                    <flux:button wire:click="addPurchaseRequisitionItem" variant="ghost" icon="plus" size="sm">
                        {{ __('Add Item') }}
                    </flux:button>
                </div>

                <div class="space-y-4">
                    @foreach ($items as $index => $item)
                        <div wire:key="item-{{ $index }}"
                            class="p-4 border rounded-lg border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                {{-- Item Name --}}
                                <flux:field>
                                    <flux:label>{{ __('Item Name') }}</flux:label>
                                    <flux:input type="text" wire:model="items.{{ $index }}.item_name"
                                        placeholder="Item Name" />
                                    @error("items.{$index}.item_name")
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </flux:field>

                                {{-- Quantity --}}
                                <flux:field>
                                    <flux:label>{{ __('Quantity') }}</flux:label>
                                    <flux:input type="number" wire:model="items.{{ $index }}.quantity"
                                        placeholder="1" />
                                </flux:field>

                                {{-- Unit --}}
                                <flux:select wire:model="items.{{ $index }}.unit_id" :label="__('Unit')">
                                    <option value="">{{ __('Select Unit') }}</option>
                                    @foreach ($this->units as $s)
                                        <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                                    @endforeach
                                </flux:select>

                                {{-- Unit Price --}}
                                <flux:field>
                                    <flux:label>{{ __('Unit Price') }}</flux:label>
                                    <flux:input type="number" step="0.01"
                                        wire:model="items.{{ $index }}.unit_price" placeholder="0.00" />
                                </flux:field>

                                {{-- Currency --}}
                                <flux:select wire:model="items.{{ $index }}.currency" :label="__('Currency')">
                                    <option value="">{{ __('Select Currency') }}</option>
                                    @foreach ($this->statuses as $s)
                                        <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                                    @endforeach
                                </flux:select>

                                {{-- Description --}}
                                <div class="md:col-span-2 lg:col-span-3">
                                    <flux:field>
                                        <flux:label>{{ __('Description') }}</flux:label>
                                        <flux:input type="text"
                                            wire:model="items.{{ $index }}.item_description"
                                            placeholder="Item Description" />
                                    </flux:field>
                                </div>

                                {{-- Action --}}
                                <div class=" ">
                                    <flux:button wire:click="removePurchaseRequisitionItem({{ $index }})"
                                        variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- Suggested Vendors --}}
            <div class="col-span-1 md:col-span-2 lg:col-span-3"
                wire:key="vendors-container-{{ count($this->partners) }}" x-data="{
                    selected: @entangle('suggested_vendor_ids'),
                    options: @js($this->partners->map(fn($partner) => ['id' => $partner->id, 'name' => $partner->name])->values()),
                    search: '',
                    open: false,
                    get filteredOptions() {
                        if (!Array.isArray(this.options)) return [];
                        const selectedIds = Array.isArray(this.selected) ? this.selected : [];
                        if (this.search === '') {
                            return this.options.filter(i => !selectedIds.includes(i.id));
                        }
                        return this.options.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase()) && !selectedIds.includes(i.id));
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
                }"
                x-effect="options = @js($this->partners->map(fn($partner) => ['id' => $partner->id, 'name' => $partner->name])->values())" class="relative z-50">
                <div class="flex items-center gap-2 mb-1">
                    <flux:label badge="Required" badgeColor="text-red-600">{{ __('Suggested Vendors') }}</flux:label>
                    <flux:button style="color: blue;" wire:click="$refresh" variant="ghost" icon="arrow-path"
                        size="sm" class="text-blue-600 dark:text-blue-400"
                        tooltip="{{ __('Refresh List') }}" />
                </div>

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
                            @keydown.escape="open = false" placeholder="{{ __('Search and add vendors...') }}"
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
                        {{ __('No vendors found.') }}
                    </div>
                </div>
                <flux:error name="suggested_vendor_ids" />
                <flux:subheading class=" mt-6 md:col-span-2 lg:col-span-3 text-sm text-zinc-500 dark:text-zinc-400">
                    If you can't find your vendor in the list, you can add it <a href="{{ route('partner.create') }}"
                        target="_blank"><span class="text-blue-500">Press Here</span></a> </flux:subheading>
            </div>

            {{-- System Settings Header --}}

            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-12 mb-2">
                <flux:heading size="lg" class="text-blue-600 dark:text-blue-400">{{ __('System Settings') }}
                </flux:heading>
            </div>



            {{-- Status --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Select Status') }}</flux:label>
                <flux:select wire:model.live="status_id">
                    <option value="">{{ __('Select Status') }}</option>
                    @foreach ($this->statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
            {{-- order_count --}}
            @if ($this->status_id == 109)
                <flux:field class="relative z-30 ">
                    <flux:label>{{ __('Order Count') }}</flux:label>
                    <flux:input type="number" wire:model="order_count" :placeholder="__('Order Count')" />
                    <flux:error name="order_count" />
                </flux:field>
            @endif
            {{-- Submit Button --}}
            <div class="md:col-span-2 lg:col-span-3 flex items-center justify-end gap-2 mt-6 relative z-0">
                <flux:button type="submit" variant="primary"
                    icon="{{ isset($type) && $type === 'save' ? 'plus' : 'check' }}" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ $heading ?? 'Submit' }}</span>
                    <span wire:loading
                        wire:target="save">{{ isset($type) && $type === 'save' ? __('Saving...') : __('Updating...') }}</span>
                </flux:button>
            </div>
            <div class="md:col-span-2 lg:col-span-3 flex justify-end w-full text-end">
                <div class="flex flex-col items-end gap-2">
                    @include('layouts._show_all_input_error')
                    {{-- <x-auth-session-status class="{{ session('type') == 'error' ? 'text-red-500' : '' }}"
                        :status="session('message')" /> --}}
                </div>
            </div>
        </form>
    </div>
</div>
