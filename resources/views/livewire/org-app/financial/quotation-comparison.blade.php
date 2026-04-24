<div class="p-8 space-y-8 min-h-screen bg-zinc-50 dark:bg-zinc-900/50">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('quotation.index') }}" wire:navigate variant="ghost" icon="arrow-right" class="rounded-full" />
            <div>
                <flux:heading level="1" size="xl">{{ __('Financial Comparison Dashboard') }}</flux:heading>
                <flux:subheading>مراجعة العروض وترسية العطاء لطلب الشراء #{{ $pr->request_number }}</flux:subheading>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <flux:button wire:click="exportExcel" icon="table-cells" variant="filled" color="green" size="sm">
                تصدير Excel (Pivot)
            </flux:button>
            @if($acceptedQuotation)
                <flux:badge color="green" size="lg" icon="check-circle" class="px-4 py-2 text-sm font-bold">تمت الترسية على: {{ $acceptedQuotation->vendor->name }}</flux:badge>
            @else
                <flux:badge color="yellow" size="lg" icon="clock" class="px-4 py-2 text-sm font-bold">قيد الدراسة والترسية</flux:badge>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-2">{{ __('Total Items') }}</div>
            <div class="text-3xl font-black text-zinc-900 dark:text-white">{{ $pr->items->count() }}</div>
        </div>
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-2">{{ __('Received Offers') }}</div>
            <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $quotations->count() }}</div>
        </div>
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-2">سعر صرف الدولار المعتمد</div>
            <div class="text-3xl font-black text-zinc-900 dark:text-white">₪{{ number_format($exchangeRate, 3) }}</div>
            <div class="text-[10px] text-zinc-400">حسب آخر تحديث في النظام</div>
        </div>
        <div class="bg-white dark:bg-zinc-800 p-6 rounded-2xl border border-indigo-200 dark:border-indigo-900 shadow-sm bg-indigo-50/10">
            <div class="text-indigo-600 dark:text-indigo-400 text-xs font-bold uppercase tracking-wider mb-2">أقل عرض سعر مقدم (بالشيكل)</div>
            @php 
                $minTotalNis = $quotations->map(function($q) use ($exchangeRate) {
                    return $q->currency_id == 170 ? $q->total_amount * $exchangeRate : $q->total_amount;
                })->min(); 
            @endphp
            <div class="text-3xl font-black text-green-600">{{ $minTotalNis ? number_format($minTotalNis, 2) : '-' }} <span class="text-sm font-normal">₪</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        {{-- Comparison Matrix --}}
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-xl overflow-hidden">
                <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 flex items-center justify-between">
                    <h3 class="font-black text-zinc-800 dark:text-zinc-200 flex items-center gap-2">
                        <flux:icon name="table-cells" variant="solid" class="size-5 text-indigo-500" />
                        مصفوفة مقارنة الأسعار التفصيلية (التحويل العادل للشيكل ₪)
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right">
                        <thead>
                            <tr class="bg-zinc-50 dark:bg-zinc-900/80">
                                <th class="px-6 py-4 font-bold text-zinc-500 border-l border-zinc-100 dark:border-zinc-700 w-64">الصنف والكمية المطلوبة</th>
                                @foreach($quotations as $quote)
                                    <th class="px-6 py-4 text-center border-l border-zinc-100 dark:border-zinc-700 min-w-[150px] {{ $quote->status_id == 1 ? 'bg-green-50/50 dark:bg-green-900/20' : '' }}">
                                        <div class="flex flex-col items-center">
                                            <span class="font-black text-zinc-900 dark:text-white">{{ $quote->vendor->name }}</span>
                                            <span class="text-[10px] text-zinc-500">العملة: {{ $quote->currency->status_name ?? '' }}</span>
                                            @if($quote->status_id == 1)
                                                <flux:badge color="green" size="sm" class="mt-1">الفائز</flux:badge>
                                            @endif
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            @foreach($pr->items as $item)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="px-6 py-4 border-l border-zinc-100 dark:border-zinc-700">
                                        <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $item->item_name }}</div>
                                        <div class="text-[10px] text-zinc-500 font-medium">الكمية المطلوبة: <span class="text-indigo-600">{{ $item->quantity }} {{ $item->unit->status_name ?? '' }}</span></div>
                                    </td>
                                    
                                    @php
                                        // حساب أقل سعر محول لهذا الصنف
                                        $fairPrices = $quotations->map(function($q) use ($item, $exchangeRate) {
                                            $p = $q->prices->where('purchase_requisition_item_id', $item->id)->first()?->offered_price;
                                            return $q->currency_id == 170 ? $p * $exchangeRate : $p;
                                        })->filter();
                                        $minFairPrice = $fairPrices->min();
                                    @endphp

                                    @foreach($quotations as $quote)
                                        @php
                                            $originalPrice = $quote->prices->where('purchase_requisition_item_id', $item->id)->first()?->offered_price;
                                            $fairPrice = $quote->currency_id == 170 ? $originalPrice * $exchangeRate : $originalPrice;
                                            $isMin = $fairPrice && $fairPrice == $minFairPrice;
                                        @endphp
                                        <td class="px-6 py-4 text-center border-l border-zinc-100 dark:border-zinc-700 {{ $isMin ? 'bg-green-50/30 dark:bg-green-900/10' : '' }}">
                                            @if($originalPrice)
                                                <div class="font-mono text-base font-bold {{ $isMin ? 'text-green-600' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                    {{ number_format($originalPrice, 2) }}
                                                </div>
                                                @if($quote->currency_id == 170)
                                                    <div class="text-[10px] text-indigo-500 font-bold">≈ ₪{{ number_format($fairPrice, 2) }}</div>
                                                @endif
                                                @if($isMin)
                                                    <span class="text-[9px] font-black text-green-500 uppercase tracking-tighter">الأفضل سعراً</span>
                                                @endif
                                            @else
                                                <span class="text-zinc-300 italic">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach

                            {{-- Totals --}}
                            <tr class="bg-zinc-50 dark:bg-zinc-900/50 font-black">
                                <td class="px-6 py-6 text-indigo-600 dark:text-indigo-400 border-l border-zinc-100 dark:border-zinc-700">إجمالي العرض (المحول للشيكل)</td>
                                @foreach($quotations as $quote)
                                    @php 
                                        $totalFair = $quote->currency_id == 170 ? $quote->total_amount * $exchangeRate : $quote->total_amount;
                                    @endphp
                                    <td class="px-6 py-6 text-center border-l border-zinc-100 dark:border-zinc-700 {{ $quote->status_id == 1 ? 'bg-green-50/50' : '' }}">
                                        <div class="text-sm text-zinc-500 font-normal mb-1">الأصلي: {{ number_format($quote->total_amount, 2) }} {{ $quote->currency->status_name ?? '' }}</div>
                                        <div class="text-xl {{ $totalFair == $minTotalNis ? 'text-green-600' : '' }}">
                                            ₪{{ number_format($totalFair, 2) }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Awarding Actions --}}
                            <tr>
                                <td class="px-6 py-4 border-l border-zinc-100 dark:border-zinc-700"></td>
                                @foreach($quotations as $quote)
                                    <td class="px-6 py-6 text-center border-l border-zinc-100 dark:border-zinc-700">
                                        @if($quote->status_id == 1)
                                            <div class="flex flex-col items-center gap-1 text-green-600">
                                                <flux:icon name="check-badge" variant="solid" class="size-8" />
                                                <span class="text-xs font-black uppercase">تمت الترسية</span>
                                            </div>
                                        @else
                                            <flux:button wire:click="acceptQuotation({{ $quote->id }})" wire:confirm="هل أنت متأكد من ترسية العرض على هذا المورد؟ سيتم إلغاء أي ترسية سابقة." variant="primary" size="sm" class="w-full">
                                                ترسية العرض
                                            </flux:button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar Summary & Winner Card --}}
        <div class="space-y-6">
            {{-- Winner Card --}}
            @if($acceptedQuotation)
                <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 opacity-10">
                        <flux:icon name="trophy" variant="solid" class="size-32" />
                    </div>
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <flux:icon name="star" variant="solid" class="size-5 text-yellow-300" />
                        المورد المعتمد للتوريد
                    </h3>
                    <div class="space-y-4 relative z-10">
                        <div class="flex justify-between items-end border-b border-white/20 pb-4">
                            <div>
                                <div class="text-indigo-200 text-xs mb-1 uppercase font-bold">اسم الشركة</div>
                                <div class="text-xl font-black">{{ $acceptedQuotation->vendor->name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-indigo-200 text-xs mb-1 uppercase font-bold">قيمة العقد</div>
                                <div class="text-2xl font-black">{{ number_format($acceptedQuotation->total_amount, 2) }}</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-indigo-200 text-[10px] uppercase font-bold">تاريخ الترسية</div>
                                <div class="font-medium text-sm">{{ now()->format('Y-m-d') }}</div>
                            </div>
                            <div>
                                <div class="text-indigo-200 text-[10px] uppercase font-bold">رقم التواصل</div>
                                <div class="font-medium text-sm">{{ $acceptedQuotation->vendor->phone ?? '-' }}</div>
                            </div>
                        </div>

                        <flux:button href="{{ route('quotation.show', $acceptedQuotation->id) }}" variant="filled" class="w-full bg-white text-indigo-600 hover:bg-indigo-50 border-none font-bold">
                            عرض التفاصيل والملفات
                        </flux:button>
                    </div>
                </div>
            @endif

            {{-- PR Context Card --}}
            <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-6 shadow-sm">
                <h3 class="font-black mb-6 text-zinc-800 dark:text-zinc-200">بيانات طلب الشراء الأصلية</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500 font-bold">تاريخ الطلب</span>
                        <span class="font-medium">{{ $pr->request_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-t border-zinc-50 dark:border-zinc-700/50 pt-4">
                        <span class="text-zinc-500 font-bold">الميزانية التقديرية</span>
                        <span class="font-black">₪{{ number_format($pr->estimated_total_nis, 2) }}</span>
                    </div>
                    <div class="space-y-2 border-t border-zinc-50 dark:border-zinc-700/50 pt-4">
                        <span class="text-zinc-500 text-xs font-bold uppercase tracking-wider block">وصف الطلب</span>
                        <div class="text-xs leading-relaxed text-zinc-600 dark:text-zinc-400 italic bg-zinc-50 dark:bg-zinc-900/50 p-3 rounded-lg">
                            {{ $pr->description ?? 'لا يوجد وصف متاح' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Decision Notes --}}
            <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-6 shadow-sm border-l-4 border-l-indigo-500">
                <h3 class="font-black mb-4 text-zinc-800 dark:text-zinc-200 flex items-center gap-2 text-sm uppercase">
                    <flux:icon name="information-circle" class="size-4 text-indigo-500" />
                    تعليمات للمدير المالي
                </h3>
                <ul class="text-xs text-zinc-500 space-y-3 list-disc pr-4 font-medium">
                    <li>يتم تلوين <span class="text-green-600 font-bold">أقل سعر</span> لكل صنف تلقائياً لتسهيل المقارنة.</li>
                    <li>عند الضغط على "ترسية العرض"، سيتم تغيير حالة طلب الشراء وتحديث السعر النهائي بناءً على العرض المختار.</li>
                    <li>يمكنك مراجعة المرفقات والملاحظات الخاصة بكل تاجر من خلال صفحة التفاصيل.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
