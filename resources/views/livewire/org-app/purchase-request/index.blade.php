<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Purchase Requisitions') }}</flux:heading>
            <flux:subheading>{{ __('Manage purchase requisitions.') }}</flux:subheading>
        </div>

        <flux:button href="{{ route('purchase_request.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('New Request') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <flux:input wire:model.live.debounce.300ms="search_number" :placeholder="__('Request Number')" icon="magnifying-glass" />
             
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

        <div class="overflow-x-auto -mx-6">
             <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">{{ __('Req #') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">{{ __('Date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">{{ __('Description') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->purchaseRequisitions as $pr)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 text-sm font-medium">{{ $pr->request_number }}</td>
                            <td class="px-6 py-4 text-sm">{{ $pr->request_date ? $pr->request_date->format('Y-m-d') : '-' }}</td>
                            <td class="px-6 py-4 text-sm truncate max-w-xs">{{ \Illuminate\Support\Str::limit($pr->description, 50) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <flux:badge size="sm">{{ $pr->status->status_name ?? '-' }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                     <flux:button href="{{ route('purchase_request.edit', $pr->id) }}" wire:navigate variant="ghost" size="sm" icon="pencil-square" />
                                     <flux:button wire:click="delete({{ $pr->id }})" wire:confirm="{{ __('Are you sure?') }}" variant="ghost" size="sm" icon="trash" class="text-red-500" />
                                </div>
                            </td>
                        </tr>
                    @empty
                         <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-zinc-500">{{ __('No purchase requisitions found.') }}</td>
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
