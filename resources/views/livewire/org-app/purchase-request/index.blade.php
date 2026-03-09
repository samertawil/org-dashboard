<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Purchase Requisitions') }}</flux:heading>
            <flux:subheading>{{ __('Manage purchase requisitions.') }}</flux:subheading>
        </div>
        @can('purchase_request.create')
            <flux:button href="{{ route('purchase_request.create') }}" wire:navigate variant="primary" icon="plus">
                {{ __('New Request') }}
            </flux:button>
        @endcan

    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <flux:input wire:model.live.debounce.300ms="search_number" :placeholder="__('Request Number')"
                icon="magnifying-glass" />

            {{-- Year --}}
            {{-- Date input for year or just text --}}
            <flux:input type="number" wire:model.live.debounce.300ms="search_year" :placeholder="__('Year (YYYY)')" />

            {{-- Date --}}
            <flux:input type="date" wire:model.live="search_date" :placeholder="__('Date')" />

            {{-- Status --}}
            <flux:select wire:model.live="search_status_id">
                <option value="">{{ __('All Statuses') }}</option>
                @foreach ($this->statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>

            {{-- Vendor --}}
            <flux:select wire:model.live="search_vendor_id">
                <option value="">{{ __('All Vendors') }}</option>
                @foreach ($this->partners as $partner)
                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="mb-4">
            @if ($search_number || $search_status_id || $search_vendor_id || $search_date || $search_year)
                <div class="mt-4 flex items-center justify-end">
                    <flux:button
                        wire:click="$set('search_number', ''); $set('search_year', ''); $set('search_status_id', ''); $set('search_vendor_id', ''); $set('search_date', '');"
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
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->purchaseRequisitions->firstItem() }}</span>
                        {{ __('to') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->purchaseRequisitions->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->purchaseRequisitions->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th wire:click="sortBy('request_number')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Req #') }}
                                @if ($sortField === 'request_number')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('request_date')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Date') }}
                                @if ($sortField === 'request_date')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('description')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Description') }}
                                @if ($sortField === 'description')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('status_id')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Status') }}
                                @if ($sortField === 'status_id')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->purchaseRequisitions as $pr)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 text-sm font-medium">{{ $pr->request_number }}</td>
                            <td class="px-6 py-4 text-sm">
                                {{ $pr->request_date ? $pr->request_date->format('Y-m-d') : '-' }}</td>
                            <td class="px-6 py-4 text-sm truncate max-w-xs">
                                {{ \Illuminate\Support\Str::limit($pr->description ?? '-', 50) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <flux:badge size="sm">{{ $pr->status->status_name ?? '-' }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">

                                <div class="flex items-center justify-end gap-2">
                                    @php
                                        $attachmentCount = count($pr->attachments ?? []);
                                    @endphp

                                    <div class="relative">
                                        <flux:button href="{{ route('purchase_request.gallery', $pr->id) }}"
                                            wire:navigate icon="paper-clip" variant="ghost" size="sm"
                                            tooltip="{{ __('Attachments') }}"
                                            style="{{ $attachmentCount > 0 ? 'color: #3b82f6 !important;' : '' }}">
                                        </flux:button>
                                        @if ($attachmentCount > 0)
                                            <span
                                                class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-blue-500 ring-1 ring-white dark:ring-zinc-900"></span>
                                        @endif
                                    </div>
                                    @can('purchase_request.create')
                                        <flux:button href="{{ route('purchase_request.edit', $pr->id) }}" wire:navigate
                                            variant="ghost" size="sm" icon="pencil-square" />
                                        <flux:button wire:click="delete({{ $pr->id }})"
                                            wire:confirm="{{ __('Are you sure?') }}" variant="ghost" size="sm"
                                            icon="trash" class="text-red-500" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No purchase requisitions found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->purchaseRequisitions->links() }}
        </div>
    </div>
</div>
