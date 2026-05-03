<div class="flex flex-col gap-6 p-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <span title="{{ __('Back to merchant responses') }}">
                <flux:button href="{{ route('quotation.index') }}" wire:navigate variant="ghost" icon="arrow-right" />
            </span>
            <div>
                <flux:heading level="1" size="xl">{{ __('Quotation Details') }}</flux:heading>
                <flux:subheading>استلام من: {{ $quotation->vendor->name }}</flux:subheading>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
            <span title="{{ __('Download or print quotation as PDF') }}" class="w-full sm:w-auto">
                <flux:button wire:click="downloadPdf" icon="printer" variant="filled" color="indigo" size="sm" class="w-full">
                    {{ __('Print PDF') }}
                </flux:button>
            </span>
            <flux:badge color="indigo" class="justify-center">{{ __('Submitted At') }}: {{ $quotation->submitted_at->format('Y-m-d H:i') }}</flux:badge>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
                    <h3 class="font-bold text-zinc-800 dark:text-zinc-200">{{ __('Items & Prices') }}</h3>
                </div>

                {{-- Mobile Items View --}}
                <div class="block md:hidden divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach($quotation->prices as $price)
                        <div class="p-4 space-y-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $price->requisitionItem->item_name }}</span>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400 block">{{ number_format($price->offered_price, 2) }}</span>
                                    <span class="text-[10px] text-zinc-500">{{ __('Unit Price') }}</span>
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-zinc-500">
                                <span>{{ __('Quantity') }}: {{ $price->requisitionItem->quantity }}</span>
                            </div>
                            @if($price->vendor_item_notes)
                                <div class="text-[11px] text-zinc-500 italic p-2 bg-zinc-50 dark:bg-zinc-900/50 rounded border border-zinc-100 dark:border-zinc-800 mt-1">
                                    {{ $price->vendor_item_notes }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                    <div class="p-4 bg-indigo-50/50 dark:bg-indigo-900/20">
                        <div class="flex justify-between items-center font-bold">
                            <span class="text-sm">{{ __('Total Amount') }}</span>
                            <span class="text-lg text-indigo-700 dark:text-indigo-300">
                                {{ number_format($quotation->total_amount, 2) }} {{ $quotation->currency->status_name ?? '' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Desktop Table View --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="text-xs text-zinc-500 uppercase border-b border-zinc-100 dark:border-zinc-700">
                                <th class="px-6 py-4 font-semibold">{{ __('Item Name') }}</th>
                                <th class="px-6 py-4 text-center font-semibold">{{ __('Qty') }}</th>
                                <th class="px-6 py-4 text-center font-semibold">{{ __('Offered Price') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ __('Vendor Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            @foreach($quotation->prices as $price)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $price->requisitionItem->item_name }}</td>
                                    <td class="px-6 py-4 text-sm text-center">{{ $price->requisitionItem->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-center font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ number_format($price->offered_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-zinc-500 italic">
                                        @if($price->vendor_item_notes)
                                            <div x-data="{ expanded: false }">
                                                <div :class="expanded ? '' : 'line-clamp-1'" class="max-w-xs">
                                                    {{ $price->vendor_item_notes }}
                                                </div>
                                                @if(strlen($price->vendor_item_notes) > 50)
                                                    <button @click="expanded = !expanded" class="text-indigo-600 font-bold hover:underline mt-1 block">
                                                        <span x-show="!expanded">{{ __('Read More') }}</span>
                                                        <span x-show="expanded">{{ __('Read Less') }}</span>
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-indigo-50/50 dark:bg-indigo-900/20 font-bold">
                                <td colspan="2" class="px-6 py-4 text-left">{{ __('Total Amount') }}</td>
                                <td class="px-6 py-4 text-center text-lg text-indigo-700 dark:text-indigo-300">
                                    {{ number_format($quotation->total_amount, 2) }} {{ $quotation->currency->status_name ?? '' }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($quotation->notes)
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 shadow-sm">
                    <h3 class="font-bold mb-4 text-zinc-800 dark:text-zinc-200">{{ __('General Notes from Vendor') }}</h3>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg">
                        <div x-data="{ expanded: false }">
                            <div :class="expanded ? '' : 'line-clamp-3'">
                                {{ $quotation->notes }}
                            </div>
                            @if(strlen($quotation->notes) > 150)
                                <button @click="expanded = !expanded" class="text-indigo-600 font-bold hover:underline mt-2 block">
                                    <span x-show="!expanded">{{ __('Read More') }}</span>
                                    <span x-show="expanded">{{ __('Read Less') }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 shadow-sm">
                <h3 class="font-bold mb-6 text-zinc-800 dark:text-zinc-200">{{ __('Related PR') }}</h3>
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500">{{ __('PR Number') }}</span>
                        <a href="{{ route('purchase_request.show', $quotation->purchase_requisition_id) }}" class="font-bold text-blue-600 hover:underline">
                            #{{ $quotation->purchaseRequisition->request_number }}
                        </a>
                    </div>
                    <div class="flex justify-between items-center text-sm border-t border-zinc-100 dark:border-zinc-700 pt-4">
                        <span class="text-zinc-500">{{ __('PR Date') }}</span>
                        <span class="font-medium">{{ $quotation->purchaseRequisition->request_date->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 shadow-sm">
                <h3 class="font-bold mb-6 text-zinc-800 dark:text-zinc-200">{{ __('Attachments') }}</h3>
                @if($quotation->attachments && count($quotation->attachments) > 0)
                    <div class="space-y-3">
                        @foreach($quotation->attachments as $path)
                            <a href="{{ Storage::url($path) }}" target="_blank" class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg border border-zinc-100 dark:border-zinc-700 hover:border-indigo-300 transition-colors">
                                <flux:icon name="document" class="size-5 text-indigo-500" />
                                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300 truncate">{{ basename($path) }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-zinc-400 italic text-xs">
                        {{ __('No files uploaded') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
