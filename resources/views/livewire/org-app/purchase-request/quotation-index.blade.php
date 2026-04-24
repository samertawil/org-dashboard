<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Merchant Responses') }}</flux:heading>
            <flux:subheading>{{ __('Browse and manage all received price quotations.') }}</flux:subheading>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        {{-- Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <flux:input wire:model.live.debounce.300ms="search_pr_number" :placeholder="__('PR Number')" icon="magnifying-glass" />
            <flux:input wire:model.live.debounce.300ms="search_vendor" :placeholder="__('Vendor Name')" icon="magnifying-glass" />
        </div>

        <div class="overflow-x-auto -mx-6">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr  >
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Date Received') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Vendor') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('PR Number') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Total Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Currency') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($quotations as $quote)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 text-sm">{{ $quote->submitted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 text-sm font-bold">{{ $quote->vendor->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('purchase_request.show', $quote->purchase_requisition_id) }}" class="text-blue-600 hover:underline font-bold">
                                    #{{ $quote->purchaseRequisition->request_number }}
                                </a>
                                @if($quote->purchaseRequisition->description)
                                    <div x-data="{ expanded: false }" class="text-xs text-zinc-500 mt-1 max-w-xs">
                                        <div :class="expanded ? '' : 'line-clamp-1'">
                                            {{ $quote->purchaseRequisition->description }}
                                        </div>
                                        @if(strlen($quote->purchaseRequisition->description) > 50)
                                            <button @click="expanded = !expanded" class="text-indigo-600 font-bold hover:underline mt-1 block">
                                                <span x-show="!expanded">{{ __('Read More') }}</span>
                                                <span x-show="expanded">{{ __('Read Less') }}</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-bold">{{ number_format($quote->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm">{{ $quote->currency->status_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm flex items-center gap-2">
                                <flux:button href="{{ route('quotation.show', $quote->id) }}" wire:navigate variant="ghost" size="sm" icon="eye" title="{{ __('View Details') }}" />
                                <flux:button href="{{ route('purchase_request.comparison', $quote->purchase_requisition_id) }}" wire:navigate variant="ghost" size="sm" icon="presentation-chart-bar" color="indigo" title="{{ __('Financial Comparison') }}" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">{{ __('No responses found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $quotations->links() }}
        </div>
    </div>
</div>
