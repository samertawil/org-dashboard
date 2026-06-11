<div class="flex flex-col gap-6" dir="rtl">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 print:hidden">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl" class="font-bold text-zinc-900 dark:text-zinc-100">لوحة تحكم مدير
                التعليم</flux:heading>
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">متابعة مجريات العملية التعليمية والمؤشرات الأساسية
                للمركز</flux:subheading>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <flux:button onclick="window.print()" icon="printer" variant="primary"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium shadow-sm transition-all duration-200">
                طباعة التقرير
            </flux:button>
        </div>
    </div>

    <!-- Print Header -->
    <div class="hidden print:block text-center mb-8 border-b pb-4">
        <h1 class="text-2xl font-bold text-zinc-950">لوحة تحكم مدير التعليم</h1>
        <p class="text-sm text-zinc-500 mt-2">
            من تاريخ: {{ $dateFrom ?: '-' }} &nbsp;&bull;&nbsp; إلى تاريخ: {{ $dateTo ?: '-' }}
        </p>
    </div>

    <!-- Filters Section -->
    <div
        class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm print:hidden">
        <div class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Date From -->
                <flux:input type="date" wire:model.live.debounce.500ms="dateFrom" label="من تاريخ"
                    class="w-full text-right" />

                <!-- Date To -->
                <flux:input type="date" wire:model.live.debounce.500ms="dateTo" label="إلى تاريخ"
                    class="w-full text-right" />

                <!-- Student Center (Group) -->
                <div class="flex flex-col gap-1">
                    <flux:label class="text-right">المركز التعليمي</flux:label>
                    <flux:select wire:model.live="selectedGroupId" class="w-full text-right">
                        <option value="">-- كل المراكز التعليمية --</option>
                        @foreach ($groups as $grp)
                            <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Clear Filters Button -->
                <div class="flex items-end justify-start">
                    @if (
                        $dateFrom !== \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') ||
                            $dateTo !== \Carbon\Carbon::now()->format('Y-m-d') ||
                            $selectedGroupId !== '' ||
                            $selectedBatchNo !== '')
                        <flux:button wire:click="clearFilters" variant="ghost" size="sm" icon="x-mark"
                            class="text-rose-600 hover:text-rose-700 dark:text-rose-400">
                            مسح الفلترة
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Sections Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative">
        <!-- Backdrop Loading Overlay -->
        <div wire:loading.delay wire:target="dateFrom,dateTo,selectedGroupId"
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
            openModal(report) {
                this.modalReport = report;
                this.modalOpen = true;
            },
            closeModal() {
                this.modalOpen = false;
                this.modalReport = null;
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
        }"
        class="grid grid-cols-1 gap-6 relative">

        <flux:card class="p-6 overflow-hidden">
            <!-- Section Header + Filters -->
            <div class="flex flex-col gap-4 border-b pb-5 mb-6 dark:border-zinc-700">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400 shrink-0">
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
                        <span class="inline-flex items-center gap-1 text-xs bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 px-2.5 py-1 rounded-full font-medium">
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
                    <div class="size-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-4">
                        <flux:icon name="document-text" class="size-8 text-zinc-400" />
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 font-medium">لا توجد تقارير مطابقة للفلترة الحالية</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">جرّب تغيير فلتر المركز أو الدفعة</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="border-b-2 border-zinc-200 dark:border-zinc-700">
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">#</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">اسم النشاط</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">المجال</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">المركز التعليمي</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300">المحتوى</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300">الملاحظة</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">تاريخ التقرير</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 text-center whitespace-nowrap">المرفقات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($supervisorReports as $report)
                                @php
                                    $firstBody = $report->bodies->first();
                                    $totalAtts = $report->bodies->sum(fn($b) => count($b->parsed_attachments));
                                    $domainColors = [
                                        'التعليم'            => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                        'الدعم النفسي'       => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                        'مهارات وقيم تربوية' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                    ];
                                    $domainClass = $domainColors[$report->domain_name] ?? 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300';
                                    $modalData = json_encode([
                                        'id'     => $report->id,
                                        'name'   => $report->report_name,
                                        'date'   => $report->report_date,
                                        'bodies' => $report->bodies->map(fn($b) => [
                                            'content'            => $b->content,
                                            'observation'        => $b->observation,
                                            'parsed_attachments' => $b->parsed_attachments,
                                        ])->values()->toArray(),
                                    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
                                @endphp
                                <tr class="hover:bg-orange-50/30 dark:hover:bg-orange-950/10 transition-colors">
                                    <td class="py-3 px-3 text-zinc-400 font-mono text-xs">{{ $report->id }}</td>

                                    <td class="py-3 px-3 font-medium text-zinc-900 dark:text-zinc-100 max-w-[200px]">
                                        <span class="line-clamp-2 leading-snug">{{ $report->activity_name ?? '—' }}</span>
                                    </td>

                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if($report->domain_name)
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

                                    <td class="py-3 px-3 max-w-[280px]">
                                        @if($firstBody && $firstBody->content)
                                            <p class="text-zinc-700 dark:text-zinc-300 text-xs leading-relaxed line-clamp-3">{{ $firstBody->content }}</p>
                                        @else
                                            <span class="text-zinc-400 text-xs">—</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 max-w-[200px]">
                                        @if($firstBody && $firstBody->observation)
                                            <p class="text-zinc-600 dark:text-zinc-400 text-xs leading-relaxed line-clamp-2 italic">{{ $firstBody->observation }}</p>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600 text-xs">لا يوجد</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 whitespace-nowrap text-zinc-500 dark:text-zinc-400 text-xs">
                                        {{ \Carbon\Carbon::parse($report->report_date)->format('Y-m-d') }}
                                    </td>

                                    <td class="py-3 px-3 text-center">
                                        @if($totalAtts > 0)
                                            <button @click="openModal({{ $modalData }})"
                                                class="inline-flex items-center gap-1 bg-orange-100 hover:bg-orange-200 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-300 text-xs font-medium px-2.5 py-1 rounded-lg transition-colors cursor-pointer">
                                                <flux:icon name="photo" class="size-3.5" />
                                                {{ $totalAtts }}
                                            </button>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </flux:card>

        <!-- ===== Attachments Modal ===== -->
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="closeModal()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            style="display:none"
        >
            <div
                x-show="modalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col overflow-hidden"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="size-9 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                            <flux:icon name="photo" class="size-4" />
                        </div>
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-zinc-100 text-base" x-text="modalReport?.name ?? ''"></h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="'تاريخ التقرير: ' + (modalReport?.date ?? '')"></p>
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
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4" x-text="allAttachments.length + ' مرفق'"></p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <template x-for="(att, idx) in allAttachments" :key="idx">
                                    <div class="group relative rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 aspect-square">
                                        <template x-if="['jpg','jpeg','png','gif','webp','svg'].includes((att.extension ?? '').toLowerCase())">
                                            <a :href="'/storage/' + att.path" target="_blank" rel="noopener" class="block w-full h-full">
                                                <img :src="'/storage/' + att.path" :alt="att.name"
                                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" />
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                    <div class="bg-white/90 rounded-full p-2">
                                                        <svg class="size-4 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </a>
                                        </template>
                                        <template x-if="!['jpg','jpeg','png','gif','webp','svg'].includes((att.extension ?? '').toLowerCase())">
                                            <a :href="'/storage/' + att.path" target="_blank" rel="noopener"
                                                class="flex flex-col items-center justify-center h-full gap-2 text-zinc-500 hover:text-indigo-600 transition-colors p-3">
                                                <svg class="size-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="text-xs text-center break-all line-clamp-2" x-text="att.name"></span>
                                                <span class="text-xs font-mono uppercase bg-zinc-200 dark:bg-zinc-700 px-1.5 py-0.5 rounded" x-text="att.extension"></span>
                                            </a>
                                        </template>
                                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs px-2 py-1 translate-y-full group-hover:translate-y-0 transition-transform duration-200 truncate"
                                            x-text="att.name"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endassets
</div>
