<div class="flex flex-col gap-6 p-4 md:p-8 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
    <div class="max-w-4xl mx-auto w-full bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-xl overflow-hidden print:border-none print:shadow-none">
        {{-- Header Section --}}
        <div class="bg-zinc-900 text-white p-8 flex justify-between items-start print:bg-white print:text-black print:p-4 print:border-b-2 print:border-zinc-300">
            <div>
                <flux:heading level="1" size="xl" class="text-white print:text-black">
                    {{ __('Purchase Requisition') }}
                </flux:heading>
                <div class="mt-2 flex gap-4 text-zinc-400 print:text-black font-mono">
                    <span>#{{ $purchaseRequisition->request_number }}</span>
                    <span>/</span>
                    <span>{{ $purchaseRequisition->request_date ? $purchaseRequisition->request_date->format('Y-md') : '-' }}</span>
                </div>
            </div>
            <div class="flex gap-3 print:hidden">
                <flux:button href="{{ route('purchase_request.index') }}" wire:navigate icon="arrow-left" variant="ghost" class="text-white hover:text-zinc-300">
                    {{ __('Back') }}
                </flux:button>
                <flux:button onclick="window.print()" icon="printer" variant="primary">
                    {{ __('Print PDF') }}
                </flux:button>
            </div>
        </div>

        <div class="p-8 space-y-8 print:p-4">
            {{-- Status & Summary --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 py-6 border-b border-zinc-100 dark:border-zinc-700 print:grid-cols-4 print:gap-4 print:py-4">
                <div>
                    <flux:label>{{ __('Status') }}</flux:label>
                    <div class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">
                        <flux:badge>{{ $purchaseRequisition->status->status_name ?? '-' }}</flux:badge>
                    </div>
                </div>
                <div>
                    <flux:label>{{ __('Request Date') }}</flux:label>
                    <div class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100 italic">
                        {{ $purchaseRequisition->request_date ? $purchaseRequisition->request_date->format('Y-m-d') : '-' }}
                    </div>
                </div>
                <div>
                    <flux:label>{{ __('Need By Date') }}</flux:label>
                    <div class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $purchaseRequisition->need_by_date ? $purchaseRequisition->need_by_date->format('Y-m-d') : '-' }}
                    </div>
                </div>
                <div>
                    <flux:label>{{ __('Created By') }}</flux:label>
                    <div class="mt-1 font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $purchaseRequisition->creator->name ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- Financial & Vendors --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 print:grid-cols-2">
                <div class="space-y-4">
                    <flux:heading level="3" size="md">{{ __('Financial Totals') }}</flux:heading>
                    <div class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-lg space-y-3">
                        <div class="flex justify-between items-center text-lg">
                            <span class="text-zinc-500">{{ __('Amount in Dollars') }}:</span>
                            <span class="font-bold text-zinc-900 dark:text-white">${{ number_format($purchaseRequisition->estimated_total_dollar, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-lg border-t border-zinc-200 dark:border-zinc-700 pt-3">
                            <span class="text-zinc-500">{{ __('Amount in NIS') }}:</span>
                            <span class="font-bold text-zinc-900 dark:text-white">₪{{ number_format($purchaseRequisition->estimated_total_nis, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <flux:heading level="3" size="md">{{ __('Suggested Vendors') }}</flux:heading>
                    <div class="flex flex-wrap gap-2">
                        @forelse($purchaseRequisition->suggestedVendors as $vendor)
                             <div class="px-3 py-1 bg-zinc-100 dark:bg-zinc-700/50 rounded-full text-sm border border-zinc-200 dark:border-zinc-600">
                                {{ $vendor->name }}
                             </div>
                        @empty
                            <span class="text-zinc-400 italic font-mono">{{ __('None listed') }}</span>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Descriptions --}}
            <div class="space-y-6">
                <div>
                    <flux:heading level="3" size="md">{{ __('General Description') }}</flux:heading>
                    <div class="mt-2 text-zinc-600 dark:text-zinc-400 leading-relaxed whitespace-pre-wrap">
                        {{ $purchaseRequisition->description ?? '-' }}
                    </div>
                </div>

                @if($purchaseRequisition->justification)
                <div>
                    <flux:heading level="3" size="md">{{ __('Business Justification') }}</flux:heading>
                    <div class="mt-2 p-4 bg-zinc-50 dark:bg-zinc-900/50 border-l-4 border-zinc-500 italic text-zinc-600 dark:text-zinc-400">
                        {{ $purchaseRequisition->justification }}
                    </div>
                </div>
                @endif
            </div>

            {{-- Items Table --}}
            <div class="space-y-4">
                <flux:heading level="3" size="md" class="flex items-center gap-2">
                    {{ __('Items Breakdown') }}
                    <span class="text-sm font-normal text-zinc-400">({{ $purchaseRequisition->items->count() }} {{ __('Items') }})</span>
                </flux:heading>
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Item Details') }}</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Qty') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Unit') }}</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Price') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-transparent tracking-tighter">
                            @foreach($purchaseRequisition->items as $item)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/20">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100 font-mono">{{ $item->item_name }}</div>
                                        @if($item->item_description)
                                            <div class="text-sm text-zinc-500">{{ $item->item_description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-semibold text-zinc-900 dark:text-zinc-100">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $item->unit->status_name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-zinc-900 dark:text-zinc-100">
                                        {{ number_format($item->unit_price, 2) }}
                                        <span class="text-[10px] text-zinc-400">{{ $item->currency }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer / Signatures --}}
            <div class="grid grid-cols-2 gap-12 mt-16 pb-8 print:block print:space-y-12">
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 text-center">
                    <p class="text-xs text-zinc-400 uppercase mb-8">{{ __('Requested By') }}</p>
                    <div class="h-8"></div>
                    <p class="font-medium font-mono">{{ $purchaseRequisition->creator->name ?? '____________________' }}</p>
                </div>
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 text-center">
                    <p class="text-xs text-zinc-400 uppercase mb-8">{{ __('Approval Authority') }}</p>
                    <div class="h-8"></div>
                    <p class="font-medium">____________________</p>
                </div>
            </div>
        </div>
    </div>
</div>

