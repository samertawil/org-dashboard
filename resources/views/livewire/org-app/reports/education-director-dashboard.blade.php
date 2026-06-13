<div class="flex flex-col gap-6" dir="rtl">
    @php
        $selectedSupervisorName = '';
        if ($selectedSupervisorId) {
            $selectedSupervisor = collect($supervisors)->firstWhere('user_id', $selectedSupervisorId);
            if ($selectedSupervisor) {
                $selectedSupervisorName = $selectedSupervisor->full_name;
            } else {
                $selectedSupervisorName = auth()->user()->employee?->full_name ?? auth()->user()->name;
            }
        }
    @endphp

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 print:hidden" dir="ltr">
        <div class="flex flex-col gap-1 text-left">
            <flux:heading level="1" size="xl" class="font-bold text-zinc-900 dark:text-zinc-100">
                @if($selectedSupervisorId)
                    Supervisor Dashboard{{ $selectedSupervisorName ? ' - ' . $selectedSupervisorName : '' }}
                @else
                    Education Director Dashboard
                @endif
            </flux:heading>
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                @if($selectedSupervisorId)
                    Monitor the educational process and key indicators for the selected supervisor
                @else
                    Monitor the educational process and key indicators of the center
                @endif
            </flux:subheading>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <flux:button onclick="window.print()" icon="printer" variant="primary"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium shadow-sm transition-all duration-200">
                Print Report
            </flux:button>
        </div>
    </div>

    <!-- Print Header -->
    <div class="hidden print:block text-center mb-8 border-b pb-4" dir="ltr">
        <h1 class="text-2xl font-bold text-zinc-950">
            @if($selectedSupervisorId)
                Supervisor Dashboard{{ $selectedSupervisorName ? ' - ' . $selectedSupervisorName : '' }}
            @else
                Education Director Dashboard
            @endif
        </h1>
        <p class="text-sm text-zinc-500 mt-2">
            From Date: {{ $dateFrom ?: '-' }} &nbsp;&bull;&nbsp; To Date: {{ $dateTo ?: '-' }}
        </p>
    </div>

    {{-- ============================================================ --}}
    {{-- Educational Progress Report (Basic Statistics)              --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 gap-6" dir="rtl">
        @livewire('org-app.reports.educational-progress')
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm print:hidden"
        dir="ltr">
        <div class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <!-- Date From -->
                <flux:input type="date" wire:model.live.debounce.500ms="dateFrom" label="From Date"
                     class="w-full text-left" />

                <!-- Date To -->
                <flux:input type="date" wire:model.live.debounce.500ms="dateTo" label="To Date"
                     class="w-full text-left" />

                @if ($canSelectSupervisor)
                    <!-- Supervisor Selection -->
                    <div class="flex flex-col gap-1">
                        <flux:label class="text-left">Supervisor</flux:label>
                        <flux:select wire:model.live="selectedSupervisorId" class="w-full text-left">
                            <option value="">-- All (Director View) --</option>
                            @foreach ($supervisors as $sup)
                                <option value="{{ $sup->user_id }}">{{ $sup->full_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                @endif

                <!-- Student Center (Group) -->
                <div class="flex flex-col gap-1">
                    <flux:label class="text-left">Educational Center</flux:label>
                    <flux:select wire:model.live="selectedGroupId" class="w-full text-left">
                        <option value="">-- All Educational Centers --</option>
                        @foreach ($groups as $grp)
                            <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Clear Filters Button -->
                <div class="flex items-end justify-start">
                    @if (
                        $dateFrom !== \Carbon\Carbon::now()->format('Y-m-d') ||
                            $dateTo !== \Carbon\Carbon::now()->format('Y-m-d') ||
                            $selectedGroupId !== '' ||
                            $selectedSupervisorId !== '' ||
                            $selectedBatchNo !== '')
                        <flux:button wire:click="clearFilters" variant="ghost" size="sm" icon="x-mark"
                            class="text-rose-600 hover:text-rose-700 dark:text-rose-400">
                            Clear Filters
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Sections Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative">
        <!-- Backdrop Loading Overlay -->
        <div wire:loading.delay wire:target="dateFrom,dateTo,selectedGroupId,selectedSupervisorId"
            class="absolute inset-0 z-10 bg-white/50 dark:bg-zinc-800/50 backdrop-blur-sm flex items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="size-8 animate-spin text-indigo-600 dark:text-indigo-400" />
        </div>

        <!-- Section 1: KPIs Table (القسم الأول: جدول المؤشرات) -->
        <div class="lg:col-span-2 space-y-6">
            <flux:card class="p-6 overflow-hidden">
                <div class="flex items-center gap-3 border-b pb-4 mb-6 dark:border-zinc-700">
                    <div
                        class="size-10 rounded-xl bg-indigo-150 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <flux:icon name="chart-bar" class="size-5" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-bold">القسم الأول: المؤشرات التعليمية</flux:heading>
                        <flux:subheading>إحصائيات مجمعة وتراكمية حول الأنشطة والحضور</flux:subheading>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr
                                class="border-b border-zinc-200 dark:border-zinc-700 text-sm font-semibold text-zinc-500 dark:text-zinc-400">
                                <th class="pb-3 pt-2 font-bold text-zinc-700 dark:text-zinc-300">المؤشر</th>
                                <th class="pb-3 pt-2 font-bold text-zinc-700 dark:text-zinc-300">طريقة الحساب</th>
                                <th class="pb-3 pt-2 font-bold text-zinc-700 dark:text-zinc-300 text-left">القيمة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 text-sm">
                            <!-- Metric 1 -->
                            <tr
                                class="bg-indigo-50/10 dark:bg-indigo-950/5 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors border-l-4 border-indigo-500">
                                <td class="py-4 px-3 font-bold text-zinc-900 dark:text-zinc-100">إجمالي الأنشطة المنفذة
                                </td>
                                <td class="py-4 text-zinc-550 dark:text-zinc-400">عدد تقارير الأنشطة (تعليم + دعم نفسي +
                                    مهارات وقيم تربوية) في الفترة</td>
                                <td class="py-4 font-bold text-indigo-600 dark:text-indigo-400 text-left text-lg">
                                    {{ $metrics['total_executed'] }}
                                </td>
                            </tr>
                            <!-- Metric 1.1 -->
                            <tr
                                class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors text-xs text-zinc-600 dark:text-zinc-455">
                                <td
                                    class="py-3 pr-8 font-medium text-zinc-850 dark:text-zinc-250 flex items-center gap-1.5">
                                    <span class="text-zinc-400 font-bold">&#8627;</span>
                                    <span>عدد الأنشطة التعليمية</span>
                                </td>
                                <td class="py-3 text-zinc-400">نوع النشاط = تعليم</td>
                                <td class="py-3 font-semibold text-zinc-800 dark:text-zinc-300 text-left text-sm">
                                    {{ $metrics['executed_educational'] }}
                                </td>
                            </tr>
                            <!-- Metric 1.2 -->
                            <tr
                                class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors text-xs text-zinc-600 dark:text-zinc-455">
                                <td
                                    class="py-3 pr-8 font-medium text-zinc-850 dark:text-zinc-250 flex items-center gap-1.5">
                                    <span class="text-zinc-400 font-bold">&#8627;</span>
                                    <span>عدد أنشطة الدعم النفسي</span>
                                </td>
                                <td class="py-3 text-zinc-400">نوع النشاط = دعم نفسي</td>
                                <td class="py-3 font-semibold text-zinc-800 dark:text-zinc-300 text-left text-sm">
                                    {{ $metrics['executed_psychological'] }}
                                </td>
                            </tr>
                            <!-- Metric 1.3 -->
                            <tr
                                class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors text-xs text-zinc-600 dark:text-zinc-455">
                                <td
                                    class="py-3 pr-8 font-medium text-zinc-850 dark:text-zinc-250 flex items-center gap-1.5">
                                    <span class="text-zinc-400 font-bold">&#8627;</span>
                                    <span>عدد أنشطة القيم والمهارات التربوية</span>
                                </td>
                                <td class="py-3 text-zinc-400">نوع النشاط = قيم تربوية</td>
                                <td class="py-3 font-semibold text-zinc-800 dark:text-zinc-300 text-left text-sm">
                                    {{ $metrics['executed_values'] }}
                                </td>
                            </tr>
                            <!-- Metric 2 -->
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="py-4 font-semibold text-zinc-900 dark:text-zinc-100">إجمالي حضور الأطفال
                                    (تراكمي)</td>
                                <td class="py-4 text-zinc-500 dark:text-zinc-400">جمع حقل "عدد الحضور" من كل نشاط منفذ
                                </td>
                                <td class="py-4 font-bold text-emerald-600 dark:text-emerald-400 text-left text-lg">
                                    {{ number_format($metrics['total_attendance']) }}
                                </td>
                            </tr>
                            <!-- Metric 3 -->
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="py-4 font-semibold text-zinc-900 dark:text-zinc-100">متوسط الحضور اليومي</td>
                                <td class="py-4 text-zinc-500 dark:text-zinc-400">إجمالي الحضور ÷ عدد أيام التنفيذ
                                    الفعلية</td>
                                <td class="py-4 font-bold text-purple-600 dark:text-purple-400 text-left text-lg">
                                    {{ number_format($metrics['avg_daily_attendance'], 2) }}
                                </td>
                            </tr>
                            <!-- Metric 4: نسبة الحضور الأسبوعية -->
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="py-4 font-semibold text-zinc-900 dark:text-zinc-100">نسبة الحضور الأسبوعية
                                </td>
                                <td class="py-4 text-zinc-500 dark:text-zinc-400">(إجمالي الحضور الفعلي) ÷ (عدد الأطفال
                                    المسجلين × عدد أيام الأنشطة) × 100</td>
                                <td class="py-4 font-bold text-blue-600 dark:text-blue-400 text-left text-lg">
                                    {{ $metrics['weekly_attendance_rate'] }}%
                                </td>
                            </tr>
                            <!-- Metric 5: نسبة الانسجام الأسبوعية -->
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="py-4 font-semibold text-zinc-900 dark:text-zinc-100">نسبة الانسجام الأسبوعية
                                </td>
                                <td class="py-4 text-zinc-500 dark:text-zinc-400">متوسط "عدد المنسجمين" ÷ متوسط "عدد
                                    الحضور" × 100</td>
                                <td
                                    class="py-4 font-bold text-teal-650 dark:text-teal-400 text-left text-lg flex items-center justify-end gap-2">
                                    <span>{{ $metrics['weekly_harmony_rate'] }}%</span>
                                    @if ($metrics['weekly_harmony_rate'] > 90)
                                        <span title="انسجام ممتاز (> 90%)" class="text-sm">🟢</span>
                                    @elseif ($metrics['weekly_harmony_rate'] >= 75)
                                        <span title="انسجام جيد (75% - 90%)" class="text-sm">🟡</span>
                                    @else
                                        <span title="انسجام منخفض (< 75%)" class="text-sm">🔴</span>
                                    @endif
                                </td>
                            </tr>
                            <!-- Metric 6: عدد الصور المرفوعة -->
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="py-4 font-semibold text-zinc-900 dark:text-zinc-100">عدد الصور المرفوعة</td>
                                <td class="py-4 text-zinc-500 dark:text-zinc-400">عدد ملفات الصور في تقارير الأنشطة
                                </td>
                                <td class="py-4 font-bold text-amber-600 dark:text-amber-400 text-left text-lg">
                                    {{ number_format($metrics['total_images_count']) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </flux:card>
        </div>

        <!-- Section 2 & 3: Placeholders and Active Charts (القسم الثاني والثالث) -->
        <div class="flex flex-col gap-6 lg:col-span-1">
            <!-- Section 2: Daily Attendance vs Absence Chart (القسم الثاني: الحضور والغياب اليومي) -->
            <flux:card class="p-6 flex flex-col gap-4">
                <div class="flex items-center gap-3 border-b pb-4 mb-2 dark:border-zinc-700">
                    <div
                        class="size-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 shrink-0">
                        <flux:icon name="presentation-chart-line" class="size-5" />
                    </div>
                    <div>
                        <flux:heading size="md" class="font-bold">القسم الثاني: الحضور والغياب اليومي
                        </flux:heading>
                        <flux:subheading>مقارنة الحضور والغياب طوال الفترة المحددة</flux:subheading>
                    </div>
                </div>

                <div wire:ignore x-data="{
                    init() {
                            const isDark = document.documentElement.classList.contains('dark');
                            let chart = new ApexCharts(this.$el, {
                                series: this.series,
                                xaxis: { categories: this.labels, labels: { rotate: -45 } },
                                chart: {
                                    type: 'bar',
                                    height: 260,
                                    foreColor: isDark ? '#e4e4e7' : '#374151',
                                    fontFamily: 'inherit',
                                    toolbar: { show: false }
                                },
                                colors: ['#10b981', '#ef4444'], // Green for present, Red for absent
                                plotOptions: {
                                    bar: {
                                        horizontal: false,
                                        columnWidth: '55%',
                                        borderRadius: 4
                                    }
                                },
                                dataLabels: { enabled: false },
                                stroke: { show: true, width: 2, colors: ['transparent'] },
                                legend: { position: 'bottom' },
                                tooltip: { theme: isDark ? 'dark' : 'light' }
                            });
                            chart.render();
                
                            this.$watch('series', (value) => {
                                chart.updateSeries(value);
                            });
                            this.$watch('labels', (value) => {
                                chart.updateOptions({ xaxis: { categories: value } });
                            });
                        },
                        series: @entangle('chartData.series'),
                        labels: @entangle('chartData.labels')
                }" class="min-h-[260px]">
                </div>
            </flux:card>

            <!-- Section 3 -->
            <flux:card
                class="p-6 flex flex-col gap-4 bg-gradient-to-br from-emerald-50/20 to-teal-50/20 dark:from-emerald-950/10 dark:to-teal-950/10 border-emerald-100 dark:border-emerald-900/30">
                <div class="flex items-center gap-3">
                    <div
                        class="size-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                        <flux:icon name="presentation-chart-bar" class="size-5" />
                    </div>
                    <div>
                        <flux:heading size="md" class="font-bold">القسم الثالث</flux:heading>
                        <flux:subheading>سيتم إدراجه لاحقاً للمتابعة والتطوير</flux:subheading>
                    </div>
                </div>
                <div
                    class="border-t border-dashed border-zinc-200 dark:border-zinc-700 pt-4 flex flex-col items-center justify-center py-6 text-center">
                    <div
                        class="size-12 rounded-full border border-dashed border-zinc-350 dark:border-zinc-650 flex items-center justify-center mb-3">
                        <flux:icon name="lock-closed" class="size-6 text-zinc-400" />
                    </div>
                    <span class="text-xs text-zinc-400 dark:text-zinc-500">جاهز للتطوير والاستقرار في المرحلة
                        القادمة</span>
                </div>
            </flux:card>
        </div>
    </div>

    <!-- Section 4: Child Data Analysis (Survey 120) -->
    <div class="grid grid-cols-1 gap-6 relative">
        <!-- Backdrop Loading Overlay for Section 4 -->
        <div wire:loading.delay wire:target="selectedGroupId,selectedBatchNo"
            class="absolute inset-0 z-10 bg-white/50 dark:bg-zinc-800/50 backdrop-blur-sm flex items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="size-8 animate-spin text-indigo-600 dark:text-indigo-400" />
        </div>

        <flux:card class="p-6 overflow-hidden">
            <div class="flex items-center justify-between border-b pb-4 mb-6 dark:border-zinc-700 gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <div
                        class="size-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400">
                        <flux:icon name="users" class="size-5" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-bold">القسم الرابع: تحليل بيانات الأطفال (استبيان
                            120)</flux:heading>
                        <flux:subheading>تحليل إحصائي ديموغرافي واجتماعي مستقل عن الفاصل الزمني ومقيد بالمركز التعليمي
                            المختار والدفعة</flux:subheading>
                    </div>
                </div>
                <!-- Batch Filter Next to Title -->
                <div class="flex items-center gap-2 print:hidden w-full sm:w-auto">
                    <flux:select wire:model.live="selectedBatchNo" class="w-full sm:w-48 text-right"
                        placeholder="البحث حسب الدفعة">
                        <option value="">-- كل الدفعات --</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch }}">{{ $batch }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr
                            class="border-b border-zinc-200 dark:border-zinc-700 text-sm font-semibold text-zinc-500 dark:text-zinc-400">
                            <th class="pb-3 pt-2 font-bold text-zinc-700 dark:text-zinc-300">البيان</th>
                            <th class="pb-3 pt-2 font-bold text-zinc-700 dark:text-zinc-300 text-center">العدد</th>
                            <th class="pb-3 pt-2 font-bold text-zinc-700 dark:text-zinc-300 text-left">النسبة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 text-sm">
                        <!-- Row 1: Total Registered -->
                        <tr
                            class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors font-bold text-zinc-950 dark:text-white bg-teal-50/10 dark:bg-teal-950/5 border-l-4 border-teal-500">
                            <td class="py-4 px-3">إجمالي الأطفال المسجلين</td>
                            <td class="py-4 text-center">
                                {{ number_format($surveyMetrics['total_registered']['count']) }}</td>
                            <td class="py-4 text-left text-teal-600 dark:text-teal-400">
                                {{ $surveyMetrics['total_registered']['pct'] }}%</td>
                        </tr>
                        <!-- Row 2: Age 6-9 -->
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-4 px-3">الأطفال 6–9 سنوات</td>
                            <td class="py-4 text-center">{{ number_format($surveyMetrics['age_6_9']['count']) }}</td>
                            <td class="py-4 text-left text-zinc-600 dark:text-zinc-400">
                                {{ $surveyMetrics['age_6_9']['pct'] }}%</td>
                        </tr>
                        <!-- Row 3: Age 10-12 -->
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-4 px-3">الأطفال 10–12 سنة</td>
                            <td class="py-4 text-center">{{ number_format($surveyMetrics['age_10_12']['count']) }}
                            </td>
                            <td class="py-4 text-left text-zinc-600 dark:text-zinc-400">
                                {{ $surveyMetrics['age_10_12']['pct'] }}%</td>
                        </tr>
                        <!-- Row 4: Male -->
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-4 px-3">الذكور</td>
                            <td class="py-4 text-center">{{ number_format($surveyMetrics['male']['count']) }}</td>
                            <td class="py-4 text-left text-zinc-600 dark:text-zinc-400">
                                {{ $surveyMetrics['male']['pct'] }}%</td>
                        </tr>
                        <!-- Row 5: Female -->
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-4 px-3">الإناث</td>
                            <td class="py-4 text-center">{{ number_format($surveyMetrics['female']['count']) }}</td>
                            <td class="py-4 text-left text-zinc-600 dark:text-zinc-400">
                                {{ $surveyMetrics['female']['pct'] }}%</td>
                        </tr>
                        <!-- Row 6: E-Learning -->
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-4 px-3">يتابع التعليم الالكتروني</td>
                            <td class="py-4 text-center">{{ number_format($surveyMetrics['elearning']['count']) }}
                            </td>
                            <td class="py-4 text-left text-zinc-600 dark:text-zinc-400">
                                {{ $surveyMetrics['elearning']['pct'] }}%</td>
                        </tr>
                        <!-- Row 7: Face-to-Face -->
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-4 px-3">يتابع التعليم الوجاهي</td>
                            <td class="py-4 text-center">{{ number_format($surveyMetrics['face_to_face']['count']) }}
                            </td>
                            <td class="py-4 text-left text-zinc-600 dark:text-zinc-400">
                                {{ $surveyMetrics['face_to_face']['pct'] }}%</td>
                        </tr>
                        <!-- Row 8: War-injured -->
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-4 px-3">الطفل مصاب حرب</td>
                            <td class="py-4 text-center">{{ number_format($surveyMetrics['war_injured']['count']) }}
                            </td>
                            <td class="py-4 text-left text-zinc-600 dark:text-zinc-400">
                                {{ $surveyMetrics['war_injured']['pct'] }}%</td>
                        </tr>
                        <!-- Row 9: Displaced -->
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-4 px-3">الأسر النازحة</td>
                            <td class="py-4 text-center">{{ number_format($surveyMetrics['displaced']['count']) }}
                            </td>
                            <td class="py-4 text-left text-zinc-600 dark:text-zinc-400">
                                {{ $surveyMetrics['displaced']['pct'] }}%</td>
                        </tr>
                        <!-- Row 10: Orphans -->
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-4 px-3">الأطفال الايتام</td>
                            <td class="py-4 text-center">{{ number_format($surveyMetrics['orphan']['count']) }}</td>
                            <td class="py-4 text-left text-zinc-600 dark:text-zinc-400">
                                {{ $surveyMetrics['orphan']['pct'] }}%</td>
                        </tr>
                        <!-- Row 11: Health issues -->
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="py-4 px-3">أطفال يعانون مشاكل صحية / إعاقة</td>
                            <td class="py-4 text-center">{{ number_format($surveyMetrics['health_issues']['count']) }}
                            </td>
                            <td class="py-4 text-left text-zinc-600 dark:text-zinc-400">
                                {{ $surveyMetrics['health_issues']['pct'] }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>


    <!-- ============================================================ -->
    <!-- Section 5: Supervisor Reports (التقارير المقدمة من المشرف) -->
    <!-- ============================================================ -->
    <div x-data="{
        modalOpen: false,
        modalReport: null,
        contentModalOpen: false,
        contentModalText: '',
        contentModalTitle: '',
        openModal(report) {
            this.modalReport = report;
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
            this.modalReport = null;
        },
        openContentModal(title, text) {
            this.contentModalTitle = title;
            this.contentModalText = text;
            this.contentModalOpen = true;
        },
        closeContentModal() {
            this.contentModalOpen = false;
            this.contentModalText = '';
            this.contentModalTitle = '';
        },
        get allAttachments() {
            if (!this.modalReport || !this.modalReport.bodies) return [];
            let atts = [];
            this.modalReport.bodies.forEach(b => {
                if (b.parsed_attachments && b.parsed_attachments.length) {
                    atts = atts.concat(b.parsed_attachments);
                }
            });
            return atts;
        }
    }" class="grid grid-cols-1 gap-6 relative">

        <flux:card class="p-6 overflow-hidden">
            <!-- Section Header + Filters -->
            <div class="flex flex-col gap-4 border-b pb-5 mb-6 dark:border-zinc-700">
                <div class="flex items-center gap-3">
                    <div
                        class="size-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400 shrink-0">
                        <flux:icon name="document-text" class="size-5" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-bold">القسم الخامس: تقارير المشرفين</flux:heading>
                        <flux:subheading>التقارير المقدمة إلى الإدارة من المشرفين الميدانيين</flux:subheading>
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="flex flex-wrap items-end gap-3 print:hidden">
                    <div class="flex flex-col gap-1 min-w-48">
                        <flux:label class="text-right text-xs">المركز التعليمي</flux:label>
                        <flux:select wire:model.live="reportSearchGroup" class="w-full text-right text-sm">
                            <option value="">-- كل المراكز --</option>
                            @foreach ($groups as $grp)
                                <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex flex-col gap-1 min-w-36">
                        <flux:label class="text-right text-xs">الدفعة</flux:label>
                        <flux:select wire:model.live="reportSearchBatch" class="w-full text-right text-sm">
                            <option value="">-- كل الدفعات --</option>
                            @foreach ($batches as $batch)
                                <option value="{{ $batch }}">{{ $batch }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    @if ($reportSearchGroup !== '' || $reportSearchBatch !== '')
                        <flux:button wire:click="$set('reportSearchGroup', ''); $set('reportSearchBatch', '')"
                            variant="ghost" size="sm" icon="x-mark"
                            class="text-rose-600 hover:text-rose-700 dark:text-rose-400 self-end">
                            مسح
                        </flux:button>
                    @endif

                    <div class="flex items-end gap-1 mr-auto">
                        <span
                            class="inline-flex items-center gap-1 text-xs bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 px-2.5 py-1 rounded-full font-medium">
                            <flux:icon name="document-text" class="size-3.5" />
                            {{ $supervisorReports->count() }} تقرير
                        </span>
                    </div>
                </div>
            </div>

            <!-- Loading Overlay for Section 5 -->
            <div wire:loading.delay wire:target="reportSearchGroup,reportSearchBatch"
                class="absolute inset-0 z-10 bg-white/60 dark:bg-zinc-800/60 backdrop-blur-sm flex items-center justify-center rounded-xl">
                <flux:icon name="arrow-path" class="size-8 animate-spin text-orange-500 dark:text-orange-400" />
            </div>

            @if ($supervisorReports->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div
                        class="size-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-4">
                        <flux:icon name="document-text" class="size-8 text-zinc-400" />
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 font-medium">لا توجد تقارير مطابقة للفلترة الحالية</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">جرّب تغيير فلتر المركز أو الدفعة</p>
                </div>
            @else
                {{-- A. Mobile Card View --}}
                <div class="block md:hidden divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach ($supervisorReports as $report)
                        @php
                            $firstBody = $report->bodies->first();
                            $totalAtts = $report->bodies->sum(fn($b) => count($b->parsed_attachments));
                            $domainColors = [
                                'التعليم' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                'الدعم النفسي' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                'مهارات وقيم تربوية' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                            ];
                            $domainClass = $domainColors[$report->domain_name] ?? 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300';
                            $modalData = json_encode([
                                'id' => $report->id,
                                'name' => $report->report_name,
                                'date' => $report->report_date,
                                'bodies' => $report->bodies->map(fn($b) => [
                                    'content' => $b->content,
                                    'observation' => $b->observation,
                                    'parsed_attachments' => $b->parsed_attachments,
                                ])->values()->toArray(),
                            ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
                        @endphp
                        <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex flex-col">
                                    <span class="text-xs text-zinc-400 font-mono">#{{ $report->id }}</span>
                                    <span class="text-sm font-bold text-zinc-900 dark:text-white leading-snug">{{ $report->activity_name ?? '—' }}</span>
                                    @if ($report->domain_name)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $domainClass }}">
                                                {{ $report->domain_name }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-xs">
                                <div>
                                    <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-1">المركز التعليمي</span>
                                    <div class="text-xs text-zinc-650 dark:text-zinc-350 leading-tight font-medium">
                                        @forelse ($report->group_names as $gname)
                                            <span class="block">{{ $gname }}</span>
                                        @empty
                                            <span>—</span>
                                        @endforelse
                                    </div>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-1">فترة التقرير</span>
                                    <div class="text-xs text-zinc-650 dark:text-zinc-350 leading-normal font-medium">
                                        من: {{ \Carbon\Carbon::parse($report->date_from)->format('Y-m-d') }}<br>
                                        إلى: {{ \Carbon\Carbon::parse($report->date_to)->format('Y-m-d') }}
                                    </div>
                                </div>
                            </div>

                            @if ($firstBody && $firstBody->content)
                                <div class="bg-zinc-50 dark:bg-zinc-900/30 p-3 rounded-lg border border-zinc-200/50 dark:border-zinc-700/50 text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed whitespace-pre-wrap">
                                    <span class="font-bold text-[10px] text-zinc-400 block mb-1">المحتوى</span>
                                    {{ $firstBody->content }}
                                </div>
                            @endif

                            @if ($firstBody && $firstBody->observation)
                                <div class="bg-amber-50 dark:bg-amber-950/20 p-3 rounded-lg border border-amber-200/50 dark:border-amber-900/30 text-xs text-amber-800 dark:text-amber-300 leading-relaxed italic whitespace-pre-wrap">
                                    <span class="font-bold text-[10px] text-amber-900 dark:text-amber-400 block mb-1 not-italic">الملاحظة</span>
                                    {{ $firstBody->observation }}
                                </div>
                            @endif

                            @if ($totalAtts > 0)
                                <div class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800/50">
                                    <button @click="openModal({{ $modalData }})"
                                        class="inline-flex items-center gap-1.5 bg-orange-100 hover:bg-orange-200 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-300 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                                        <flux:icon name="photo" class="size-4" />
                                        <span>المرفقات ({{ $totalAtts }})</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- B. Desktop Sticky Table View --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="border-b-2 border-zinc-200 dark:border-zinc-700">
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">#</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">اسم النشاط</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">المجال</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">المركز التعليمي</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300">المحتوى</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">فترة التقرير</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 text-center whitespace-nowrap">التفاصيل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($supervisorReports as $report)
                                @php
                                    $firstBody = $report->bodies->first();
                                    $totalAtts = $report->bodies->sum(fn($b) => count($b->parsed_attachments));
                                    $domainColors = [
                                        'التعليم' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                        'الدعم النفسي' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                        'مهارات وقيم تربوية' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                    ];
                                    $domainClass = $domainColors[$report->domain_name] ?? 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300';
                                    $modalData = json_encode([
                                        'id' => $report->id,
                                        'name' => $report->report_name,
                                        'date' => $report->report_date,
                                        'bodies' => $report->bodies->map(fn($b) => [
                                            'content' => $b->content,
                                            'observation' => $b->observation,
                                            'parsed_attachments' => $b->parsed_attachments,
                                        ])->values()->toArray(),
                                    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
                                @endphp
                                <tr class="hover:bg-orange-50/30 dark:hover:bg-orange-950/10 transition-colors">
                                    <td class="py-3 px-3 text-zinc-400 font-mono text-xs">{{ $report->id }}</td>
                                    <td class="py-3 px-3 font-medium text-zinc-900 dark:text-zinc-100 min-w-[600px]">
                                        <span class="leading-snug break-words">{{ $report->activity_name ?? '—' }}</span>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if ($report->domain_name)
                                            <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full {{ $domainClass }}">
                                                {{ $report->domain_name }}
                                            </span>
                                        @else
                                            <span class="text-zinc-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 max-w-[200px]">
                                        @forelse ($report->group_names as $gname)
                                            <span class="block text-xs text-zinc-700 dark:text-zinc-300 leading-snug line-clamp-2">{{ $gname }}</span>
                                        @empty
                                            <span class="text-zinc-400">—</span>
                                        @endforelse
                                    </td>
                                    <td class="py-3 px-3 max-w-[90px]">
                                        @if ($firstBody && $firstBody->content)
                                            <div>
                                                <p class="text-zinc-700 dark:text-zinc-300 text-xs leading-relaxed line-clamp-2">
                                                    {{ $firstBody->content }}
                                                </p>
                                                @if (mb_strlen($firstBody->content) > 50)
                                                    <button @click="openContentModal($el.getAttribute('data-title'), $el.getAttribute('data-content'))"
                                                        data-title="{{ $report->activity_name ?? 'تفاصيل المحتوى' }}"
                                                        data-content="{{ $firstBody->content }}"
                                                        class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-semibold mt-1 cursor-pointer focus:outline-none block">
                                                        قراءة المزيد
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-zinc-400 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap text-zinc-500 dark:text-zinc-400 text-xs leading-normal">
                                        <div class="flex flex-col gap-0.5 font-medium">
                                            <span>من: {{ \Carbon\Carbon::parse($report->date_from)->format('Y-m-d') }}</span>
                                            <span>إلى: {{ \Carbon\Carbon::parse($report->date_to)->format('Y-m-d') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($firstBody && $firstBody->observation)
                                                <button @click="openContentModal($el.getAttribute('data-title'), $el.getAttribute('data-obs'))"
                                                    data-title="ملاحظة: {{ $report->activity_name ?? 'النشاط' }}"
                                                    data-obs="{{ $firstBody->observation }}"
                                                    class="inline-flex items-center gap-1 bg-amber-100 hover:bg-amber-200 dark:bg-amber-900/30 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-300 text-xs font-medium px-2.5 py-1 rounded-lg transition-colors cursor-pointer"
                                                    title="الملاحظة">
                                                    <flux:icon name="chat-bubble-left-right" class="size-3.5" />
                                                    <span>الملاحظة</span>
                                                </button>
                                            @endif

                                            @if ($totalAtts > 0)
                                                <button @click="openModal({{ $modalData }})"
                                                    class="inline-flex items-center gap-1 bg-orange-100 hover:bg-orange-200 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-300 text-xs font-medium px-2.5 py-1 rounded-lg transition-colors cursor-pointer"
                                                    title="المرفقات">
                                                    <flux:icon name="photo" class="size-3.5" />
                                                    <span>المرفقات ({{ $totalAtts }})</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($supervisorReports->hasPages())
                <div class="mt-6 border-t border-zinc-200 dark:border-zinc-700 pt-4 print:hidden">
                    {{ $supervisorReports->links() }}
                </div>
            @endif
        </flux:card>

        <!-- ===== Attachments Modal ===== -->
        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @keydown.escape.window="closeModal()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            style="display:none">
            <div x-show="modalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" @click.stop
                class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col overflow-hidden">
                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                    <div class="flex items-center gap-3">
                        <div
                            class="size-9 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                            <flux:icon name="photo" class="size-4" />
                        </div>
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-zinc-100 text-base"
                                x-text="modalReport?.name ?? ''"></h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400"
                                x-text="'تاريخ التقرير: ' + (modalReport?.date ?? '')"></p>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        class="size-8 rounded-lg flex items-center justify-center text-zinc-500 hover:text-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="overflow-y-auto flex-1 p-6">
                    <template x-if="allAttachments.length === 0">
                        <div class="flex flex-col items-center justify-center py-12 text-zinc-400">
                            <flux:icon name="photo" class="size-12 mb-3 opacity-30" />
                            <p class="text-sm">لا توجد مرفقات</p>
                        </div>
                    </template>

                    <template x-if="allAttachments.length > 0">
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4"
                                x-text="allAttachments.length + ' مرفق'"></p>
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                <template x-for="(att, idx) in allAttachments" :key="idx">
                                    <div
                                        class="group relative rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 aspect-square max-h-24">
                                        <template
                                            x-if="['jpg','jpeg','png','gif','webp','svg'].includes((att.extension ?? '').toLowerCase())">
                                            <a :href="'/storage/' + att.path" target="_blank" rel="noopener"
                                                class="block w-full h-full">
                                                <img :src="'/storage/' + att.path" :alt="att.name"
                                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                                    loading="lazy" />
                                                <div
                                                    class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                    <div class="bg-white/90 rounded-full p-1.5">
                                                        <svg class="size-3 text-zinc-700" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </a>
                                        </template>
                                        <template
                                            x-if="!['jpg','jpeg','png','gif','webp','svg'].includes((att.extension ?? '').toLowerCase())">
                                            <a :href="'/storage/' + att.path" target="_blank" rel="noopener"
                                                class="flex flex-col items-center justify-center h-full gap-1 text-zinc-500 hover:text-indigo-600 transition-colors p-2">
                                                <svg class="size-7" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="text-[10px] text-center break-all line-clamp-1"
                                                    x-text="att.name"></span>
                                                <span
                                                    class="text-[10px] font-mono uppercase bg-zinc-200 dark:bg-zinc-700 px-1 py-0.5 rounded"
                                                    x-text="att.extension"></span>
                                            </a>
                                        </template>
                                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[10px] px-1.5 py-0.5 translate-y-full group-hover:translate-y-0 transition-transform duration-200 truncate"
                                            x-text="att.name"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ===== Content Modal ===== -->
        <div x-show="contentModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @keydown.escape.window="closeContentModal()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            style="display:none">
            <div x-show="contentModalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" @click.stop
                class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col overflow-hidden text-right"
                dir="rtl">
                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                    <div class="flex items-center gap-3">
                        <div
                            class="size-9 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <flux:icon name="document-text" class="size-4" />
                        </div>
                        <div class="text-right">
                            <h3 class="font-bold text-zinc-900 dark:text-zinc-100 text-base">محتوى التقرير</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5" x-text="contentModalTitle"></p>
                        </div>
                    </div>
                    <button @click="closeContentModal()"
                        class="size-8 rounded-lg flex items-center justify-center text-zinc-500 hover:text-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="overflow-y-auto flex-1 p-6 text-right">
                    <p class="text-zinc-800 dark:text-zinc-200 text-sm leading-relaxed whitespace-pre-line"
                        x-text="contentModalText"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 6: Survey Assessment Stats (pre/post per batch)       --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 gap-6 relative" dir="rtl">
        {{-- Loading Overlay --}}
        <div wire:loading.delay wire:target="surveyBatchNo"
            class="absolute inset-0 z-10 bg-white/50 dark:bg-zinc-800/50 backdrop-blur-sm flex items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="size-8 animate-spin text-violet-600 dark:text-violet-400" />
        </div>

        <flux:card class="p-6 overflow-hidden">
            {{-- Section Header --}}
            <div class="flex flex-col gap-4 border-b pb-5 mb-6 dark:border-zinc-700">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between flex-wrap">
                    <div class="flex items-center gap-3">
                        <div
                            class="size-10 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center text-violet-600 dark:text-violet-400 shrink-0">
                            <flux:icon name="clipboard-document-check" class="size-5" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="font-bold">القسم السادس: إحصائيات التقييم القبلي والبعدي
                            </flux:heading>
                            <flux:subheading>مقارنة نسب الاستجابة لكل نوع تقييم وفئة عمرية حسب الدفعة
                            </flux:subheading>
                        </div>
                    </div>

                    {{-- Batch Filter --}}
                    <div class="flex items-center gap-2 print:hidden w-full sm:w-auto">
                        <flux:select wire:model.live="surveyBatchNo" class="w-full sm:w-48 text-right"
                            placeholder="كل الدفعات">
                            <option value="">-- كل الدفعات --</option>
                            @foreach ($batches as $batch)
                                <option value="{{ $batch }}">{{ $batch }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </div>

            @if (empty($surveyAssessmentStats))
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div
                        class="size-16 rounded-full bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center mb-4">
                        <flux:icon name="clipboard-document-check" class="size-8 text-violet-400" />
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 font-medium">لا توجد بيانات استبيانات متاحة</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">تأكد من وجود استبيانات قبلية وبعدية
                        مُعرَّفة في النظام</p>
                </div>
            @else
                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="border-b-2 border-zinc-200 dark:border-zinc-700 text-xs">
                                <th
                                    class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">
                                    نوع التقييم</th>
                                <th
                                    class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">
                                    الفئة المستهدفة</th>
                                <th
                                    class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 text-center whitespace-nowrap">
                                    العدد المستهدف</th>
                                <th
                                    class="pb-3 pt-1 px-3 font-bold text-blue-700 dark:text-blue-300 text-center whitespace-nowrap border-r border-zinc-200 dark:border-zinc-700">
                                    تم تقييمهم قبلياً</th>
                                <th
                                    class="pb-3 pt-1 px-3 font-bold text-blue-700 dark:text-blue-300 text-center whitespace-nowrap">
                                    النسبة</th>
                                <th
                                    class="pb-3 pt-1 px-3 font-bold text-emerald-700 dark:text-emerald-300 text-center whitespace-nowrap border-r border-zinc-200 dark:border-zinc-700">
                                    تم تقييمهم بعدياً</th>
                                <th
                                    class="pb-3 pt-1 px-3 font-bold text-emerald-700 dark:text-emerald-300 text-center whitespace-nowrap">
                                    النسبة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($surveyAssessmentStats as $row)
                                @php
                                    $ageLabel = ($row['from_age'] !== null && $row['to_age'] !== null)
                                        ? 'أطفال ' . $row['from_age'] . '-' . $row['to_age']
                                        : ($row['target_name'] ?? '—');

                                    $preColor = $row['pre_rate'] === null ? 'text-zinc-400'
                                        : ($row['pre_rate'] >= 80 ? 'text-emerald-600 dark:text-emerald-400'
                                        : ($row['pre_rate'] >= 50 ? 'text-amber-600 dark:text-amber-400'
                                        : 'text-red-600 dark:text-red-400'));

                                    $postColor = $row['post_rate'] === null ? 'text-zinc-400'
                                        : ($row['post_rate'] >= 80 ? 'text-emerald-600 dark:text-emerald-400'
                                        : ($row['post_rate'] >= 50 ? 'text-amber-600 dark:text-amber-400'
                                        : 'text-red-600 dark:text-red-400'));
                                @endphp
                                <tr
                                    class="hover:bg-violet-50/30 dark:hover:bg-violet-950/10 transition-colors group">
                                    {{-- نوع التقييم --}}
                                    <td class="py-3 px-3 font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $row['short_name'] }}
                                    </td>

                                    {{-- الفئة المستهدفة --}}
                                    <td class="py-3 px-3">
                                        <span
                                            class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 whitespace-nowrap">
                                            {{ $ageLabel }}
                                        </span>
                                    </td>

                                    {{-- العدد المستهدف --}}
                                    <td class="py-3 px-3 text-center">
                                        <span class="font-bold text-zinc-800 dark:text-zinc-200 text-base">
                                            {{ number_format($row['target_count']) }}
                                        </span>
                                    </td>

                                    {{-- التقييم القبلي: العدد فقط --}}
                                    <td
                                        class="py-3 px-3 border-r border-zinc-100 dark:border-zinc-800 text-center">
                                        @if ($row['pre_name'])
                                            <span class="font-bold text-blue-700 dark:text-blue-300 text-base">
                                                {{ number_format($row['pre_count']) }}
                                            </span>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600 text-xs">—</span>
                                        @endif
                                    </td>

                                    {{-- نسبة القبلي --}}
                                    <td class="py-3 px-3 text-center">
                                        @if ($row['pre_rate'] !== null)
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="font-bold text-sm {{ $preColor }}">
                                                    {{ $row['pre_rate'] }}%
                                                </span>
                                                <div
                                                    class="w-16 bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 overflow-hidden">
                                                    <div class="h-1.5 rounded-full transition-all duration-500 {{ $row['pre_rate'] >= 80 ? 'bg-emerald-500' : ($row['pre_rate'] >= 50 ? 'bg-amber-400' : 'bg-red-500') }}"
                                                        style="width: {{ min($row['pre_rate'], 100) }}%"></div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600 text-xs">—</span>
                                        @endif
                                    </td>

                                    {{-- التقييم البعدي: العدد فقط --}}
                                    <td
                                        class="py-3 px-3 border-r border-zinc-100 dark:border-zinc-800 text-center">
                                        @if ($row['post_name'])
                                            <span class="font-bold text-emerald-700 dark:text-emerald-300 text-base">
                                                {{ number_format($row['post_count']) }}
                                            </span>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600 text-xs">—</span>
                                        @endif
                                    </td>

                                    {{-- نسبة البعدي --}}
                                    <td class="py-3 px-3 text-center">
                                        @if ($row['post_rate'] !== null)
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="font-bold text-sm {{ $postColor }}">
                                                    {{ $row['post_rate'] }}%
                                                </span>
                                                <div
                                                    class="w-16 bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 overflow-hidden">
                                                    <div class="h-1.5 rounded-full transition-all duration-500 {{ $row['post_rate'] >= 80 ? 'bg-emerald-500' : ($row['post_rate'] >= 50 ? 'bg-amber-400' : 'bg-red-500') }}"
                                                        style="width: {{ min($row['post_rate'], 100) }}%"></div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card View --}}
                <div class="block md:hidden divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach ($surveyAssessmentStats as $row)
                        @php
                            $ageLabel = ($row['from_age'] !== null && $row['to_age'] !== null)
                                ? 'أطفال ' . $row['from_age'] . '-' . $row['to_age']
                                : ($row['target_name'] ?? '—');

                            $preColor = $row['pre_rate'] === null ? 'text-zinc-400'
                                : ($row['pre_rate'] >= 80 ? 'text-emerald-600 dark:text-emerald-400'
                                : ($row['pre_rate'] >= 50 ? 'text-amber-600 dark:text-amber-400'
                                : 'text-red-600 dark:text-red-400'));

                            $postColor = $row['post_rate'] === null ? 'text-zinc-400'
                                : ($row['post_rate'] >= 80 ? 'text-emerald-600 dark:text-emerald-400'
                                : ($row['post_rate'] >= 50 ? 'text-amber-600 dark:text-amber-400'
                                : 'text-red-600 dark:text-red-400'));
                        @endphp
                        <div class="p-4 space-y-3">
                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-sm font-bold text-zinc-900 dark:text-white">{{ $row['short_name'] }}</span>
                                    <span
                                        class="inline-flex w-fit items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300">
                                        {{ $ageLabel }}
                                    </span>
                                </div>
                                <div class="text-left">
                                    <span class="text-[10px] text-zinc-400 block">العدد المستهدف</span>
                                    <span
                                        class="font-bold text-zinc-800 dark:text-zinc-200 text-base">{{ number_format($row['target_count']) }}</span>
                                </div>
                            </div>

                            {{-- Pre/Post Grid --}}
                            <div class="grid grid-cols-2 gap-3">
                                {{-- القبلي --}}
                                <div
                                    class="bg-blue-50 dark:bg-blue-950/20 rounded-xl p-3 border border-blue-100 dark:border-blue-900/40">
                                    <span
                                        class="text-[10px] font-bold text-blue-600 dark:text-blue-400 block mb-1 uppercase tracking-wider">قبلي</span>
                                    @if ($row['pre_name'])
                                        <div class="flex items-baseline gap-1">
                                            <span
                                                class="font-bold text-blue-700 dark:text-blue-300 text-base">{{ number_format($row['pre_count']) }}</span>
                                            <span class="text-xs {{ $preColor }} font-semibold">
                                                @if ($row['pre_rate'] !== null)
                                                    ({{ $row['pre_rate'] }}%)
                                                @else
                                                    (—)
                                                @endif
                                            </span>
                                        </div>
                                        @if ($row['pre_rate'] !== null)
                                            <div class="w-full bg-blue-200 dark:bg-blue-900 rounded-full h-1.5 mt-2 overflow-hidden">
                                                <div class="h-1.5 rounded-full {{ $row['pre_rate'] >= 80 ? 'bg-emerald-500' : ($row['pre_rate'] >= 50 ? 'bg-amber-400' : 'bg-red-500') }}"
                                                    style="width: {{ min($row['pre_rate'], 100) }}%"></div>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-zinc-400 text-xs">لا يوجد</span>
                                    @endif
                                </div>

                                {{-- البعدي --}}
                                <div
                                    class="bg-emerald-50 dark:bg-emerald-950/20 rounded-xl p-3 border border-emerald-100 dark:border-emerald-900/40">
                                    <span
                                        class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 block mb-1 uppercase tracking-wider">بعدي</span>
                                    @if ($row['post_name'])
                                        <div class="flex items-baseline gap-1">
                                            <span
                                                class="font-bold text-emerald-700 dark:text-emerald-300 text-base">{{ number_format($row['post_count']) }}</span>
                                            <span class="text-xs {{ $postColor }} font-semibold">
                                                @if ($row['post_rate'] !== null)
                                                    ({{ $row['post_rate'] }}%)
                                                @else
                                                    (—)
                                                @endif
                                            </span>
                                        </div>
                                        @if ($row['post_rate'] !== null)
                                            <div class="w-full bg-emerald-200 dark:bg-emerald-900 rounded-full h-1.5 mt-2 overflow-hidden">
                                                <div class="h-1.5 rounded-full {{ $row['post_rate'] >= 80 ? 'bg-emerald-500' : ($row['post_rate'] >= 50 ? 'bg-amber-400' : 'bg-red-500') }}"
                                                    style="width: {{ min($row['post_rate'], 100) }}%"></div>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-zinc-400 text-xs">لا يوجد</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>
    </div>

    {{-- Coordinator Report (Late Teachers) --}}
    @if (\App\Services\SupervisorService::isSupervisor(auth()->user()))
        <div class="mt-6" dir="rtl">
            <flux:card class="p-6 overflow-hidden">
                <div class="flex items-center gap-3 border-b pb-4 mb-6 dark:border-zinc-700">
                    <div class="size-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <flux:icon name="exclamation-triangle" class="size-5" />
                    </div>
                    <div class="text-right">
                        <flux:heading size="lg" class="font-bold">المعلمون المتأخرون عن تقديم التقارير</flux:heading>
                        <flux:subheading>أسماء المعلمين وعدد التقارير المتأخرة المطلوبة منهم</flux:subheading>
                    </div>
                </div>

                @if (empty($lateTeachers))
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="size-16 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center mb-4">
                            <flux:icon name="check-circle" class="size-8 text-emerald-500" />
                        </div>
                        <p class="text-zinc-500 dark:text-zinc-400 font-medium">كل المعلمين قاموا بتسليم جميع التقارير المطلوبة</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-zinc-700 text-xs">
                                    <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300">اسم المعلم</th>
                                    <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 text-center">عدد التقارير المتأخرة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @foreach ($lateTeachers as $teacher)
                                    <tr class="hover:bg-amber-50/10 dark:hover:bg-amber-950/5 transition-colors">
                                        <td class="py-3.5 px-3 font-semibold text-zinc-900 dark:text-zinc-100">
                                            {{ $teacher['employee_name'] }}
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <flux:badge size="sm" color="red" class="font-bold rounded-full">
                                                {{ $teacher['delayed_count'] }}
                                            </flux:badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </flux:card>
        </div>
    @endif

    {{-- Manager Report (Lazy-loaded Educational Tasks Stats for Highest Batch) --}}
    @if (auth()->user()->can('select.any.student'))
        <div class="mt-6" dir="rtl">
            @livewire('org-app.reports.educational-tasks-stats', ['onlyHighestBatch' => true, 'lazy' => true])
        </div>
    @endif

    {{-- Groups Attendance (Lazy-loaded with Date Filters inherited from Dashboard) --}}
    @if (\Illuminate\Support\Facades\Gate::allows('reports.all') || \Illuminate\Support\Facades\Gate::allows('reports.groups.attendance') || \Illuminate\Support\Facades\Gate::allows('student.group.date.students'))
        <div class="mt-6" dir="rtl">
            @livewire('org-app.reports.groups-attendance', [
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'isLazy' => true
            ], key('groups-attendance-' . $dateFrom . '-' . $dateTo))
        </div>
    @endif

    @assets
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endassets
</div>
