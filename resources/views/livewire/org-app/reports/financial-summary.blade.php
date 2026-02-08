<div class="space-y-6">
    {{-- Header & Filters --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <flux:heading size="xl">{{ __('Financial Reports') }}</flux:heading>
            <flux:subheading>{{ __('Analysis of spending, costs, and financial efficiency.') }}</flux:subheading>
        </div>
        <div class="no-print">
            <flux:button icon="printer" onclick="window.print()">{{ __('Print') }}</flux:button>
        </div>
    </div>
    <div class="flex  gap-6">
        <flux:field  >
            <flux:label>{{ __('From Date') }}</flux:label>
            <flux:input type="date" wire:model.live="dateFrom"  />
        </flux:field>
        <flux:field>
            <flux:label>{{ __('To Date') }}</flux:label>
            <flux:input type="date" wire:model.live="dateTo"  />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Sector') }}</flux:label>
            <flux:select wire:model.live="selectedSector" placeholder="{{ __('Filter by Sector') }}" class="w-48">
                <option value="">{{ __('All Sectors') }}</option>
                @foreach ($availableSectors as $sector)
                    <option value="{{ $sector->id }}">{{ $sector->status_name }}</option>
                @endforeach
            </flux:select>
        </flux:field>


    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Total Spending (USD)') }}</span>
            <div class="text-3xl font-bold text-green-600">${{ number_format($kpis['totalCostUSD'], 2) }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Total Spending (NIS)') }}</span>
            <div class="text-3xl font-bold text-blue-600">₪{{ number_format($kpis['totalCostNIS'], 2) }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Avg. Cost / Activity') }}</span>
            <div class="text-3xl font-bold text-zinc-700 dark:text-zinc-300">${{ number_format($kpis['avgCost'], 2) }}
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Avg. Cost / Beneficiary') }}</span>
            <div class="text-3xl font-bold text-amber-600">
                ${{ number_format($kpis['costPerBeneficiary'], 2) }}
            </div>
            <span class="text-xs text-zinc-400">Total Beneficiaries:
                {{ number_format($kpis['totalBeneficiaries']) }}</span>
        </flux:card>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Spending by Sector --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">{{ __('Spending by Sector') }}</flux:heading>
            <div wire:ignore x-data="{
                init() {
                        const isDark = document.documentElement.classList.contains('dark');
                        let chart = new ApexCharts(this.$el, {
                            series: @js($sectorChartData['series']),
                            labels: @js($sectorChartData['labels']),
                            chart: { 
                                type: 'pie', 
                                height: 300,
                                foreColor: isDark ? '#e4e4e7' : '#374151',
                                fontFamily: 'inherit'
                            },
                            colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1'],
                            legend: { position: 'bottom' },
                            tooltip: { theme: isDark ? 'dark' : 'light' },
                            dataLabels: { enabled: true, formatter: function(val, opts) { return opts.w.globals.series[opts.seriesIndex] > 0 ? '$' + opts.w.globals.series[opts.seriesIndex].toLocaleString() : '' } }
                        });
                        chart.render();
            
                        $watch('series', (value) => {
                            chart.updateSeries(value);
                        });
                    },
                    series: @entangle('sectorChartData.series')
            }" class="min-h-[300px]">
            </div>
        </flux:card>

        {{-- Monthly Spending Trend --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">{{ __('Monthly Spending Trend') }}</flux:heading>
            <div wire:ignore x-data="{
                init() {
                    const isDark = document.documentElement.classList.contains('dark');
                    let chart = new ApexCharts(this.$el, {
                        series: [{ name: 'Spending ($)', data: @js($monthlyChartData['series']) }],
                        xaxis: { categories: @js($monthlyChartData['labels']) },
                        chart: { 
                            type: 'bar', 
                            height: 300,
                            foreColor: isDark ? '#e4e4e7' : '#374151',
                            fontFamily: 'inherit'
                        },
                        colors: ['#10b981'],
                        plotOptions: { bar: { borderRadius: 4, dataLabels: { position: 'top' } } },
                        dataLabels: { enabled: false },
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
