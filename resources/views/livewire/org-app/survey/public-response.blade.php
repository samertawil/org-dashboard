<div dir="rtl"
    class="min-h-screen text-right bg-zinc-50 dark:bg-zinc-950 flex flex-col items-center justify-center p-4">

    <div
        class="w-full max-w-2xl bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        <div class="flex justify-center items-center mt-3">
            <img src="{{ asset('logo.png') }}" alt="" style="width: 200px; height: 100px;">
        </div>
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 pt-4 text-center">
            <h1 class="text-2xl font-bold">{{ $this->survey->survey_name }}</h1>
            {{-- <p class="text-blue-100 mt-2">يرجى تعبئة المعلومات أدناه بعناية.</p> --}}
        </div>

        <div class="py-4  px-6">
            @if ($step == 0)
                {{-- Step 0: Survey Closed --}}
                <div class="text-center py-12">
                    <div
                        class="mx-auto w-20 h-20 bg-amber-100 dark:bg-amber-900/40 rounded-full flex items-center justify-center mb-6">
                        <flux:icon name="no-symbol" class="size-10 text-amber-600 dark:text-amber-400" />
                    </div>
                    <h2 class="text-2xl font-bold dark:text-white">الاستبيان مغلق حالياً</h2>
                    <p class="text-zinc-600 dark:text-zinc-400 mt-2">هذا الاستبيان لا يستقبل ردود في الوقت الحالي. يرجى
                        المحاولة لاحقاً.</p>
                    <div class="mt-8">
                        <flux:button href="/" variant="primary">العودة للرئيسية</flux:button>
                    </div>
                </div>
            @elseif($step == 1)
                {{-- Step 1: Identification --}}
                @if ($this->survey->notes)
                    <div class="last:mb-0">

                        <div
                            class="prose prose-sm md:prose-base prose-blue dark:prose-invert max-w-none text-zinc-700 dark:text-zinc-300">
                            {!! $this->survey->notes !!}
                        </div>
                    </div>
                @endif
                <div class="space-y-6">
                    @if ($this->survey->conditions)
                        <div
                            class="p-2 px-6 bg-blue-50/50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl">
                            @if ($this->survey->conditions)
                                <div class="mb-0 last:mb-0">
                                    <h3 class="text-lg font-bold text-blue-800 dark:text-blue-300 mb-2">تعليمات وشروط
                                        الاستبيان:</h3>
                                    <div
                                        class="prose prose-sm md:prose-base prose-blue dark:prose-invert max-w-none text-zinc-700 dark:text-zinc-300">
                                        {!! $this->survey->conditions !!}
                                    </div>
                                </div>
                            @endif


                        </div>
                    @endif

                    <div class="text-center">
                        {{-- <div class="mx-auto w-16 h-16 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center mb-4">
                            <flux:icon name="identification" class="size-8 text-blue-600 dark:text-blue-400" />
                        </div> --}}
                        {{-- <h2 class="text-xl font-semibold dark:text-white">التحقق من الهوية</h2> --}}
                        <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">أدخل رقم الهوية لتبدأ الاستبيان.</p>
                    </div>

                    <form wire:submit="startSurvey" class="space-y-4">
                        <flux:input label="يرجى إدخال رقم هوية الطفل (العمر المستهدف: 6–12 سنة)."
                            wire:model="account_id" placeholder="123456789" required />
                        <flux:button type="submit" variant="primary" size="base" class="w-full">
                            بدء الاستبيان
                        </flux:button>
                    </form>
                </div>
            @elseif($step == 4)
                {{-- Step 4: Password Gate --}}
                <div class="space-y-6 text-center">
                    <div
                        class="mx-auto w-16 h-16 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-8 h-8 text-blue-600 dark:text-blue-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold dark:text-white">بوابة الأمان</h2>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1 mb-4">
                        لقد قمت بإدخال إجابات لهذا الاستبيان مسبقاً. للمتابعة وتعديل بياناتك، يرجى إدخال كلمة المرور
                        التي قمت باختيارها.
                    </p>

                    <form wire:submit="verifyPassword" class="space-y-4 max-w-sm mx-auto text-right">
                        <flux:input type="password" label="كلمة المرور" wire:model="enteredPassword" placeholder="****"
                            required maxlength="4" />

                        <div class="flex gap-2 pt-2">
                            <flux:button wire:click="$set('step', 1)" variant="ghost" class="w-1/3">إلغاء</flux:button>
                            <flux:button type="submit" variant="primary" size="base" class="w-2/3">
                                تحقق ودخول
                            </flux:button>
                        </div>
                    </form>
                </div>
            @elseif($step == 2)
                {{-- Step 2: Survey Questions --}}
                <form wire:submit="submit" class="space-y-8">

                    @foreach ($this->survey->questions->sortBy('question_order') as $question)
                        <div class="space-y-2">
                            <flux:label class="text-lg font-medium dark:text-white pb-3">
                                {{ $question->question_order }}. {{ $question->question_ar_text }}
                                @if (!isset($question->required_answer) || $question->required_answer == 1)
                                    <span class="text-red-500">*</span> <span
                                        class="text-sm text-red-500">(مطلوب)</span>
                                @endif
                            </flux:label>

                            @if ($question->answer_input_type == 1)
                                {{-- Short Text --}}
                                <flux:input style="height: auto" wire:model="answers.{{ $question->id }}.answer"
                                    placeholder="اكتب إجابتك هنا..." />
                            @elseif($question->answer_input_type == 2)
                                {{-- Multiple Choice --}}
                                <flux:radio.group wire:model="answers.{{ $question->id }}.answer"
                                    class="grid grid-cols-1 gap-2">
                                    @if (is_array($question->answer_options))
                                        @foreach ($question->answer_options as $option)
                                            @php
                                                $optValue = is_array($option) ? $option['value'] ?? '' : $option;
                                                $optLabel = is_array($option) ? $option['label'] ?? '' : $option;
                                            @endphp
                                            <flux:radio value="{{ $optValue }}" label="{{ $optLabel }}" />
                                        @endforeach
                                    @endif
                                </flux:radio.group>
                            @elseif($question->answer_input_type == 3)
                                {{-- Number --}}
                                @if (strtolower(trim($question->question_ar_text)) === 'password' || trim($question->question_ar_text) === 'كلمة المرور')
                                    <flux:input type="password" wire:model="answers.{{ $question->id }}.answer"
                                        placeholder="****" maxlength="4" />
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">يجب أن تتكون من 4 أرقام
                                        لضمان إمكانية تعديل بياناتك لاحقاً.</div>
                                @else
                                    <flux:input type="number" wire:model="answers.{{ $question->id }}.answer" />
                                @endif
                            @elseif($question->answer_input_type == 5)
                                {{-- Date --}}
                                <flux:input type="date" wire:model="answers.{{ $question->id }}.answer" />
                            @elseif($question->answer_input_type == 4)
                                {{-- Long Text --}}
                                <flux:textarea wire:model="answers.{{ $question->id }}.answer" rows="3" />
                            @elseif($question->answer_input_type == 6)
                                {{-- File Upload --}}
                                <input type="file" wire:model="answers.{{ $question->id }}.answer"
                                    class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-zinc-400 dark:file:bg-zinc-800 dark:file:text-blue-400" />
                                <div wire:loading wire:target="answers.{{ $question->id }}.answer"
                                    class="text-sm text-blue-500 mt-1">جاري الرفع...</div>
                                @if (isset($answers[$question->id]['answer']) &&
                                        is_string($answers[$question->id]['answer']) &&
                                        $answers[$question->id]['answer'] != '')
                                    <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                                        يوجد ملف محفوظ مسبقاً. <a
                                            href="{{ Storage::url($answers[$question->id]['answer']) }}"
                                            target="_blank" class="underline text-blue-600">عرض الملف</a>
                                        (اختر ملفاً جديداً لاستبداله)
                                    </div>
                                @endif
                            @endif

                            @if ($question->require_detail == 1)
                                <div
                                    class="mt-3 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-100 dark:border-zinc-700">
                                    <flux:input type="text" wire:model="answers.{{ $question->id }}.detail"
                                        label="{{ $question->detail ?: 'التفاصيل (مطلوبة)' }}" placeholder="..." />
                                </div>
                            @endif

                        </div>
                        <hr class="border-zinc-100 dark:border-zinc-800" />
                    @endforeach

                    <div class="flex justify-between items-center pt-4">
                        <flux:button wire:click="$set('step', 1)" variant="ghost">رجوع</flux:button>
                        <flux:button type="submit" variant="primary" size="base">
                            إرسال الرد
                        </flux:button>
                    </div>

                    @if ($errors->any())
                        <div class="mt-4 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-zinc-800 dark:text-red-400"
                            role="alert">
                            <ul class="list-disc pr-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>
            @elseif($step == 3)
                {{-- Step 3: Success --}}
                <div class="text-center py-12">
                    <div
                        class="mx-auto w-20 h-20 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center mb-6">
                        <flux:icon name="check" class="size-10 text-green-600 dark:text-green-400" />
                    </div>
                    <h2 class="text-2xl font-bold dark:text-white">شكراً لك!</h2>
                    <p class="text-zinc-600 dark:text-zinc-400 mt-2">{{ session('message') }}</p>

                </div>
            @endif
        </div>
    </div>
</div>
