<div>
    {{-- Header & Progress Hero --}}
    <div class="mb-8">
        <flux:card class="bg-linear-to-br from-indigo-600 to-blue-700 dark:from-indigo-900 dark:to-blue-900 border-transparent overflow-hidden relative shadow-lg text-white">
            <img src="{{ asset('banner.webp') }}" alt="Banner" class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay pointer-events-none z-0" />
            
            {{-- Background Deco --}}
            <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none z-0"></div>
           

            <div class="relative z-10 flex flex-col items-center justify-center py-6 text-center">
                <div class="inline-flex items-center justify-center p-3 bg-white/10 rounded-full mb-4 ring-4 ring-white/10">
                    <flux:icon icon="academic-cap" class="size-8 text-white" />
                </div>
                <h1 class="text-3xl font-bold mb-2">{{ __('دليل إعداد ومتابعة الطلاب') }}</h1>
                <p class="text-indigo-100 max-w-lg mb-8">
                    {{ __('تتبع تقدم استكمال تهيئة النظام للطلاب. هذه الشاشة توفر لك ملخص للمهام المطلوبة لضمان جاهزية المنصة التعليمية بنسبة 100%.') }}
                </p>

                <div class="w-full max-w-2xl bg-white/10 rounded-2xl p-6 backdrop-blur-sm border border-white/10">
                    <div class="flex justify-between items-end mb-3">
                        <div class="text-right">
                            <h3 class="text-lg font-semibold">{{ __('نسبة الإنجاز الكلية') }}</h3>
                            <p class="text-sm text-indigo-200">{{ $completedCount }} {{ __('من أصل') }} {{ $totalSteps }} {{ __('خطوات أساسية مكتملة') }}</p>
                        </div>
                        <div class="text-4xl font-bold font-mono" dir="ltr">
                            {{ round($progressPercentage) }}%
                        </div>
                    </div>
                    <div class="w-full bg-black/20 rounded-full h-3 overflow-hidden">
                        <div class="bg-emerald-400 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Smart Actions / Alerts --}}
    @if($progressPercentage < 100)
    <div class="mb-8">
        <h2 class="text-xl font-bold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
            <flux:icon icon="bolt" class="size-5 text-amber-500" />
            {{ __('الإجراءات المطلوبة اليوم') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            @if(!$hasGroups)
            <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30 p-4 rounded-xl flex gap-4">
                <div class="bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 p-2 rounded-lg h-fit">
                    <flux:icon icon="map-pin" class="size-5" />
                </div>
                <div>
                    <h4 class="font-bold text-amber-800 dark:text-amber-300 text-sm mb-1">{{ __('مجموعات الطلاب') }}</h4>
                    <p class="text-xs text-amber-700/80 dark:text-amber-400/80 mb-3">{{ __('لا يوجد مجموعات فعالة لليوم، يرجى تهيئة المجموعات.') }}</p>
                    <flux:button size="sm" variant="danger" class="!bg-amber-500 hover:!bg-amber-600 !text-white" href="{{ route('student.group.index') }}" wire:navigate>{{ __('إعداد المجموعات') }}</flux:button>
                </div>
            </div>
            @endif

            @if(!$hasSubjects)
            <div class="bg-rose-50 dark:bg-rose-900/10 border border-rose-200 dark:border-rose-800/30 p-4 rounded-xl flex gap-4">
                <div class="bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 p-2 rounded-lg h-fit">
                    <flux:icon icon="book-open" class="size-5" />
                </div>
                <div>
                    <h4 class="font-bold text-rose-800 dark:text-rose-300 text-sm mb-1">{{ __('المناهج التعليمية') }}</h4>
                    <p class="text-xs text-rose-700/80 dark:text-rose-400/80 mb-3">{{ __('يوجد مجموعات تحتاج إلى ربط المناهج التعليمية بها.') }}</p>
                    <flux:button size="sm" color="rose" href="{{ route('subject.index') }}" wire:navigate>{{ __('إضافة المناهج') }}</flux:button>
                </div>
            </div>
            @endif

            @if(!$hasStudents)
            <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/30 p-4 rounded-xl flex gap-4">
                <div class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-2 rounded-lg h-fit">
                    <flux:icon icon="users" class="size-5" />
                </div>
                <div>
                    <h4 class="font-bold text-blue-800 dark:text-blue-300 text-sm mb-1">{{ __('بيانات الطلبة') }}</h4>
                    <p class="text-xs text-blue-700/80 dark:text-blue-400/80 mb-3">
                        {{ __('نسبة إشغال الطلاب متدنية ('.$studentsPercentage.'%). يرجى تسجيل وتوزيع الطلاب.') }}
                    </p>
                    <flux:button size="sm" color="blue" href="{{ route('student.index') }}" wire:navigate>{{ __('تسجيل الطلاب') }}</flux:button>
                </div>
            </div>
            @endif

            @if(!$hasSurveys)
            <div class="bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-200 dark:border-indigo-800/30 p-4 rounded-xl flex gap-4">
                <div class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 p-2 rounded-lg h-fit">
                    <flux:icon icon="clipboard-document-check" class="size-5" />
                </div>
                <div>
                    <h4 class="font-bold text-indigo-800 dark:text-indigo-300 text-sm mb-1">{{ __('أسئلة التقييم') }}</h4>
                    <p class="text-xs text-indigo-700/80 dark:text-indigo-400/80 mb-3">{{ __('نسبة تجهيز الاستبيانات ('.$hasSurveysPersentage.'%). يرجى إعداد نماذج التقييم للمجموعات.') }}</p>
                    <flux:button size="sm" color="indigo" href="{{ route('survey.manage') }}" wire:navigate>{{ __('إعداد التقييمات') }}</flux:button>
                </div>
            </div>
            @endif

            @if(!$hasAttendance)
            <div class="bg-violet-50 dark:bg-violet-900/10 border border-violet-200 dark:border-violet-800/30 p-4 rounded-xl flex gap-4">
                <div class="bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 p-2 rounded-lg h-fit">
                    <flux:icon icon="calendar-days" class="size-5" />
                </div>
                <div>
                    <h4 class="font-bold text-violet-800 dark:text-violet-300 text-sm mb-1">{{ __('الحضور والغياب') }}</h4>
                    <p class="text-xs text-violet-700/80 dark:text-violet-400/80 mb-3">
                        @if($expectedAttendanceStudentsCount == 0 && $enteredAttendanceStudentsCount == 0)
                            {{ __('لا يوجد رصد للحضور والغياب اليوم.') }}
                        @else
                            {{ __('تم رصد ('.$enteredAttendanceStudentsCount.') من أصل ('.$expectedAttendanceStudentsCount.') طالب لليوم.') }}
                        @endif
                    </p>
                    <flux:button size="sm" color="violet" href="{{ route('reports.groups.attendance') }}" wire:navigate>{{ __('متابعة الحضور') }}</flux:button>
                </div>
            </div>
            @endif

        </div>
    </div>
    @endif

    {{-- Details Step Cards --}}
    <div class="mb-12 mt-8">
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#1a7a85] dark:text-teal-500 mb-12 tracking-tight text-center">
            {{ __('مسار تهيئة النظام والتفعيل') }}
        </h2>

        {{-- Timeline Indicator --}}
        <div class="hidden lg:flex items-center justify-between px-20 relative mb-8" dir="ltr">
            <div class="absolute top-1/2 left-0 w-full h-[2px] bg-gray-200 dark:bg-zinc-700 -z-10"></div>
            
            <span class="w-12 h-12 rounded-full border-4 border-white dark:border-zinc-950 flex items-center justify-center font-bold text-lg shadow-sm transition-colors {{ $hasGroups ? 'bg-emerald-500 text-white border-emerald-100' : 'bg-[#cbd5e1] text-gray-700' }}">1</span>
            
            <span class="w-12 h-12 rounded-full border-4 border-white dark:border-zinc-950 flex items-center justify-center font-bold text-lg shadow-sm transition-colors {{ $hasSubjects ? 'bg-emerald-500 text-white border-emerald-100' : 'bg-[#f4e3d3] text-gray-700' }}">2</span>
            
            <span class="w-12 h-12 rounded-full border-4 border-white dark:border-zinc-950 flex items-center justify-center font-bold text-lg shadow-sm transition-colors {{ $hasStudents ? 'bg-emerald-500 text-white border-emerald-100' : 'bg-[#cbd5e1] text-gray-700' }}">3</span>
            
            <span class="w-12 h-12 rounded-full border-4 border-white dark:border-zinc-950 flex items-center justify-center font-bold text-lg shadow-sm transition-colors {{ $hasSurveys ? 'bg-emerald-500 text-white border-emerald-100' : 'bg-[#f4e3d3] text-gray-700' }}">4</span>
            
            <span class="w-12 h-12 rounded-full border-4 border-white dark:border-zinc-950 flex items-center justify-center font-bold text-lg shadow-sm transition-colors {{ $hasAttendance ? 'bg-emerald-500 text-white border-emerald-100' : 'bg-[#cbd5e1] text-gray-700' }}">5</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
            
            {{-- Step 1 --}}
            <div class="bg-[#d1e1e6] dark:bg-slate-800/80 {{ $hasGroups ? 'border-emerald-500 shadow-emerald-100 ring-2 ring-emerald-500/20' : 'border-transparent' }} p-6 rounded-xl flex flex-col items-center text-center shadow-sm border transition-colors relative">
                @if($hasGroups)
                    <div class="absolute -top-3 -right-3 bg-emerald-500 text-white size-6 rounded-full flex items-center justify-center shadow-md">✓</div>
                @endif
                <div class="h-20 flex items-center {{ $hasGroups ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#1a7a85] dark:text-teal-400' }}">
                    <flux:icon icon="map-pin" class="w-12 h-12 text-current" />
                </div>
                <h3 class="text-lg font-bold {{ $hasGroups ? 'text-emerald-800 dark:text-emerald-300' : 'text-[#0f3a41] dark:text-slate-200' }} mb-2">{{ __('النقاط التعليمية') }}</h3>
                <p class="text-xs {{ $hasGroups ? 'text-emerald-700/80 dark:text-emerald-400/80' : 'text-[#3b5d63] dark:text-slate-400' }} leading-relaxed mb-4 flex-1">{{ __('إدخال وتهيئة بيانات النقاط والمجموعات.') }}</p>
                
                <div class="w-full bg-white/50 dark:bg-zinc-900/50 rounded-lg p-2 mb-4 text-xs font-semibold">
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('نشطة:') }}</span>
                        <span class="{{ $hasGroups ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-700 dark:text-zinc-300' }}">{{ $activeGroupsCount }}</span>
                    </div>
                </div>
                
                <a href="{{ route('student.group.index') }}" wire:navigate class="w-full text-xs font-bold py-2 rounded-lg transition-colors border {{ $hasGroups ? 'bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-white text-[#1a7a85] border-[#1a7a85]/20 hover:bg-[#1a7a85]/10 dark:bg-zinc-800 dark:text-teal-400 dark:border-teal-900/50 hover:dark:bg-zinc-700' }}">
                    {{ __('إدارة') }} &larr;
                </a>
            </div>
            

            {{-- Step 2 --}}
            <div class="bg-[#f4e3d3] dark:bg-orange-900/20 {{ $hasSubjects ? 'border-emerald-500 shadow-emerald-100 ring-2 ring-emerald-500/20' : 'border-transparent' }} p-6 rounded-xl flex flex-col items-center text-center shadow-sm border transition-colors relative">
                @if($hasSubjects)
                    <div class="absolute -top-3 -right-3 bg-emerald-500 text-white size-6 rounded-full flex items-center justify-center shadow-md">✓</div>
                @endif
                <div class="h-20 flex items-center {{ $hasSubjects ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#9c6a3e] dark:text-orange-400' }}">
                    <flux:icon icon="book-open" class="w-12 h-12 text-current" />
                </div>
                <h3 class="text-lg font-bold {{ $hasSubjects ? 'text-emerald-800 dark:text-emerald-300' : 'text-[#4a3421] dark:text-orange-200' }} mb-2">{{ __('المناهج') }}</h3>
                <p class="text-xs {{ $hasSubjects ? 'text-emerald-700/80 dark:text-emerald-400/80' : 'text-[#6b5643] dark:text-orange-300/80' }} leading-relaxed mb-4 flex-1">{{ __('تحديد وإدخال المناهج للمجموعات.') }}</p>
                
                <div class="w-full bg-white/50 dark:bg-zinc-900/50 rounded-lg p-2 mb-4 text-xs font-semibold">
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('مضافة:') }}</span>
                        <span class="{{ $hasSubjects ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-700 dark:text-zinc-300' }}">{{ $SubjectsCounts }}</span>
                    </div>
                </div>
                
                <a href="{{ route('subject.index') }}" wire:navigate class="w-full text-xs font-bold py-2 rounded-lg transition-colors border {{ $hasSubjects ? 'bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-white text-[#9c6a3e] border-[#9c6a3e]/20 hover:bg-[#9c6a3e]/10 dark:bg-zinc-800 dark:text-orange-400 dark:border-orange-900/50 hover:dark:bg-zinc-700' }}">
                    {{ __('إدارة') }} &larr;
                </a>
            </div>

            {{-- Step 3 --}}
            <div class="bg-[#d1e1e6] dark:bg-slate-800/80 {{ $hasStudents ? 'border-emerald-500 shadow-emerald-100 ring-2 ring-emerald-500/20' : 'border-transparent' }} p-6 rounded-xl flex flex-col items-center text-center shadow-sm border transition-colors relative {{ !$hasGroups ? 'opacity-60 cursor-not-allowed' : '' }}">
                @if($hasStudents && $studentsPercentage >= 90)
                    <div class="absolute -top-3 -right-3 bg-emerald-500 text-white size-6 rounded-full flex items-center justify-center shadow-md">✓</div>
                @endif
                <div class="h-20 flex items-center {{ $hasStudents ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#1a7a85] dark:text-teal-400' }}">
                    <flux:icon icon="users" class="w-12 h-12 text-current" />
                </div>
                <h3 class="text-lg font-bold {{ $hasStudents ? 'text-emerald-800 dark:text-emerald-300' : 'text-[#0f3a41] dark:text-slate-200' }} mb-2">{{ __('الطلبة') }}</h3>
                <p class="text-xs {{ $hasStudents ? 'text-emerald-700/80 dark:text-emerald-400/80' : 'text-[#3b5d63] dark:text-slate-400' }} leading-relaxed mb-4 flex-1">{{ __('تسجيل الطلاب وتوزيعهم.') }}</p>
                
                <div class="w-full bg-white/50 dark:bg-zinc-900/50 rounded-lg p-2 mb-4 text-xs font-semibold">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('الإشغال:') }}</span>
                        <span class="{{ $hasStudents ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-700 dark:text-zinc-300' }}">{{ $studentsPercentage }}%</span>
                    </div>
                    <div class="w-full bg-black/10 dark:bg-white/10 rounded-full h-1 overflow-hidden">
                        <div class="bg-[#1a7a85] dark:bg-teal-500 h-1 rounded-full" style="width: {{ min(100, $studentsPercentage) }}%"></div>
                    </div>
                </div>
                
                <a href="{{ route('student.index') }}" wire:navigate class="w-full text-xs font-bold py-2 rounded-lg transition-colors border {{ $hasStudents ? 'bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-white text-[#1a7a85] border-[#1a7a85]/20 hover:bg-[#1a7a85]/10 dark:bg-zinc-800 dark:text-teal-400 dark:border-teal-900/50 hover:dark:bg-zinc-700' }}">
                    {{ __('إدارة') }} &larr;
                </a>
            </div>

            {{-- Step 4 --}}
            <div class="bg-[#f4e3d3] dark:bg-orange-900/20 {{ $hasSurveys ? 'border-emerald-500 shadow-emerald-100 ring-2 ring-emerald-500/20' : 'border-transparent' }} p-6 rounded-xl flex flex-col items-center text-center shadow-sm border transition-colors relative {{ !$hasStudents ? 'opacity-60 cursor-not-allowed' : '' }}">
                @if($hasSurveys && $hasSurveysPersentage >= 99)
                    <div class="absolute -top-3 -right-3 bg-emerald-500 text-white size-6 rounded-full flex items-center justify-center shadow-md">✓</div>
                @endif
                <div class="h-20 flex items-center {{ $hasSurveys ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#9c6a3e] dark:text-orange-400' }}">
                    <flux:icon icon="clipboard-document-check" class="w-12 h-12 text-current" />
                </div>
                <h3 class="text-lg font-bold {{ $hasSurveys ? 'text-emerald-800 dark:text-emerald-300' : 'text-[#4a3421] dark:text-orange-200' }} mb-2">{{ __('التقييم') }}</h3>
                <p class="text-xs {{ $hasSurveys ? 'text-emerald-700/80 dark:text-emerald-400/80' : 'text-[#6b5643] dark:text-orange-300/80' }} leading-relaxed mb-4 flex-1">{{ __('تجهيز نماذج التقييم.') }}</p>
                
                <div class="w-full bg-white/50 dark:bg-zinc-900/50 rounded-lg p-2 mb-4 text-xs font-semibold">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('التجهيز:') }}</span>
                        <span class="{{ $hasSurveys ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-700 dark:text-zinc-300' }}">{{ $hasSurveysPersentage }}%</span>
                    </div>
                    <div class="w-full bg-black/10 dark:bg-white/10 rounded-full h-1 overflow-hidden">
                        <div class="bg-[#9c6a3e] dark:bg-orange-500 h-1 rounded-full" style="width: {{ min(100, $hasSurveysPersentage) }}%"></div>
                    </div>
                </div>
                
                <a href="{{ route('survey.manage') }}" wire:navigate class="w-full text-xs font-bold py-2 rounded-lg transition-colors border {{ $hasSurveys ? 'bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-white text-[#9c6a3e] border-[#9c6a3e]/20 hover:bg-[#9c6a3e]/10 dark:bg-zinc-800 dark:text-orange-400 dark:border-orange-900/50 hover:dark:bg-zinc-700' }}">
                    {{ __('إدارة') }} &larr;
                </a>
            </div>

            {{-- Step 5 --}}
            <div class="bg-[#d1e1e6] dark:bg-slate-800/80 {{ $hasAttendance ? 'border-emerald-500 shadow-emerald-100 ring-2 ring-emerald-500/20' : 'border-transparent' }} p-6 rounded-xl flex flex-col items-center text-center shadow-sm border transition-colors relative {{ !$hasSurveys ? 'opacity-60 cursor-not-allowed' : '' }}">
                @if($hasAttendance && $attendancePercentage >= 99)
                    <div class="absolute -top-3 -right-3 bg-emerald-500 text-white size-6 rounded-full flex items-center justify-center shadow-md">✓</div>
                @endif
                <div class="h-20 flex items-center {{ $hasAttendance ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#1a7a85] dark:text-teal-400' }}">
                    <flux:icon icon="calendar-days" class="w-12 h-12 text-current" />
                </div>
                <h3 class="text-lg font-bold {{ $hasAttendance ? 'text-emerald-800 dark:text-emerald-300' : 'text-[#0f3a41] dark:text-slate-200' }} mb-2">{{ __('المتابعة') }}</h3>
                <p class="text-xs {{ $hasAttendance ? 'text-emerald-700/80 dark:text-emerald-400/80' : 'text-[#3b5d63] dark:text-slate-400' }} leading-relaxed mb-4 flex-1">{{ __('رصد المتابعة والغياب.') }}</p>
                
                <div class="w-full bg-white/50 dark:bg-zinc-900/50 rounded-lg p-2 mb-4 text-xs font-semibold">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('الإنجاز:') }}</span>
                        <span class="{{ $hasAttendance ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-700 dark:text-zinc-300' }}">{{ $attendancePercentage }}%</span>
                    </div>
                    <div class="w-full bg-black/10 dark:bg-white/10 rounded-full h-1 overflow-hidden">
                        <div class="bg-[#1a7a85] dark:bg-teal-500 h-1 rounded-full" style="width: {{ min(100, $attendancePercentage) }}%"></div>
                    </div>
                </div>
                
                <a href="{{ route('reports.groups.attendance') }}" wire:navigate class="w-full text-xs font-bold py-2 rounded-lg transition-colors border {{ $hasAttendance ? 'bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-white text-[#1a7a85] border-[#1a7a85]/20 hover:bg-[#1a7a85]/10 dark:bg-zinc-800 dark:text-teal-400 dark:border-teal-900/50 hover:dark:bg-zinc-700' }}">
                    {{ __('إدارة') }} &larr;
                </a>
            </div>

        </div>
    </div>
</div>
