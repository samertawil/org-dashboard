<div class="space-y-6">
    {{-- Header & Filters --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <flux:heading size="xl">{{ __('Activity Reports') }}</flux:heading>
            <flux:subheading>{{ __('Overview of organizational activities, status, and distribution.') }}
            </flux:subheading>
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
            <flux:label>{{ __('Location') }}</flux:label>
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Total Activities') }}</span>
            <div class="text-3xl font-bold">{{ $kpis['totalActivities'] }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Completed') }}</span>
            <div class="text-3xl font-bold text-green-600">{{ $kpis['completedActivities'] }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('In Progress') }}</span>
            <div class="text-3xl font-bold text-yellow-600">{{ $kpis['ongoingActivities'] }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Planned') }}</span>
            <div class="text-3xl font-bold text-blue-600">{{ $kpis['PlannedActivities'] }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('On Hold') }}</span>
            <div class="text-3xl font-bold text-red-600">{{ $kpis['OnHoldActivities'] }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Total Budget') }}</span>
            <div class="text-3xl font-bold text-blue-600">${{ number_format($kpis['totalBudget']) }}</div>
        </flux:card>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Status Chart --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">{{ __('Status Distribution') }}</flux:heading>
            <div wire:ignore x-data="{
                init() {
                    const isDark = document.documentElement.classList.contains('dark');
                    let chart = new ApexCharts(this.$el, {
                        series: @js($statusChartData['series']),
                        labels: @js($statusChartData['labels']),
                        chart: { 
                            type: 'donut', 
                            height: 300,
                            foreColor: isDark ? '#e4e4e7' : '#374151',
                            fontFamily: 'inherit'
                        },
                        colors: ['#22c55e', '#eab308', '#3b82f6', '#f97316', '#64748b'],
                        legend: { position: 'bottom' },
                        tooltip: { theme: isDark ? 'dark' : 'light' }
                    });
                    chart.render();
        
                    $watch('series', (value) => {
                        chart.updateSeries(value);
                    });
                },
                series: @entangle('statusChartData.series')
            }" class="min-h-[300px]">
            </div>
        </flux:card>

        {{-- Geo Chart --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">{{ __('Geographic Spread') }}</flux:heading>
            <div wire:ignore x-data="{
                init() {
                    const isDark = document.documentElement.classList.contains('dark');
                    let chart = new ApexCharts(this.$el, {
                        series: [{ name: 'Activities', data: @js($geoChartData['series']) }],
                        xaxis: { categories: @js($geoChartData['labels']) },
                        chart: { 
                            type: 'bar', 
                            height: 300,
                            foreColor: isDark ? '#e4e4e7' : '#374151',
                            fontFamily: 'inherit'
                        },
                        colors: ['#3b82f6'],
                        tooltip: { theme: isDark ? 'dark' : 'light' }
                    });
                    chart.render();
                }
            }" class="min-h-[300px]">
            </div>
        </flux:card>

        {{-- Monthly Progress --}}
        <flux:card class="lg:col-span-2">
            <flux:heading size="md" class="mb-4">{{ __('Monthly Progress') }}</flux:heading>
            <div wire:ignore x-data="{
                init() {
                    const isDark = document.documentElement.classList.contains('dark');
                    let chart = new ApexCharts(this.$el, {
                        series: [{ name: 'Started', data: @js($monthlyChartData['series']) }],
                        xaxis: { categories: @js($monthlyChartData['labels']) },
                        chart: { 
                            type: 'area', 
                            height: 300,
                            foreColor: isDark ? '#e4e4e7' : '#374151',
                            fontFamily: 'inherit'
                        },
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
