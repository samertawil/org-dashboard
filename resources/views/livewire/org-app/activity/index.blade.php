<div class="flex flex-col gap-6" x-on:modal-show.window="Flux.modal($event.detail.name).show()">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Activities') }}</flux:heading>
            <flux:subheading>{{ __('Manage and filter organization Activities.') }}</flux:subheading>
        </div>
        @can('activity.create')
            <flux:button href="{{ route('activity.create') }}" wire:navigate variant="primary" icon="plus">
                {{ __('Add Activity') }}
            </flux:button>
        @endcan
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search and Filters Section --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            {{-- Name Search --}}
            <flux:field class="relative">
                <flux:label>{{ __('Activity Name') }}</flux:label>
                <flux:input wire:model.live.debounce.300ms="search" :placeholder="__('Search by name...')"
                    icon="magnifying-glass" />
                <div wire:loading wire:target="search" class="absolute right-3 bottom-2">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </flux:field>

            {{-- Start Date --}}
            <flux:field>
                <flux:label>{{ __('Start Date') }}</flux:label>
                <flux:input type="date" wire:model.live="start_date" />
            </flux:field>

            {{-- Status --}}
            <flux:select wire:model.live="status_id" :label="__('Status')">
                <option value="">{{ __('All Statuses') }}</option>
                @foreach ($this->allStatuses->where('p_id_sub', config('appConstant.activity_status')) as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>

            {{-- Region --}}
            <flux:select wire:model.live="region_id" :label="__('Region')">
                <option value="">{{ __('All Regions') }}</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                @endforeach
            </flux:select>

            {{-- City --}}
            <flux:select wire:model.live="city_id" :label="__('City')" :disabled="!$region_id">
                <option value="">{{ __('All Cities') }}</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                @endforeach
            </flux:select>



            {{-- Clear Filters --}}
            @if ($search || $start_date || $status_id || $region_id || $city_id)
                <div class="mt-4 flex items-center justify-end">
                    <flux:button
                        wire:click="$set('search', ''); $set('start_date', ''); $set('status_id', ''); $set('region_id', ''); $set('city_id', '')"
                        variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </div>
            @endif

        </div>

        <div class="overflow-x-auto -mx-6">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->activities->firstItem() }}</span>
                        {{ __('to') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->activities->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->activities->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th wire:click="sortBy('name')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Name') }}
                                @if ($sortField === 'name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('start_date')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Start Date') }}
                                @if ($sortField === 'start_date')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('status')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Status') }}
                                @if ($sortField === 'status')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Specific Sector') }}
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Region/City') }}
                        </th>
                        
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('PR #') }}
                        </th>
                        {{-- 
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Rating') }}
                        </th> --}}

                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->activities as $activity)
                        <tr wire:key="activity-{{ $activity->id }}"
                            class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-6 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $activity->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $activity->start_date }} 
                                @if ($activity->end_date <> $activity->start_date)
                                <br>
                                &rarr;
                                <br>   
                                {{ $activity->end_date }}
                                @endif
                            </td>

                            {{--
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <span @class([
                                    'text-sm max-w-xs  truncate',
                                    'text-green-600 dark:text-green-400' => $activity->status === 27,
                                    'text-yellow-600 dark:text-yellow-400' => $activity->status === 26,
                                    'text-purple-600 dark:text-purple-400' => $activity->status === 25,
                                    'text-red-600 dark:text-red-400' => $activity->status === 28,
                                ])>
                                    {{ $activity->status_name ?? ($activity->activityStatus->status_name ?? '-') }}
                                </span>
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge :color="$activity->status_info['color']" size="sm" inset="top bottom">
                                    {{ $activity->status_info['name'] }}
                                </flux:badge>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $activity->statusSpecificSector->status_name ?? '-' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $activity->regions->region_name ?? '-' }} /
                                {{ $activity->cities->city_name ?? '-' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                @php
                                    $prs = $activity->parcels->map(fn($p) => $p->purchaseRequisition)->filter()->unique('id');
                                @endphp
                                <div class="flex flex-wrap gap-1">
                                    @foreach($prs as $pr)
                                        <flux:button wire:click="showDetails({{ $pr->id }})" variant="ghost" size="sm" class="p-0 h-auto">
                                            <flux:badge size="sm" color="blue" variant="outline" class="cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">{{ $pr->request_number }}</flux:badge>
                                        </flux:button>
                                    @endforeach
                                    @if($prs->isEmpty())
                                        -
                                    @endif
                                </div>
                            </td>
                            {{-- 
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                <div class="flex items-center gap-1">
                                    <flux:icon icon="star" variant="solid" class="{{ $activity->rating_info['color'] }} w-4 h-4" />
                                    <span>{{ $activity->rating_info['rating'] }}</span>
                                </div>
                            </td> --}}

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @can('activity.create')
                                    <flux:button href="{{ route('activity.edit', $activity) }}" wire:navigate
                                        variant="ghost" size="sm" icon="pencil-square" />
{{--                                  
                                        <flux:button wire:click="delete({{ $activity->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this activity?') }}"
                                            variant="ghost" size="sm" icon="trash"
                                            class="text-red-500 hover:text-red-600 dark:hover:text-red-400" /> --}}
                                    @endcan
                                    @php $modalName = 'activity-show-' . $activity->id; @endphp

                                    <flux:modal.trigger :name="$modalName">
                                        <flux:button icon="eye" variant="ghost" size="sm"
                                            tooltip="Show All Data" wire:click="openShowModal({{ $activity->id }})">
                                        </flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal :name="$modalName"
                                        class="!w-11/12 !max-w-7xl md:!w-11/12 md:!max-w-7xl"
                                        x-on:close-modal="$wire.closeShowModal()">
                                        <div class="mt-4">
                                            @if ($selectedactivityIdForShowModal === $activity->id)
                                                <livewire:OrgApp.activity.show :activity="$activity"
                                                    wire:key="show-activity-{{ $activity->id }}" />
                                            @endif
                                        </div>
                                    </flux:modal>
                                    @can('activity.create')
                                    <div class="relative">
                                        <flux:button href="{{ route('activity.gallery', $activity->id) }}"
                                            wire:navigate icon="paper-clip" variant="ghost" size="sm"
                                            tooltip="Attachments"
                                            style="{{ $activity->attachments_count > 0 ? 'color: #3b82f6 !important;' : '' }}">
                                        </flux:button>
                                        @if ($activity->attachments_count > 0)
                                            <span
                                                class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-blue-500 ring-1 ring-white dark:ring-zinc-900"></span>
                                        @endif
                                    </div>
                                    @endcan

                                    @if($activity->beneficiary_names_count > 0)
                                        <flux:button style="color: blue !important;" wire:click="showBeneficiaries({{ $activity->id }})" icon="user-group" variant="ghost" size="sm"  tooltip="{{ __('Show Beneficiaries') }}" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No activitys found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>



        <div class="mt-4">
            {{ $this->activities->links() }}
        </div>
    </div>

    {{-- PR Details Modal --}}
    <flux:modal name="show-pr-modal" class="md:w-[800px]">
        <div class="flex flex-col gap-6 text-left">
            @if($selectedPr)
                <div class="flex justify-between items-center">
                    <flux:heading level="2" size="lg">{{ __('Purchase Requisition') }} #{{ $selectedPr->request_number }}</flux:heading>
                    <flux:button href="{{ route('purchase_request.show', $selectedPr->id) }}" variant="ghost" icon="printer" tooltip="{{ __('Print / Full View') }}">
                        {{ __('Print') }}
                    </flux:button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <flux:label>{{ __('Request Date') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200">{{ $selectedPr->request_date ? $selectedPr->request_date->format('Y-m-d') : '-' }}</div>
                        </div>
                        <div>
                            <flux:label>{{ __('Status') }}</flux:label>
                            <div><flux:badge>{{ $selectedPr->status->status_name ?? '-' }}</flux:badge></div>
                        </div>
                        @if($selectedPr->order_count)
                        <div>
                            <flux:label>{{ __('Order Count') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200">{{ $selectedPr->order_count }}</div>
                        </div>
                        @endif
                        <div>
                            <flux:label>{{ __('Requested By') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200">{{ $selectedPr->creator->name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <flux:label>{{ __('Estimated Total (Dollar)') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200 font-semibold">${{ number_format($selectedPr->estimated_total_dollar, 2) }}</div>
                        </div>
                        <div>
                            <flux:label>{{ __('Estimated Total (NIS)') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200 font-semibold">₪{{ number_format($selectedPr->estimated_total_nis, 2) }}</div>
                        </div>
                        <div>
                            <flux:label>{{ __('Quotation Deadline') }}</flux:label>
                            <div class="text-zinc-800 dark:text-zinc-200">{{ $selectedPr->quotation_deadline ? $selectedPr->quotation_deadline->format('Y-m-d') : '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:label>{{ __('Suggested Vendors') }}</flux:label>
                    <div class="flex flex-wrap gap-1">
                        @foreach($selectedPr->suggested_vendors as $vendor)
                            <flux:badge size="sm" variant="outline">{{ $vendor->name }}</flux:badge>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:label>{{ __('Description') }}</flux:label>
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded border border-zinc-200 dark:border-zinc-700">
                        {{ $selectedPr->description ?? '-' }}
                    </div>
                </div>

                @if($selectedPr->justification)
                    <div class="space-y-2">
                        <flux:label>{{ __('Justification') }}</flux:label>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded border border-zinc-200 dark:border-zinc-700 italic">
                            {{ $selectedPr->justification }}
                        </div>
                    </div>
                @endif

                <div class="space-y-2">
                    <flux:heading level="3" size="md">{{ __('Items') }}</flux:heading>
                    <div class="overflow-hidden border border-zinc-200 dark:border-zinc-700 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">{{ __('Item') }}</th>
                                    <th class="px-3 py-2 text-center">{{ __('Qty') }}</th>
                                    <th class="px-3 py-2 text-left">{{ __('Unit') }}</th>
                                    <th class="px-3 py-2 text-right">{{ __('Price') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($selectedPr->items as $item)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <div class="font-medium">{{ $item->item_name }}</div>
                                            <div class="text-xs text-zinc-500">{{ $item->item_description }}</div>
                                        </td>
                                        <td class="px-3 py-2 text-center">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2 text-left">{{ $item->unit->status_name ?? '-' }}</td>
                                        <td class="px-3 py-2 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </flux:modal>

    {{-- Beneficiaries Modal --}}
    <flux:modal name="beneficiaries-modal" class="md:w-[900px]">
        <div class="flex flex-col gap-6 text-left">
            @if($selectedActivityForBeneficiaries)
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <flux:heading level="2" size="lg">{{ __('Beneficiaries') }}</flux:heading>
                        <flux:subheading>{{ $selectedActivityForBeneficiaries->name }}</flux:subheading>
                    </div>
                    
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <flux:input wire:model.live.debounce.300ms="beneficiarySearch" :placeholder="__('Search by name...')" icon="magnifying-glass" size="sm" class="w-full md:w-64" />
                        
                        <flux:button wire:click="exportBeneficiaries({{ $selectedActivityForBeneficiaries->id }})" variant="ghost" icon="document-arrow-down" class="text-green-600" size="sm">
                            {{ __('Export') }}
                        </flux:button>
                    </div>
                </div>

                <div class="overflow-hidden border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                            <tr>
                                <th class="px-4 py-2 text-left">{{ __('Full Name') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('Receipt Date') }}</th>
                              
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($this->selectedActivityBeneficiaries as $beneficiary)
                                <tr>
                                    <td class="px-4 py-2 font-medium text-zinc-900 dark:text-white">{{ $beneficiary->full_name }}</td>
                    <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ $beneficiary->receipt_date }}<br>
                        {{ $beneficiary->status->status_name ?? $beneficiary->receive_method }}
                        
                    </td>
                                   
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-zinc-500">{{ __('No beneficiaries found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </flux:modal>
</div>
