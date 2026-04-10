<div class="bg-slate-50 min-h-screen pb-16 px-4 font-sans focus:outline-none">
    {{-- Header & Progress Hero --}}
    <div class="mb-8 pt-4">
        <flux:card
            class="bg-linear-to-br min-h-[400px] flex flex-col justify-end to-blue-70 dark:to-blue-900 border-transparent overflow-hidden relative shadow-lg text-white">
            <img src="{{ asset('images/school5.jpg') }}" alt="Banner"
                class="hidden md:block absolute inset-0 w-full h-full object-cover pointer-events-none z-0" />
            <img src="{{ asset('images/school-mobile4.png') }}" alt="Banner"
                class="block md:hidden absolute inset-0 w-full h-full object-contain pointer-events-none z-0" />

            {{-- Background Deco --}}
            <div
                class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none z-0">
            </div>

            <div class="relative z-10 flex flex-col items-center justify-end h-full pt-16 pb-2 text-center">


            </div>
        </flux:card>
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
<div id="summary">
    @foreach ($activeGroupsData as $groups)
        <li> {{ $groups->name }} ( {{ $groups->region->region_name }} -> {{ $groups->city->city_name }} )
            {{ $groups->students_count }} طالب -> {{ $groups->subjects_count }} منهج -> {{ $groups->teachers_count }}
            معلم ,{{ $groups->late_students_count }} متاخر عن التقييم {{ $groups->withdrawn_count }} عدد المنسحبين من
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
</div>
</div>
