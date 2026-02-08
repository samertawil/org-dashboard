<div class="space-y-6">
    {{-- Header & Filters --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <flux:heading size="xl">{{ __('Educational Reports') }}</flux:heading>
            <flux:subheading>{{ __('Student enrollment, demographics, and group distribution.') }}</flux:subheading>
        </div>

        <div class="no-print">
            <flux:button icon="printer" onclick="window.print()">{{ __('Print') }}</flux:button>
        </div>

        <div class="flex items-center gap-2 no-print">
         
            
            <flux:select wire:model.live="selectedRegion" placeholder="{{ __('All Regions') }}" class="w-40">
                <option value="">{{ __('All Regions') }}</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                @endforeach
            </flux:select>
            
            <flux:select wire:model.live="selectedGroup" placeholder="{{ __('All Groups') }}" class="w-48">
                <option value="">{{ __('All Groups') }}</option>
                @foreach ($studentGroups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </flux:select>
        </div>
        
        <div wire:loading class="absolute right-0 top-1/2 -translate-y-1/2">
            <flux:icon name="arrow-path" class="size-5 animate-spin text-zinc-400" />
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Total Students') }}</span>
            <div class="text-3xl font-bold text-indigo-600">{{ number_format($kpis['totalStudents']) }}</div>
        </flux:card>
        
        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Active Groups') }}</span>
            <div class="text-3xl font-bold text-zinc-700 dark:text-zinc-300">{{ $kpis['totalGroups'] }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Occupancy Rate') }}</span>
            <div class="text-3xl font-bold {{ $kpis['occupancyRate'] > 90 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ number_format($kpis['occupancyRate'], 1) }}%
            </div>
        </flux:card>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Gender Distribution --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">{{ __('Gender Distribution') }}</flux:heading>
            <div wire:ignore
                 x-data="{
                    init() {
                        const isDark = document.documentElement.classList.contains('dark');
                        let chart = new ApexCharts(this.$el, {
                            series: @js($genderChartData['series']),
                            labels: @js($genderChartData['labels']),
                            chart: { 
                                type: 'pie', 
                                height: 300,
                                foreColor: isDark ? '#e4e4e7' : '#374151',
                                fontFamily: 'inherit'
                            },
                            colors: ['#3b82f6', '#ec4899'], // Blue for Male, Pink for Female
                            legend: { position: 'bottom' },
                            tooltip: { theme: isDark ? 'dark' : 'light' }
                        });
                        chart.render();
                        
                        $watch('series', (value) => {
                            chart.updateSeries(value);
                        });
                    },
                    series: @entangle('genderChartData.series')
                 }"
                 class="min-h-[300px]">
            </div>
        </flux:card>

        {{-- Age Distribution --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">{{ __('Age Groups') }}</flux:heading>
            <div wire:ignore
                 x-data="{
                    init() {
                        const isDark = document.documentElement.classList.contains('dark');
                        let chart = new ApexCharts(this.$el, {
                             series: [{ name: 'Students', data: @js($ageChartData['series']) }],
                             xaxis: { categories: @js($ageChartData['labels']) },
                             chart: { 
                                type: 'bar', 
                                height: 300,
                                foreColor: isDark ? '#e4e4e7' : '#374151',
                                fontFamily: 'inherit'
                             },
                             colors: ['#8b5cf6'],
                             plotOptions: { bar: { borderRadius: 4 } },
                             tooltip: { theme: isDark ? 'dark' : 'light' }
                        });
                        chart.render();
                    }
                 }"
                 class="min-h-[300px]">
            </div>
        </flux:card>

        {{-- Group Distribution --}}
        <flux:card class="lg:col-span-2">
             <flux:heading size="md" class="mb-4">{{ __('Students per Group (Top 10)') }}</flux:heading>
             <div wire:ignore
                  x-data="{
                    init() {
                        const isDark = document.documentElement.classList.contains('dark');
                        let chart = new ApexCharts(this.$el, {
                             series: [{ name: 'Students', data: @js($groupChartData['series']) }],
                             xaxis: { categories: @js($groupChartData['labels']) },
                             chart: { 
                                type: 'bar', 
                                height: 350,
                                foreColor: isDark ? '#e4e4e7' : '#374151',
                                fontFamily: 'inherit'
                             },
                             colors: ['#0ea5e9'],
                             plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                             tooltip: { theme: isDark ? 'dark' : 'light' }
                        });
                        chart.render();
                    }
                  }"
                  class="min-h-[350px]">
             </div>
        </flux:card>
    </div>

    @assets
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endassets
</div>
