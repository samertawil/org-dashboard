<div class="space-y-6">
    {{-- Header & Filters --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <flux:heading size="xl">{{ __('Feedback & Satisfaction') }}</flux:heading>
            <flux:subheading>{{ __('Analysis of beneficiary feedback and program ratings.') }}</flux:subheading>
        </div>
        <div class="no-print">
            <flux:button icon="printer" onclick="window.print()">{{ __('Print') }}</flux:button>
        </div>
    </div>
    <div>  
        <div class="flex items-start gap-6 relative">
            <flux:field>
                <flux:label>{{ __('From Date') }}</flux:label>
                <flux:input type="date" wire:model.live="dateFrom" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('To Date') }}</flux:label>
                <flux:input type="date" wire:model.live="dateTo" />
            </flux:field>
            
    
            <div wire:loading class="absolute right-0 top-1/2 -translate-y-1/2">
                <flux:icon name="arrow-path" class="size-5 animate-spin text-zinc-400" />
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Total Feedback') }}</span>
            <div class="text-3xl font-bold text-indigo-600">{{ number_format($kpis['totalFeedback']) }}</div>
        </flux:card>
        
        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Average Rating') }}</span>
            <div class="text-3xl font-bold text-amber-500 flex items-center gap-1">
                {{ number_format($kpis['avgRating'], 1) }}
                <flux:icon name="star" variant="solid" class="w-6 h-6 text-amber-500" />
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Positive Sentiment') }}</span>
            <div class="text-3xl font-bold {{ $kpis['sentimentScore'] >= 80 ? 'text-emerald-600' : 'text-blue-600' }}">
                {{ number_format($kpis['sentimentScore'], 1) }}%
            </div>
            <span class="text-xs text-zinc-400">{{ __('Rated 4 or 5 stars') }}</span>
        </flux:card>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Rating Distribution --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">{{ __('Rating Distribution') }}</flux:heading>
            <div wire:ignore
                 x-data="{
                    init() {
                        const isDark = document.documentElement.classList.contains('dark');
                        let chart = new ApexCharts(this.$el, {
                            series: [{ name: 'Count', data: @js($ratingChartData['series']) }],
                            xaxis: { categories: @js($ratingChartData['labels']) },
                            chart: { 
                                type: 'bar', 
                                height: 300,
                                foreColor: isDark ? '#e4e4e7' : '#374151',
                                fontFamily: 'inherit'
                            },
                            colors: ['#f59e0b'],
                            plotOptions: { bar: { borderRadius: 4, distributed: true } },
                            tooltip: { theme: isDark ? 'dark' : 'light' }
                        });
                        chart.render();
                    }
                 }"
                 class="min-h-[300px]">
            </div>
        </flux:card>

        {{-- Top Rated Activities --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">{{ __('Highest Rated Activities') }}</flux:heading>
            <div wire:ignore
                 x-data="{
                    init() {
                        const isDark = document.documentElement.classList.contains('dark');
                        let chart = new ApexCharts(this.$el, {
                            series: [{ name: 'Avg Rating', data: @js($activityChartData['series']) }],
                            xaxis: { categories: @js($activityChartData['labels']) },
                            chart: { 
                                type: 'bar', 
                                height: 300,
                                foreColor: isDark ? '#e4e4e7' : '#374151',
                                fontFamily: 'inherit'
                            },
                            colors: ['#10b981'],
                            plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                             dataLabels: { enabled: true, formatter: function (val) { return val } },
                             tooltip: { theme: isDark ? 'dark' : 'light' }
                        });
                        chart.render();
                    }
                 }"
                 class="min-h-[300px]">
            </div>
        </flux:card>

        {{-- Satisfaction Trend --}}
        <flux:card class="lg:col-span-2">
             <flux:heading size="md" class="mb-4">{{ __('Satisfaction Trend') }}</flux:heading>
             <div wire:ignore
                  x-data="{
                    init() {
                        const isDark = document.documentElement.classList.contains('dark');
                        let chart = new ApexCharts(this.$el, {
                             series: [{ name: 'Avg Rating', data: @js($trendChartData['series']) }],
                             xaxis: { categories: @js($trendChartData['labels']) },
                             chart: { 
                                type: 'line', 
                                height: 300,
                                foreColor: isDark ? '#e4e4e7' : '#374151',
                                fontFamily: 'inherit'
                             },
                             colors: ['#6366f1'],
                             stroke: { curve: 'smooth', width: 3 },
                             markers: { size: 5 },
                             tooltip: { theme: isDark ? 'dark' : 'light' }
                        });
                        chart.render();
                    }
                  }"
                  class="min-h-[300px]">
             </div>
        </flux:card>
    </div>

    @assets
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endassets
</div>
