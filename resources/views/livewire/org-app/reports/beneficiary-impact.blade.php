<div class="space-y-6">
    {{-- Header & Filters --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <flux:heading size="xl">{{ __('Beneficiaries & Impact') }}</flux:heading>
            <flux:subheading>{{ __('Insights into project reach and community impact.') }}</flux:subheading>
        </div>
        <div class="no-print">
            <flux:button icon="printer" onclick="window.print()">{{ __('Print') }}</flux:button>
        </div>
    </div>
         

        <div class="flex items-start gap-6 relative">
            <flux:field>
                <flux:label>{{ __('From Date') }}</flux:label>
                <flux:input type="date" wire:model.live="dateFrom" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('To Date') }}</flux:label>
                <flux:input type="date" wire:model.live="dateTo" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Region') }}</flux:label>
                <flux:select wire:model.live="selectedRegion" placeholder="{{ __('Region') }}" class="w-40">
                    <option value="">{{ __('All Regions') }}</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
    
            <div wire:loading class="absolute right-0 top-1/2 -translate-y-1/2">
                <flux:icon name="arrow-path" class="size-5 animate-spin text-zinc-400" />
            </div>
        </div>
     

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Total Beneficiaries') }}</span>
            <div class="text-3xl font-bold text-indigo-600">{{ number_format($kpis['totalBeneficiaries']) }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Activities with Impact') }}</span>
            <div class="text-3xl font-bold text-zinc-700 dark:text-zinc-300">{{ $kpis['activitiesWithBeneficiaries'] }}
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Avg. Beneficiaries / Activity') }}</span>
            <div class="text-3xl font-bold text-emerald-600">{{ number_format($kpis['avgBeneficiariesPerActivity']) }}
            </div>
        </flux:card>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Beneficiaries by Type --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">{{ __('Beneficiary Types') }}</flux:heading>
            <div wire:ignore x-data="{
                init() {
                        const isDark = document.documentElement.classList.contains('dark');
                        let chart = new ApexCharts(this.$el, {
                            series: @js($typeChartData['series']),
                            labels: @js($typeChartData['labels']),
                            chart: {
                                type: 'pie',
                                height: 300,
                                foreColor: isDark ? '#e4e4e7' : '#374151',
                                fontFamily: 'inherit'
                            },
                            colors: ['#8b5cf6', '#ec4899', '#f97316', '#3b82f6', '#10b981'],
                            legend: { position: 'bottom' },
                            tooltip: { theme: isDark ? 'dark' : 'light' }
                        });
                        chart.render();
            
                        $watch('series', (value) => {
                            chart.updateSeries(value);
                        });
                    },
                    series: @entangle('typeChartData.series')
            }" class="min-h-[300px]">
            </div>
        </flux:card>

        {{-- Regional Impact --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">{{ __('Regional Impact') }}</flux:heading>
            <div wire:ignore x-data="{
                init() {
                    const isDark = document.documentElement.classList.contains('dark');
                    let chart = new ApexCharts(this.$el, {
                        series: [{ name: 'Beneficiaries', data: @js($regionChartData['series']) }],
                        xaxis: { categories: @js($regionChartData['labels']) },
                        chart: {
                            type: 'bar',
                            height: 300,
                            foreColor: isDark ? '#e4e4e7' : '#374151',
                            fontFamily: 'inherit'
                        },
                        colors: ['#06b6d4'],
                        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                        tooltip: { theme: isDark ? 'dark' : 'light' }
                    });
                    chart.render();
                }
            }" class="min-h-[300px]">
            </div>
        </flux:card>

        {{-- Monthly Reach --}}
        <flux:card class="lg:col-span-2">
            <flux:heading size="md" class="mb-4">{{ __('Monthly Reach Growth') }}</flux:heading>
            <div wire:ignore x-data="{
                init() {
                    const isDark = document.documentElement.classList.contains('dark');
                    let chart = new ApexCharts(this.$el, {
                        series: [{ name: 'New Beneficiaries', data: @js($monthlyChartData['series']) }],
                        xaxis: { categories: @js($monthlyChartData['labels']) },
                        chart: {
                            type: 'area',
                            height: 300,
                            foreColor: isDark ? '#e4e4e7' : '#374151',
                            fontFamily: 'inherit'
                        },
                        colors: ['#8b5cf6'],
                        stroke: { curve: 'smooth' },
                        tooltip: { theme: isDark ? 'dark' : 'light' }
                    });
                    chart.render();
                }
            }" class="min-h-[300px]">
            </div>
        </flux:card>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endassets
</div>
