<div class="flex flex-col gap-6" x-on:modal-show.window="Flux.modal($event.detail.name).show()">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Purchase Requisitions') }}</flux:heading>
            <flux:subheading>{{ __('Manage purchase requisitions.') }}</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:button wire:click="export" icon="document-arrow-down" variant="outline">
                {{ __('Export Excel') }}
            </flux:button>
            @can('purchase_request.create')
                <flux:button href="{{ route('purchase_request.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('New Request') }}
                </flux:button>
            @endcan
        </div>

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
                        <th class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Suggested Vendors') }}
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
                                <div class="flex flex-wrap gap-1">
                                    @foreach($pr->suggested_vendors as $vendor)
                                        <flux:badge size="sm" variant="ghost">{{ $vendor->name }}</flux:badge>
                                    @endforeach
                                    @if($pr->suggested_vendors->isEmpty())
                                        -
                                    @endif
                                </div>
                            </td>
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
                                        <flux:button wire:click="showDetails({{ $pr->id }})" variant="ghost" size="sm" icon="eye" tooltip="{{ __('View Details') }}" />
                                        <flux:button href="{{ route('purchase_request.edit', $pr->id) }}" wire:navigate
                                            variant="ghost" size="sm" icon="pencil-square" tooltip="{{ __('Edit') }}" />
                                        <flux:button wire:click="delete({{ $pr->id }})"
                                            wire:confirm="{{ __('Are you sure?') }}" variant="ghost" size="sm"
                                            icon="trash" class="text-red-500" tooltip="{{ __('Delete') }}" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">
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

    {{-- Details Modal --}}
    <flux:modal name="show-pr-modal" class="md:w-[800px]">
        <div class="flex flex-col gap-6">
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
                    <div class="flex flex-wrap gap-2">
                        @foreach($selectedPr->suggested_vendors as $vendor)
                            <div class="flex items-center gap-2 px-3 py-1 bg-zinc-100 dark:bg-zinc-700/50 rounded-lg border border-zinc-200 dark:border-zinc-600">
                                <span class="text-sm">{{ $vendor->name }}</span>
                                
                                @if($vendor->phone)
                                    @php
                                        $prLink = route('quotation.public', ['token' => $selectedPr->token, 'vendor_id' => $vendor->id]);
                                        $vendorPin = $selectedPr->calculateVendorPin($vendor->id);
                                        $appName = config('app.name');
                                        $message = "عزيزي {$vendor->name}، تود مؤسسة {$appName} الحصول على عرض سعر لـ " . ($selectedPr->description ?? 'طلب شراء') . ". \n\nرابط إدخال العرض: {$prLink} \nكود التحقق الخاص بكم: {$vendorPin}";
                                        $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $vendor->phone) . "?text=" . urlencode($message);
                                    @endphp
                                    <a href="{{ $waUrl }}" target="_blank" class="text-green-600 hover:text-green-700 transition-colors" title="{{ __('Send via WhatsApp') }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.149-.174.198-.298.297-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
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

                <div class="space-y-4">
                    <flux:heading level="3" size="md" class="flex items-center gap-2">
                        {{ __('Items Breakdown') }}
                        <span class="text-sm font-normal text-zinc-400">({{ $selectedPr->items->count() }} {{ __('Items') }})</span>
                    </flux:heading>
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

                {{-- Comparison Matrix --}}
                @if($selectedPr->quotations->count() > 0)
                    <div class="space-y-4 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                        <div class="flex justify-between items-center">
                            <flux:heading level="3" size="md" class="text-indigo-600 dark:text-indigo-400">
                                {{ __('Quotations Comparison Matrix') }}
                            </flux:heading>
                            <flux:badge color="green" size="sm">{{ $selectedPr->quotations->count() }} {{ __('Offers Received') }}</flux:badge>
                        </div>

                        <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
                            <table class="w-full text-sm text-right">
                                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                                    <tr>
                                        <th class="px-4 py-3 font-bold text-zinc-700 dark:text-zinc-300 w-48">{{ __('Item Name') }}</th>
                                        @foreach($selectedPr->quotations as $quote)
                                            <th class="px-4 py-3 text-center border-r border-zinc-200 dark:border-zinc-700">
                                                <div class="flex flex-col items-center">
                                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $quote->vendor->name }}</span>
                                                    <span class="text-[10px] text-zinc-500">{{ $quote->submitted_at->format('Y-m-d H:i') }}</span>
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @foreach($selectedPr->items as $item)
                                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100 bg-zinc-50/30 dark:bg-zinc-900/30">
                                                {{ $item->item_name }}
                                                <div class="text-[10px] text-zinc-500 font-normal">الكمية: {{ $item->quantity }}</div>
                                            </td>
                                            
                                            @php
                                                // Find min price for this item among all quotations
                                                $pricesForThisItem = $selectedPr->quotations->map(function($q) use ($item) {
                                                    return $q->prices->where('purchase_requisition_item_id', $item->id)->first()?->offered_price;
                                                })->filter()->toArray();
                                                $minPrice = count($pricesForThisItem) > 0 ? min($pricesForThisItem) : null;
                                            @endphp

                                            @foreach($selectedPr->quotations as $quote)
                                                @php
                                                    $offeredPrice = $quote->prices->where('purchase_requisition_item_id', $item->id)->first()?->offered_price;
                                                    $isMin = $minPrice && $offeredPrice == $minPrice;
                                                @endphp
                                                <td class="px-4 py-3 text-center border-r border-zinc-200 dark:border-zinc-700 {{ $isMin ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                                    @if($offeredPrice)
                                                        <span class="font-mono font-bold {{ $isMin ? 'text-green-700 dark:text-green-400' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                            {{ number_format($offeredPrice, 2) }}
                                                        </span>
                                                        <span class="text-[10px] text-zinc-400">{{ $quote->currency->status_name ?? '' }}</span>
                                                        
                                                        @if($isMin)
                                                            <div class="text-[9px] text-green-600 font-bold uppercase">{{ __('Lowest') }}</div>
                                                        @endif
                                                    @else
                                                        <span class="text-zinc-300 italic text-xs">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    {{-- Total Row --}}
                                    <tr class="bg-zinc-100/50 dark:bg-zinc-900/50 font-bold">
                                        <td class="px-4 py-4 text-left">{{ __('Total Offer Amount') }}</td>
                                        @foreach($selectedPr->quotations as $quote)
                                            <td class="px-4 py-4 text-center border-r border-zinc-200 dark:border-zinc-700">
                                                <div class="text-lg text-indigo-600 dark:text-indigo-400">
                                                    {{ number_format($quote->total_amount, 2) }}
                                                    <span class="text-xs">{{ $quote->currency->status_name ?? '' }}</span>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    {{-- Actions Row --}}
                                    <tr class="bg-white dark:bg-zinc-800">
                                        <td class="px-4 py-4"></td>
                                        @foreach($selectedPr->quotations as $quote)
                                            <td class="px-4 py-4 text-center border-r border-zinc-200 dark:border-zinc-700">
                                                <div class="flex flex-col gap-2 items-center">
                                                    @if($quote->attachments)
                                                        <a href="{{ Storage::url($quote->attachments[0] ?? '') }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                                            <flux:icon name="paper-clip" class="size-3" />
                                                            عرض الملف
                                                        </a>
                                                    @endif
                                                    <flux:button wire:click="acceptQuotation({{ $quote->id }})" wire:confirm="{{ __('Are you sure you want to accept this quotation?') }}" size="sm" variant="primary" class="w-full">
                                                        {{ __('Accept') }}
                                                    </flux:button>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </flux:modal>
</div>
