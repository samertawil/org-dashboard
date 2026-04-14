<div class="bg-slate-50 min-h-screen pb-16 px-4 font-sans focus:outline-none relative">
    {{-- Top Navigation --}}
    {{-- Top Navigation (Standard HTML) --}}
    <div class="fixed top-4 left-4 z-[9999] md:absolute md:top-8 md:left-8">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-2 bg-white/95 backdrop-blur-md px-4 py-2 rounded-xl border border-slate-200 shadow-lg text-slate-700 hover:bg-slate-50 transition-all duration-300 transform hover:scale-105 active:scale-95 group no-underline decoration-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-transform group-hover:-translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="font-bold text-sm">{{ __('الرئيسية') }}</span>
        </a>
    </div>
    
    {{-- Header & Progress Hero --}}
    <div class="mb-8 pt-4">
        <flux:card
            class="bg-linear-to-br min-h-[400px] flex flex-col justify-end to-blue-70 dark:to-blue-900 border-transparent overflow-hidden relative shadow-lg text-white">
            <img src="{{ asset('images/school5.jpg') }}" alt="Banner"
                class="hidden md:block absolute inset-0 w-full h-full object-cover pointer-events-none z-0" />
            <img src="{{ asset('images/school-mobile4.png') }}" alt="Banner"
                class="block md:hidden absolute inset-0 w-full h-full object-fit pointer-events-none z-0" />

            {{-- Background Deco --}}
            <div
                class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none z-0">
            </div>

            <div class="relative z-10 flex flex-col items-center justify-end h-full pt-16 pb-2 text-center">


            </div>
        </flux:card>
    </div>
    <div class="max-w-6xl mx-auto mt-12 mb-8 flex justify-center ">
        <flux:button variant="filled" color="indigo" icon="home" href="{{ route('dashboard') }}" wire:navigate class="px-8">
            {{ __('العودة للرئيسية') }}
        </flux:button>
    </div>
    <div class="text-center mb-10 flex flex-col items-center mt-2">
        {{-- <div class="inline-flex items-center justify-center p-3 bg-teal-100 rounded-full mb-4 ring-4 ring-teal-50 shadow-sm">
            <flux:icon icon="academic-cap" class="size-8 text-teal-600" />
        </div> --}}
        <h1 class="text-3xl font-bold mb-3 text-teal-900">{{ __('دليل إعداد ومتابعة الطلاب') }}</h1>
        <p class="text-teal-800 max-w-2xl text-lg leading-relaxed mix-blend-multiply">
            {{ __('تتبع تقدم استكمال تهيئة النظام للطلاب. هذه الشاشة توفر لك ملخص للمهام المطلوبة لضمان جاهزية المنصة التعليمية بنسبة 100%.') }}
        </p>
        <div class=" mt-4 w-full max-w-2xl bg-white/10 rounded-2xl p-6 backdrop-blur-sm border border-white/10">
            <div class="flex justify-between items-end mb-3">
                <div class="text-right">
                    <h3 class="text-lg font-semibold text-center">{{ __('نسبة الإنجاز الكلية') }}</h3>
                    <p class="text-sm text-indigo-200">{{ $completedCount }} {{ __('من أصل') }} {{ $totalSteps }}
                        {{ __('خطوات أساسية مكتملة') }}</p>
                </div>
                <div class="text-4xl font-bold font-mono" dir="ltr">
                    {{ round($progressPercentage) }}%
                </div>
            </div>
            <div class="w-full bg-black/20 rounded-full h-3 overflow-hidden">
                <div class="bg-emerald-400 h-3 rounded-full transition-all duration-1000 ease-out"
                    style="width: {{ $progressPercentage }}%"></div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto mb-10">

        <div class="text-center mt-10 mb-10">

            <div class="hidden md:flex items-center justify-between px-10 relative">
                <div class="absolute top-1/2 left-0 w-full h-[2px] bg-slate-200 -z-10"></div>

                <span
                    class="w-12 h-12 rounded-full bg-slate-300 flex items-center justify-center font-bold text-slate-700 shadow-sm border-4 border-slate-50">1</span>
                <span
                    class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center font-bold text-amber-900 shadow-sm border-4 border-slate-50">2</span>
                <span
                    class="w-12 h-12 rounded-full bg-slate-300 flex items-center justify-center font-bold text-slate-700 shadow-sm border-4 border-slate-50">3</span>
                <span
                    class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center font-bold text-amber-900 shadow-sm border-4 border-slate-50">4</span>
                <span
                    class="w-12 h-12 rounded-full bg-slate-300 flex items-center justify-center font-bold text-slate-700 shadow-sm border-4 border-slate-50">5</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            {{-- Step 1 --}}
            <a href="{{ route('student.group.index') }}" wire:navigate
                class="bg-cyan-50 p-8 rounded-xl flex flex-col items-center text-center shadow-sm hover:shadow-lg hover:-translate-y-1 focus:ring-2 focus:ring-teal-500 transition-all duration-300 relative group">
                @if ($hasGroups)
                    <div class="absolute top-4 right-4 bg-teal-600 text-white text-xs px-2 py-1 rounded shadow-sm">
                        مكتمل</div>
                @endif
                <div class="h-28 flex items-center text-teal-600 transition-transform group-hover:scale-110">
                    <img src="{{ asset('images/undraw_teaching_58yg.svg') }}"
                        class="w-16 h-16 object-contain drop-shadow" alt="النقاط التعليمية">
                </div>
                <h2 class="text-xl font-bold text-teal-900 mb-2">{{ __('النقاط التعليمية') }}</h2>
                <p class="text-sm text-teal-800 leading-relaxed mb-4 flex-1">
                    {{ __('إدخال وتهيئة بيانات النقاط التعليمية والمجموعات.') }}</p>

                <div class="mt-auto bg-white/60 backdrop-blur-sm w-full rounded py-2 text-teal-900 font-bold text-sm">
                    {{ __('المجموعات النشطة:') }} {{ $activeGroupsCount }}
                </div>
            </a>

            {{-- Step 2 --}}
            <a href="{{ route('subject.index') }}" wire:navigate
                class="bg-amber-50 p-8 rounded-xl flex flex-col items-center text-center shadow-sm hover:shadow-lg hover:-translate-y-1 focus:ring-2 focus:ring-amber-500 transition-all duration-300 relative group">
                @if ($hasSubjects)
                    <div class="absolute top-4 right-4 bg-amber-600 text-white text-xs px-2 py-1 rounded shadow-sm">
                        مكتمل</div>
                @endif

                <div class="h-28 flex items-center text-teal-600 transition-transform group-hover:scale-110">
                    <img src="{{ asset('images/undraw_books_wxzz.svg') }}" class="w-16 h-16 object-contain drop-shadow"
                        alt="النقاط التعليمية">
                </div>
                <h2 class="text-xl font-bold text-amber-900 mb-2">{{ __('المناهج') }}</h2>
                <p class="text-sm text-amber-800 leading-relaxed mb-4 flex-1">
                    {{ __('تحديد وإدخال المناهج للمجموعات التعليمية.') }}</p>

                <div class="mt-auto bg-white/60 backdrop-blur-sm w-full rounded py-2 text-amber-900 font-bold text-sm">
                    {{ __('المناهج المضافة:') }} {{ $SubjectsCounts }}
                </div>
            </a>


            {{-- Step 3 --}}
            <a href="{{ route('student.index') }}" wire:navigate
                class="bg-cyan-50 p-8 rounded-xl flex flex-col items-center text-center shadow-sm hover:shadow-lg hover:-translate-y-1 focus:ring-2 focus:ring-teal-500 transition-all duration-300 relative group {{ !$hasGroups ? 'opacity-50 pointer-events-none cursor-not-allowed' : '' }}">
                @if ($hasStudents && $studentsPercentage >= 90)
                    <div class="absolute top-4 right-4 bg-teal-600 text-white text-xs px-2 py-1 rounded shadow-sm">
                        مكتمل</div>
                @endif

                <div class="h-28 flex items-center text-teal-600 transition-transform group-hover:scale-110">
                    <img src="{{ asset('images/undraw_true-friends_1h3v.svg') }}"
                        class="w-16 h-16 object-contain drop-shadow" alt="النقاط التعليمية">
                </div>
                <h2 class="text-xl font-bold text-teal-900 mb-2">{{ __('الطلبة') }}</h2>
                <p class="text-sm text-teal-800 leading-relaxed mb-4 flex-1">
                    {{ __('تسجيل الطلاب وتوزيعهم على المجموعات لتكتمل السعة.') }}</p>

                <div class="mt-auto bg-white/60 backdrop-blur-sm w-full rounded py-2 text-teal-900 font-bold text-sm">
                    {{ __('الإشغال:') }} {{ $studentsPercentage }}%
                </div>
            </a>

            {{-- Step 4 --}}
            <a href="{{ route('survey.manage') }}" wire:navigate
                class="bg-amber-50 p-8 rounded-xl flex flex-col items-center text-center shadow-sm hover:shadow-lg hover:-translate-y-1 focus:ring-2 focus:ring-amber-500 transition-all duration-300 relative group {{ !$hasStudents ? 'opacity-50 pointer-events-none cursor-not-allowed' : '' }}">
                @if ($hasSurveys && $hasSurveysPersentage >= 99)
                    <div class="absolute top-4 right-4 bg-amber-600 text-white text-xs px-2 py-1 rounded shadow-sm">
                        مكتمل</div>
                @endif

                <div class="h-28 flex items-center text-teal-600 transition-transform group-hover:scale-110">
                    <img src="{{ asset('images/undraw_spreadsheets_bh6n.svg') }}"
                        class="w-16 h-16 object-contain drop-shadow" alt="النقاط التعليمية">
                </div>
                <h2 class="text-xl font-bold text-amber-900 mb-2">{{ __('أسئلة التقييم') }}</h2>
                <p class="text-sm text-amber-800 leading-relaxed mb-4 flex-1">
                    {{ __('تجهيز نماذج التقييم والاستبيانات للمجموعات.') }}</p>

                <div class="mt-auto bg-white/60 backdrop-blur-sm w-full rounded py-2 text-amber-900 font-bold text-sm">
                    {{ __('التجهيز:') }} {{ $hasSurveysPersentage }}%
                </div>
            </a>

            {{-- Step 5 --}}
            <a href="{{ route('reports.groups.attendance') }}" wire:navigate
                class="bg-cyan-50 p-8 rounded-xl flex flex-col items-center text-center shadow-sm hover:shadow-lg hover:-translate-y-1 focus:ring-2 focus:ring-teal-500 transition-all duration-300 relative group {{ !$hasSurveys ? 'opacity-50 pointer-events-none cursor-not-allowed' : '' }}">
                @if ($hasAttendance && $attendancePercentage >= 99)
                    <div class="absolute top-4 right-4 bg-teal-600 text-white text-xs px-2 py-1 rounded shadow-sm">
                        مكتمل</div>
                @endif
                <div class="h-28 flex items-center text-teal-600 transition-transform group-hover:scale-110">
                    <img src="{{ asset('images/undraw_professor_d7zn.svg') }}"
                        class="w-16 h-16 object-contain drop-shadow" alt="النقاط التعليمية">
                </div>
                <h2 class="text-xl font-bold text-teal-900 mb-2">{{ __('المتابعة') }}</h2>
                <p class="text-sm text-teal-800 leading-relaxed mb-4 flex-1">
                    {{ __('رصد المتابعة اليومية للطلاب حسب الجدول.') }}</p>

                <div class="mt-auto bg-white/60 backdrop-blur-sm w-full rounded py-2 text-teal-900 font-bold text-sm">
                    {{ __('الإنجاز:') }} {{ $attendancePercentage }}%
                </div>
            </a>




        </div>

    </div>
    {{-- Smart Actions / Alerts --}}
    @if ($progressPercentage < 100)
        <div class="max-w-6xl mx-auto mb-8  ">
            <h2 class="text-xl font-bold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                <flux:icon icon="bolt" class="size-5 text-amber-500" />
                {{ __('الإجراءات المطلوبة اليوم') }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 ">

                @if (!$hasGroups)
                    <div
                        class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30 p-4 rounded-xl flex gap-4">
                        <div
                            class="bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 p-2 rounded-lg h-fit">
                            <flux:icon icon="map-pin" class="size-5" />
                        </div>
                        <div>
                            <h4 class="font-bold text-amber-800 dark:text-amber-300 text-sm mb-1">
                                {{ __('مجموعات الطلاب') }}</h4>
                            <p class="text-xs text-amber-700/80 dark:text-amber-400/80 mb-3">
                                {{ __('لا يوجد مجموعات فعالة لليوم، يرجى تهيئة المجموعات.') }}</p>
                            <flux:button size="sm" variant="danger"
                                class="!bg-amber-500 hover:!bg-amber-600 !text-white"
                                href="{{ route('student.group.index') }}" wire:navigate>{{ __('إعداد المجموعات') }}
                            </flux:button>
                        </div>
                    </div>
                @endif

                @if (!$hasSubjects)
                    <div
                        class="bg-rose-50 dark:bg-rose-900/10 border border-rose-200 dark:border-rose-800/30 p-4 rounded-xl flex gap-4">
                        <div
                            class="bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 p-2 rounded-lg h-fit">
                            <flux:icon icon="book-open" class="size-5" />
                        </div>
                        <div>
                            <h4 class="font-bold text-rose-800 dark:text-rose-300 text-sm mb-1">
                                {{ __('المناهج التعليمية') }}</h4>
                            <p class="text-xs text-rose-700/80 dark:text-rose-400/80 mb-3">
                                {{ __('يوجد مجموعات تحتاج إلى ربط المناهج التعليمية بها.') }}</p>
                            <flux:button size="sm" color="rose" href="{{ route('subject.index') }}"
                                wire:navigate>{{ __('إضافة المناهج') }}</flux:button>
                        </div>
                    </div>
                @endif

                @if (!$hasStudents)
                    <div
                        class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/30 p-4 rounded-xl flex gap-4">
                        <div
                            class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-2 rounded-lg h-fit">
                            <flux:icon icon="users" class="size-5" />
                        </div>
                        <div>
                            <h4 class="font-bold text-blue-800 dark:text-blue-300 text-sm mb-1">
                                {{ __('بيانات الطلبة') }}</h4>
                            <p class="text-xs text-blue-700/80 dark:text-blue-400/80 mb-3">
                                {{ __('نسبة إشغال الطلاب متدنية (' . $studentsPercentage . '%). يرجى تسجيل وتوزيع الطلاب.') }}
                            </p>
                            <flux:button size="sm" color="blue" href="{{ route('student.index') }}"
                                wire:navigate>{{ __('تسجيل الطلاب') }}</flux:button>
                        </div>
                    </div>
                @endif

                @if (!$hasSurveys)
                    <div
                        class="bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-200 dark:border-indigo-800/30 p-4 rounded-xl flex gap-4">
                        <div
                            class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 p-2 rounded-lg h-fit">
                            <flux:icon icon="clipboard-document-check" class="size-5" />
                        </div>
                        <div>
                            <h4 class="font-bold text-indigo-800 dark:text-indigo-300 text-sm mb-1">
                                {{ __('أسئلة التقييم') }}</h4>
                            <p class="text-xs text-indigo-700/80 dark:text-indigo-400/80 mb-3">
                                {{ __('نسبة تجهيز الاستبيانات (' . $hasSurveysPersentage . '%). يرجى إعداد نماذج التقييم للمجموعات.') }}
                            </p>
                            <flux:button size="sm" color="indigo" href="{{ route('survey.manage') }}"
                                wire:navigate>{{ __('إعداد التقييمات') }}</flux:button>
                        </div>
                    </div>
                @endif

                @if (!$hasAttendance)
                    <div
                        class="bg-violet-50 dark:bg-violet-900/10 border border-violet-200 dark:border-violet-800/30 p-4 rounded-xl flex gap-4">
                        <div
                            class="bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 p-2 rounded-lg h-fit">
                            <flux:icon icon="calendar-days" class="size-5" />
                        </div>
                        <div>
                            <h4 class="font-bold text-violet-800 dark:text-violet-300 text-sm mb-1">
                                {{ __('الحضور والغياب') }}</h4>
                            <p class="text-xs text-violet-700/80 dark:text-violet-400/80 mb-3">
                                @if ($expectedAttendanceStudentsCount == 0 && $enteredAttendanceStudentsCount == 0)
                                    {{ __('لا يوجد رصد للحضور والغياب اليوم.') }}
                                @else
                                    {{ __('تم رصد (' . $enteredAttendanceStudentsCount . ') من أصل (' . $expectedAttendanceStudentsCount . ') طالب لليوم.') }}
                                @endif
                            </p>
                            <flux:button size="sm" color="violet"
                                href="{{ route('reports.groups.attendance') }}" wire:navigate>
                                {{ __('متابعة الحضور') }}</flux:button>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif
    {{-- summary --}}
    {{-- summary section --}}
    <div id="summary" class="max-w-6xl mx-auto space-y-12 mt-16 px-4 md:px-0">
        <!-- Overall Stats Section -->
        <section>
            <div class="flex items-center gap-3 mb-8">
                <div class="h-8 w-1.5 bg-indigo-500 rounded-full"></div>
                <flux:heading size="xl" class="text-slate-900 dark:text-white font-bold">
                    {{ __('الملخص العام للأداء') }}
                </flux:heading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Attendance Overall -->
                <flux:card
                    class="p-6 relative overflow-hidden group hover:ring-2 hover:ring-emerald-500/30 transition-all duration-300">
                    <div>
                        <flux:text size="xs"
                            class=" uppercase tracking-widest font-bold text-slate-500 dark:text-slate-400 mb-2">
                            {{ __('نسبة الحضور  لجميع المجموعات') }}</flux:text>
                        <div class="flex items-baseline gap-2">
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="text-4xl font-black text-slate-900 dark:text-white">{{ $overallAttendancePercentage }}%</span>
                            </div>
                            <div class="mt-6 h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 ease-out"
                                    style="width: {{ $overallAttendancePercentage }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <flux:text size="xs"
                            class=" uppercase tracking-widest font-bold text-slate-500 dark:text-slate-400 mb-2">
                            {{ __('نسبة الغياب ') }}</flux:text>
                        <div class="flex items-baseline gap-2">

                            <div class="flex items-baseline gap-2">
                                <span
                                    class="text-4xl font-black text-rose-600">{{ $overallAbsencePercentage }}%</span>
                            </div>
                            <div class="mt-6 h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-red-500 rounded-full transition-all duration-1000 ease-out"
                                    style="width: {{ $overallAbsencePercentage }}%"></div>
                            </div>
                        </div>

                    </div>
                </flux:card>

                <flux:card
                    class="p-6 relative overflow-hidden group hover:ring-2 hover:ring-blue-500/30 transition-all duration-300">

                    <flux:text size="xs"
                        class="uppercase tracking-widest font-bold text-slate-500 dark:text-slate-400 mb-2">
                        {{ __('حضور اليوم لجميع المجموعات') }}</flux:text>
                    <div class="flex items-baseline gap-2">
                        <div class="flex items-baseline gap-2">
                            <span
                                class="text-4xl font-black text-slate-900 dark:text-white">{{ $todayOverallAttendancePercentage }}%</span>
                        </div>
                        <div class="mt-6 h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 ease-out"
                                style="width: {{ $todayOverallAttendancePercentage }}%"></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <flux:text size="xs"
                            class="uppercase tracking-widest font-bold text-slate-500 dark:text-slate-400 mb-2">
                            {{ __('غياب اليوم') }}</flux:text>
                        <div class="flex items-baseline gap-2">
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="text-4xl font-black text-rose-600">{{ $todayOverallAbsencePercentage }}%</span>
                            </div>
                            <div class="mt-6 h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-red-500 rounded-full transition-all duration-1000 ease-out"
                                    style="width: {{ $todayOverallAbsencePercentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </flux:card>


                <!-- Withdrawn Overall -->
                <flux:card
                    class="p-6 relative overflow-hidden group hover:ring-2 hover:ring-slate-500/30 transition-all duration-300">
                    <flux:text size="xs"
                        class="uppercase tracking-widest font-bold text-slate-500 dark:text-slate-400 mb-2">
                        {{ __('المنسحبون') }}</flux:text>
                    <div class="flex items-baseline gap-2">
                        <span
                            class="text-4xl font-black text-slate-900 dark:text-white">{{ $overallWithdrawnCount }}</span>
                        <span
                            class="text-lg text-slate-500 font-medium">({{ number_format($overallWithdrawnPercentage, 1) }}%)</span>
                    </div>
                    <flux:text size="xs" class="mt-4 text-slate-400 flex items-center gap-1">
                        <flux:icon icon="information-circle" size="3" />
                        {{ __('إجمالي المنسحبين من كافة المجموعات') }}
                    </flux:text>
                </flux:card>
            </div>
        </section>

        <!-- Groups Detailed View -->
        <section>
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1.5 bg-amber-500 rounded-full"></div>
                    <flux:heading size="xl" class="text-slate-900 dark:text-white font-bold">
                        {{ __('تفاصيل المجموعات') }}
                    </flux:heading>
                </div>
                <flux:badge color="slate" size="lg" variant="solid" class="rounded-full px-4">
                    {{ $activeGroupsData->count() }} {{ __('مجموعة نشطة') }}</flux:badge>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($activeGroupsData as $group)
                    <flux:card
                        class="p-0 overflow-hidden border-none shadow-sm hover:shadow-xl transition-all duration-500 group border-t-4 border-indigo-500">
                        <!-- Card Header -->
                        <div
                            class="bg-light-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800 px-6 py-5 flex justify-between items-start">
                            <div class="space-y-1">
                                <flux:heading level="3"
                                    class="text-xl font-black text-indigo-900 dark:text-indigo-300">
                                    {{ $group->name }}
                                </flux:heading>
                                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                    <flux:icon icon="map-pin" size="3.5" class="text-slate-400" />
                                    <span class="text-xs font-semibold">{{ $group->region->region_name }}</span>
                                    <flux:icon icon="chevron-left" size="3" class="text-slate-300" />
                                    <span class="text-xs font-semibold">{{ $group->city->city_name }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @if ($group->late_students_count > 0)
                                    <flux:badge color="amber" icon="clock" size="sm" variant="solid"
                                        class="animate-pulse">
                                        {{ $group->late_students_count }} {{ __('تقييم متأخر') }}
                                    </flux:badge>
                                @endif
                                @if ($group->withdrawn_count > 0)
                                    <flux:badge color="rose" icon="user-minus" size="sm" variant="outline">
                                        {{ $group->withdrawn_count }} {{ __('طالب منسحب') }}
                                    </flux:badge>
                                @endif
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 space-y-8">
                            <!-- Quick Stats Grid -->
                            <div class="grid grid-cols-3 gap-4">
                                <div
                                    class="bg-indigo-50 dark:bg-indigo-900/10 p-4 rounded-2xl text-center group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/20 transition-colors">
                                    <flux:icon icon="users"
                                        class="mx-auto text-indigo-600 dark:text-indigo-400 mb-2" size="sm" />
                                    <flux:text size="xs"
                                        class="font-bold text-indigo-900/60 dark:text-indigo-400/60 uppercase tracking-tighter">
                                        {{ __('الطلاب') }}</flux:text>
                                    <div class="text-2xl font-black text-indigo-900 dark:text-white">
                                        {{ $group->students_count }}</div>
                                </div>
                                <div
                                    class="bg-amber-50 dark:bg-amber-900/10 p-4 rounded-2xl text-center group-hover:bg-amber-100 dark:group-hover:bg-amber-900/20 transition-colors">
                                    <flux:icon icon="book-open"
                                        class="mx-auto text-amber-600 dark:text-amber-400 mb-2" size="sm" />
                                    <flux:text size="xs"
                                        class="font-bold text-amber-900/60 dark:text-amber-400/60 uppercase tracking-tighter">
                                        {{ __('المناهج') }}</flux:text>
                                    <div class="text-2xl font-black text-amber-900 dark:text-white">
                                        {{ $group->subjects_count }}</div>
                                </div>
                                <div
                                    class="bg-emerald-50 dark:bg-emerald-900/10 p-4 rounded-2xl text-center group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/20 transition-colors">
                                    <flux:icon icon="academic-cap"
                                        class="mx-auto text-emerald-600 dark:text-emerald-400 mb-2" size="sm" />
                                    <flux:text size="xs"
                                        class="font-bold text-emerald-900/60 dark:text-emerald-400/60 uppercase tracking-tighter">
                                        {{ __('المعلمون') }}</flux:text>
                                    <div class="text-2xl font-black text-emerald-900 dark:text-white">
                                        {{ $group->teachers_count }}</div>
                                </div>
                            </div>

                            <!-- Progress Monitoring -->
                            <div
                                class="bg-slate-50 dark:bg-slate-900/40 rounded-2xl p-6 border border-slate-100 dark:border-slate-800 space-y-6">
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <flux:text size="sm"
                                            class="font-bold text-slate-700 dark:text-slate-300">
                                            {{ __('  نسبة الحضور الكلية للمجموعة خلال الفصل') }}</flux:text>

                                        <span
                                            class="text-lg font-black text-emerald-600">{{ $group->attendance_percentage }}%</span>
                                    </div>
                                    <div
                                        class="h-3 w-full bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 ease-out shadow-sm"
                                            style="width: {{ $group->attendance_percentage }}%"></div>
                                    </div>
                                    {{-- <div class="flex justify-between mt-2 px-1">
                                        <flux:text size="xs" class="text-slate-400">{{ __('مجموع عدد الحضور للطلاب حتى تاريخ اليوم:') }} <span class="font-bold text-emerald-600">{{ $group->present_count }}</span></flux:text>
                                        <flux:text size="xs" class="text-slate-400">{{ __('غائب:') }} <span class="font-bold text-rose-500">{{ $group->absent_count }}</span></flux:text>
                                    </div> --}}
                                </div>

                                <flux:separator class="opacity-50" />

                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <flux:text size="sm"
                                            class="font-bold text-slate-700 dark:text-slate-300">
                                            {{ __('نسبة الحضور لهذا اليوم') }}</flux:text>
                                        <span
                                            class="text-lg font-black text-blue-600">{{ $group->today_attendance_percentage }}%</span>
                                    </div>
                                    <div
                                        class="h-3 w-full bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full bg-blue-500 rounded-full transition-all duration-1000 ease-out shadow-sm"
                                            style="width: {{ $group->today_attendance_percentage }}%"></div>
                                    </div>
                                    <div class="mt-3 flex flex-col sm:flex-row items-center justify-center gap-4 text-center">
                                        <div>
                                          
                                         <p>   {{ __('البيانات مفقودة:') }}</p>
                                         <p>   {{ $group->missing_attendance_count }} سجل لم يتم ادخال حضور او غياب</p>
                                       
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-indigo-900 dark:bg-indigo-950 px-6 py-3 flex justify-between items-center">
                            <flux:text size="xs" class="text-indigo-200/70">{{ __('تحليل البيانات نشط') }}
                            </flux:text>
                            <flux:button variant="ghost" size="xs" class="!text-white hover:!bg-white/10"
                                icon-trailing="chevron-left">
                                {{ __('عرض التقارير') }}
                            </flux:button>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </section>

        <!-- Alerts / System Health Section -->
        @if (count($sectorNotAddedNames) > 0 || count($gradingScaleMissingNames) > 0)
            <section
                class="bg-rose-50/50 dark:bg-rose-950/10 rounded-3xl p-8 border border-rose-100 dark:border-rose-900/30 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="bg-rose-100 dark:bg-rose-900/40 p-3 rounded-2xl text-rose-600">
                        <flux:icon icon="exclamation-triangle" variant="solid" size="lg" />
                    </div>
                    <div>
                        <flux:heading size="xl" class="text-rose-900 dark:text-rose-300 font-black">
                            {{ __('تنبيهات التهيئة') }}
                        </flux:heading>
                        <flux:text size="sm" class="text-rose-800/70 dark:text-rose-400/70">
                            {{ __('يوجد بعض الإعدادات المفقودة التي قد تؤثر على دقة التقييمات') }}</flux:text>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if (count($sectorNotAddedNames) > 0)
                        <div
                            class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-rose-100 dark:border-rose-900/30">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="size-2 bg-rose-500 rounded-full animate-ping"></span>
                                <flux:heading size="sm" class="text-rose-900 dark:text-rose-300 font-bold">
                                    {{ __('تقييمات مفقودة الأسئلة') }}</flux:heading>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($sectorNotAddedNames as $name)
                                    <flux:badge color="rose" variant="outline" size="sm" class="font-bold">
                                        {{ $name }}</flux:badge>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (count($gradingScaleMissingNames) > 0)
                        <div
                            class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-amber-100 dark:border-amber-900/30">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="size-2 bg-amber-500 rounded-full"></span>
                                <flux:heading size="sm" class="text-amber-900 dark:text-amber-300 font-bold">
                                    {{ __('تقيمات مفقود سلم الدرجات(Grading Scale)') }}</flux:heading>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($gradingScaleMissingNames as $name)
                                    <flux:badge color="amber" variant="outline" size="sm" class="font-bold">
                                        {{ $name }}</flux:badge>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif
    </div>
{{-- 
    <div id="summary">
        @foreach ($activeGroupsData as $groups)
            <li> {{ $groups->name }} ( {{ $groups->region->region_name }} -> {{ $groups->city->city_name }} )
                {{ $groups->students_count }} طالب -> {{ $groups->subjects_count }} منهج ->
                {{ $groups->teachers_count }}
                معلم ,{{ $groups->late_students_count }} متاخر عن التقييم {{ $groups->withdrawn_count }} عدد
                المنسحبين من
                المجموعة {{ $groups->withdrawn_percentage }} نسبة المنسحبين من المجموعة</li>

            <li>{{ $groups->present_count }} مجموع عدد الحضور للطلاب حتى تاريخ اليوم</li>
            <li>{{ $groups->absent_count }}محموعد عدد الغياب للطلاب حتى تاريخ اليوم</li>
            <li>{{ $groups->missing_attendance_count }} مجموع عدد البيانات الغير مدخلة حتى تاريخ اليوم</li>

            <li>{{ $groups->attendance_percentage }}% للمجموعة نسبة الحضور الكلية</li>
            <li>{{ $groups->absence_percentage }}% نسبة الغياب الكلية للمجموعة</li>

            <li>{{ $groups->today_attendance_percentage }}% نسبة الحضور لهذا اليوم</li>
            <li>{{ $groups->today_absence_percentage }}% نسبة الغياب لهذا اليوم</li>
        @endforeach
        <li>{{ $overallAttendancePercentage }}نسبة الحضور الكلية لجميع المجموعات</li>
        <li>{{ $overallAbsencePercentage }}نسبة الغياب الكلية لجميع المجموعات</li>
        <li>{{ $todayOverallAttendancePercentage }}نسبة الحضور الكلية لجميع المجموعات لهذا اليوم</li>
        <li>{{ $todayOverallAbsencePercentage }}نسبة الغياب الكلية لجميع المجموعات لهذا اليوم</li>

        <li>{{ $overallWithdrawnCount }} عدد المنسحبين من جميع المجموعات</li>
        <li>{{ number_format($overallWithdrawnPercentage, 1) }} نسبة المنسحبين من جميع المجموعات</li>

        تقيمات لم يتم ادخال اسئلة لها
        @foreach ($sectorNotAddedNames as $sectorNotAddedName)
            <li>{{ $sectorNotAddedName }}</li>
        @endforeach
        تقيمات لم يتم ادخال درجات المؤشرات grading Scale
        @foreach ($gradingScaleMissingNames as $ScaleMissingName)
            <li>{{ $ScaleMissingName }}</li>
        @endforeach

        </li>
    </div> --}}

    {{-- Bottom Navigation (Standard HTML) --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12  flex justify-center  ">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 bg-teal-600 hover:bg-teal-700 text-white shadow-xl hover:shadow-teal-500/20 px-10 py-5 rounded-2xl transition-all duration-300 transform hover:-translate-y-1 active:scale-95 no-underline font-black uppercase tracking-wide decoration-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            {{ __('العودة للرئيسية') }}
        </a>
    </div>

    {{-- Scroll to Top Button (Robust Pure Native JS) --}}
    <div id="scroll-to-top-btn" 
         class="fixed bottom-6 right-6 z-[9999] transition-all duration-500 opacity-0 translate-y-20 pointer-events-none scale-0">
        <button onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl text-teal-600 dark:text-teal-400 p-5 rounded-full shadow-[0_20px_50px_rgba(13,148,136,0.3)] border border-teal-100/50 dark:border-slate-700 transition-all duration-300 hover:scale-110 active:scale-90 focus:outline-none group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 transition-transform group-hover:-translate-y-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 15l7-7 7 7" />
            </svg>
        </button>
    </div>

    <script>
        (function() {
            function updateScrollButton() {
                const btn = document.getElementById('scroll-to-top-btn');
                if (!btn) return;
                
                const scrollPos = window.scrollY || document.documentElement.scrollTop;
                
                if (scrollPos > 400) {
                    btn.classList.remove('opacity-0', 'translate-y-20', 'pointer-events-none', 'scale-0');
                    btn.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto', 'scale-100');
                } else {
                    btn.classList.add('opacity-0', 'translate-y-20', 'pointer-events-none', 'scale-0');
                    btn.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto', 'scale-100');
                }
            }

            window.addEventListener('scroll', updateScrollButton);
            document.addEventListener('DOMContentLoaded', updateScrollButton);
            document.addEventListener('livewire:navigated', updateScrollButton);
            
            // Re-check periodically if transition state gets stuck
            setInterval(updateScrollButton, 1000);
        })();
    </script>
</div>
</div>
