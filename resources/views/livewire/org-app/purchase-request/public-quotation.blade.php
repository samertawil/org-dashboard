<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 font-sans" dir="rtl">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8 text-center">
            <div class="flex justify-center mb-6">
                <!-- المؤسسة Logo placeholder -->
                <div class="w-20 h-20 bg-indigo-600 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-indigo-100">
                    Org
                </div>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">تقديم عرض سعر تجاري</h1>
            <p class="text-lg text-gray-600">طلب شراء رقم: <span class="font-bold text-indigo-600">#{{ $pr->request_number }}</span></p>
            <div class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-full text-sm font-medium">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                مرحباً بك: {{ $vendor->name }}
            </div>
        </div>

    @if($is_submitted)
        <div class="max-w-2xl mx-auto py-20 px-6 text-center">
            <div class="mb-8 inline-flex items-center justify-center size-24 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600">
                <flux:icon name="check-circle" variant="solid" class="size-16" />
            </div>
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-4">شكراً لكم!</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mb-8">تم استلام عرض السعر الخاص بكم بنجاح. سيتم مراجعة العرض من قبل القسم المختص والرد عليكم في أقرب وقت.</p>
           
        </div>
    @elseif($show_history)
        <div class="max-w-3xl mx-auto py-12 px-6">
            <div class="bg-white dark:bg-zinc-800 rounded-3xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                @if(!$is_history_verified)
                    <div class="bg-indigo-600 p-8 text-white text-center">
                        <div class="mb-4 inline-flex items-center justify-center size-16 rounded-full bg-indigo-500">
                            <flux:icon name="lock-closed" variant="solid" class="size-8" />
                        </div>
                        <h2 class="text-2xl font-bold mb-2">تحقق الأمان</h2>
                        <p class="text-indigo-100 opacity-90">لرؤية عروض أسعاركم السابقة، يرجى إدخال كود الـ PIN الخاص بكم.</p>
                    </div>

                    <div class="p-8 max-w-sm mx-auto">
                        <div class="space-y-6">
                            <flux:input wire:model="history_pin" type="text" maxlength="4" placeholder="0 0 0 0" class="text-center text-2xl tracking-[1em]" label="كود الـ PIN" />
                            
                            @error('history_pin')
                                <p class="text-red-500 text-sm text-center mt-2">{{ $message }}</p>
                            @enderror

                            <div class="flex flex-col gap-3">
                                <flux:button wire:click="verifyHistoryPin" variant="primary" class="w-full">عرض السجل</flux:button>
                                <flux:button wire:click="startNewOffer" variant="ghost" class="w-full">بدء عرض جديد دون رؤية السجل</flux:button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-indigo-600 p-8 text-white">
                        <h2 class="text-2xl font-bold mb-2">سجل عروض الأسعار الخاصة بكم</h2>
                        <p class="text-indigo-100 opacity-90">يمكنكم الاطلاع على تفاصيل عروضكم السابقة أو تقديم عرض جديد لهذا الطلب.</p>
                    </div>
                    
                    <div class="p-8">
                        <div class="space-y-4 mb-8">
                            @foreach($previousOffers as $offer)
                                <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border border-zinc-100 dark:border-zinc-700">
                                    <div class="flex items-center gap-4">
                                        <div class="size-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600">
                                            <flux:icon name="calendar" class="size-5" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-zinc-900 dark:text-white">تاريخ العرض: {{ $offer->submitted_at->format('Y-m-d H:i') }}</div>
                                            <div class="text-xs text-zinc-500">الإجمالي: {{ number_format($offer->total_amount, 2) }} {{ $offer->currency->status_name ?? '' }}</div>
                                        </div>
                                    </div>
                                    <flux:button wire:click="downloadOffer({{ $offer->id }})" variant="ghost" icon="arrow-down-tray" size="sm">تحميل PDF</flux:button>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <flux:button wire:click="startNewOffer" variant="primary" class="flex-1" icon="plus">بدء تعبئة عرض جديد</flux:button>
                            <flux:button wire:click="$set('show_history', false)" variant="ghost" class="flex-1">إغلاق</flux:button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @elseif($is_expired)
            <div class="bg-red-50 border-l-4 border-red-400 p-8 rounded-2xl shadow-sm text-center">
                <div class="flex justify-center mb-4">
                    <div class="rounded-full bg-red-100 p-3">
                        <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-red-800 mb-2">عذراً، انتهى الموعد المحدد!</h2>
                <p class="text-red-700">لقد انتهى الموعد النهائي لتقديم عروض الأسعار لهذا الطلب في تاريخ ({{ $pr->quotation_deadline->format('Y-m-d') }}). يرجى التواصل مع الإدارة للمزيد من التفاصيل.</p>
            </div>
        @else
            <form wire:submit.prevent="submit" class="space-y-8">
                <!-- Items Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-xl font-bold text-gray-800">تفاصيل الأصناف المطلوبة</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الصنف</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الوصف</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">الكمية</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">سعر الوحدة</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">ملاحظاتك</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($pr->items as $item)
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ $item->item_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($item->item_description)
                                                <div x-data="{ expanded: false }" class="text-xs text-zinc-500">
                                                    <div :class="expanded ? '' : 'line-clamp-1'" class="max-w-xs">
                                                        {{ $item->item_description }}
                                                    </div>
                                                    @if(strlen($item->item_description) > 50)
                                                        <button @click="expanded = !expanded" type="button" class="text-indigo-600 hover:text-indigo-700 font-bold mt-1 text-[10px] uppercase tracking-wider">
                                                            <span x-show="!expanded">{{ __('Read More') }}</span>
                                                            <span x-show="expanded">{{ __('Read Less') }}</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-zinc-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $item->quantity }} {{ $item->unit?->status_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="relative">
                                                <input type="number" step="0.01" wire:model="prices.{{ $item->id }}" 
                                                    class="block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-center text-sm font-bold"
                                                    placeholder="0.00">
                                            </div>
                                            @error('prices.'.$item->id) <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" wire:model="item_notes.{{ $item->id }}" 
                                                class="block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                placeholder="اختياري...">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-zinc-50 dark:bg-zinc-900/50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-left font-bold text-zinc-700 dark:text-zinc-300">
                                        {{ __('Total Amount') }}:
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $total = 0;
                                            foreach($pr->items as $item) {
                                                $price = (float)($prices[$item->id] ?? 0);
                                                $total += $price * $item->quantity;
                                            }
                                        @endphp
                                        <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                            {{ number_format($total, 2) }}
                                        </span>
                                        <span class="text-xs text-zinc-500">{{ $pr->currency->status_name ?? '' }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Footer Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- General Notes & Files -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-lg font-bold text-gray-800 mb-6">معلومات إضافية</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">العملة المستخدمة</label>
                                <select wire:model="currency_id" class="block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}">{{ $currency->status_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات عامة على العرض</label>
                                <textarea wire:model="general_notes" rows="3" class="block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="اكتب أي ملاحظات إضافية هنا..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">إرفاق عرض السعر الرسمي (PDF/Image)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:border-indigo-400 transition-colors cursor-pointer bg-gray-50/30">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="file-upload" class="relative cursor-pointer bg-transparent rounded-md font-bold text-indigo-600 hover:text-indigo-500">
                                                <span>تحميل الملفات</span>
                                                <input id="file-upload" wire:model="attachments" type="file" multiple class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, PNG, JPG حتى 10 ميجابايت</p>
                                    </div>
                                </div>
                                <div wire:loading wire:target="attachments" class="text-xs text-indigo-600 mt-2">جاري الرفع...</div>
                                @if($attachments)
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach($attachments as $index => $file)
                                            <div class="flex items-center bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg text-xs">
                                                <span>{{ $file->getClientOriginalName() }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Summary & Submit -->
                    <div class="bg-indigo-600 rounded-2xl shadow-xl p-8 text-white flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold mb-8">ملخص العرض</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-indigo-100">
                                    <span>عدد الأصناف:</span>
                                    <span class="font-bold">{{ $pr->items->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center text-indigo-100 border-b border-indigo-500 pb-4">
                                    <span>العملة:</span>
                                    <span class="font-bold">{{ $currencies->find($currency_id)?->status_name ?? 'غير محدد' }}</span>
                                </div>
                            </div>

                            {{-- PIN Verification --}}
                            <div class="mt-8">
                                <label class="block text-sm font-medium text-indigo-100 mb-2 italic">كود التحقق السري (PIN)</label>
                                <input type="text" wire:model="input_pin" maxlength="4"
                                    class="block w-full rounded-xl border-transparent focus:border-white focus:ring-0 text-center text-2xl font-bold tracking-[1em] text-indigo-900 placeholder-indigo-300"
                                    placeholder="0000">
                                @error('input_pin') <span class="text-xs text-red-200 mt-2 block">{{ $message }}</span> @enderror
                                <p class="text-[10px] text-indigo-200 mt-2">يرجى إدخال الكود المكون من 4 أرقام الذي وصلكم في رسالة الواتساب.</p>
                            </div>
                        </div>

                        <div class="mt-12">
                            <button type="submit" wire:loading.attr="disabled"
                                class="w-full bg-white text-indigo-600 font-extrabold py-4 rounded-xl shadow-lg hover:bg-indigo-50 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center">
                                <span wire:loading.remove>إرسال عرض السعر النهائي</span>
                                <span wire:loading>جاري الإرسال...</span>
                                <svg wire:loading.remove class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                            <p class="text-xs text-indigo-200 mt-4 text-center">بضغطك على إرسال، فإنك توافق على شروط وأحكام المؤسسة.</p>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        <!-- Footer -->
        <div class="mt-12 text-center text-gray-400 text-sm">
            &copy; {{ date('Y') }} جميع الحقوق محفوظة للمؤسسة.
        </div>
    </div>
</div>
