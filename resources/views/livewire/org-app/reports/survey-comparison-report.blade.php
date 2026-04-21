<div class="space-y-6">
    {{-- Header & Filters --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <flux:heading size="xl">{{ __('Survey Comparison Dashboard') }}</flux:heading>
            <flux:subheading>{{ __('Compare pre and post survey results grouped by Age Level') }}</flux:subheading>
        </div>

        <div class="flex flex-wrap items-end gap-2 no-print">
            <flux:select wire:model.live="selectedLevel" :label="__('Target Level')">
                @foreach ($levels as $key => $level)
                    <option value="{{ $key }}">{{ $level['name'] }}</option>
                @endforeach
            </flux:select>
            
            <flux:select wire:model.live="selectedGroupId" :label="__('Filter Group')">
                <option value="">{{ __('All Groups') }}</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </flux:select>

            <div class="pb-1 mt-auto flex gap-2">
                <flux:button icon="printer" onclick="window.print()">{{ __('Print') }}</flux:button>
                <flux:button wire:click="export" variant="primary" icon="document-arrow-down">{{ __('Export to Excel') }}</flux:button>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Total Students') }}</span>
            <div class="text-3xl font-bold text-indigo-600">{{ count($reportData) }}</div>
        </flux:card>

        @php
            $totalImproved = 0;
            $allParticipants = count($reportData);
            if($allParticipants > 0) {
                foreach($reportData as $row) {
                    $hasImprovement = false;
                    foreach($row->pair_results as $pair) {
                       if($pair['total']['diff'] ?? 0 > 0) $hasImprovement = true;
                    }
                    if($hasImprovement) $totalImproved++;
                }
            }
        @endphp
        
        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Students showing Progress') }}</span>
            <div class="text-3xl font-bold text-emerald-600">{{ $totalImproved }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Assessment Pairs') }}</span>
            <div class="text-3xl font-bold text-blue-600">{{ count($activePairs) }}</div>
        </flux:card>

        <flux:card class="flex flex-col gap-2">
            <span class="text-zinc-500 text-sm font-medium">{{ __('Selected Level') }}</span>
            <div class="text-md font-bold text-zinc-700 truncate">
                {{ $levels[$selectedLevel]['name'] }}
            </div>
        </flux:card>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-{{ count($activePairs) }} gap-6">
        @foreach($activePairs as $pair)
            <flux:card>
                <flux:heading size="md" class="mb-2 text-center">{{ $pair['label'] }}</flux:heading>
                <flux:subheading size="xs" class="mb-4 text-center">{{ __('Progress Ratio') }} ({{ $pair['pre'] }} vs {{ $pair['post'] }})</flux:subheading>
                
                @php
                    $preId = $pair['pre'];
                    $statusGroups = collect($reportData)->groupBy(fn($r) => $r->pair_results[$preId]['total']['evaluation'] ?? 'Pending')->map->count();
                    $chartLabels = $statusGroups->keys()->toArray();
                    $chartSeries = $statusGroups->values()->toArray();
                    $chartId = "chart_" . $preId;
                @endphp
                
                <div wire:ignore
                     x-data="{
                        init() {
                            const isDark = document.documentElement.classList.contains('dark');
                            let chart = new ApexCharts(this.$el, {
                                series: @js($chartSeries),
                                labels: @js($chartLabels),
                                chart: { type: 'donut', height: 250, foreColor: isDark ? '#e4e4e7' : '#374151' },
                                legend: { position: 'bottom', fontSize: '10px' },
                                tooltip: { theme: isDark ? 'dark' : 'light' },
                                dataLabels: { enabled: false }
                            });
                            chart.render();
                            
                            Livewire.on('refreshCharts', () => {
                                chart.updateSeries(@js($chartSeries));
                            });
                        }
                     }"
                     class="min-h-[250px]">
                </div>
            </flux:card>
        @endforeach
    </div>

    {{-- Detail Table --}}
    <flux:card>
        <flux:heading size="md" class="mb-4">{{ __('Detailed Level Comparison') }}</flux:heading>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th rowspan="2" class="px-3 py-3 text-right border text-xs font-semibold text-zinc-500 uppercase min-w-[150px]">{{ __('Student') }}</th>
                        @foreach($activePairs as $pair)
                            @php 
                                $sampleRow = collect($reportData)->first();
                                $domainCount = count($sampleRow->pair_results[$pair['pre']]['domains'] ?? []);
                            @endphp
                            <th colspan="{{ $domainCount + 1 }}" class="px-3 py-2 text-center border text-xs font-bold text-indigo-600 border-b-2 border-b-indigo-200">
                                {{ $pair['label'] }}
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($activePairs as $pair)
                            @foreach($reportData->first()->pair_results[$pair['pre']]['domains'] as $domain)
                                <th class="px-2 py-2 text-center border text-[10px] font-semibold text-zinc-400 uppercase bg-zinc-50/50">
                                    {{ $domain['name'] }}
                                </th>
                            @endforeach
                            <th class="px-2 py-2 text-center border text-[10px] font-bold text-emerald-600 uppercase bg-emerald-50/30">
                                {{ __('Total Diff') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($reportData as $row)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-3 py-3 border whitespace-nowrap">
                                <div class="text-xs font-bold text-zinc-900 dark:text-white">{{ $row->full_name }}</div>
                                <div class="text-[10px] text-zinc-500">{{ $row->identity_number }}</div>
                            </td>
                            @foreach($activePairs as $pair)
                                @php $pairRes = $row->pair_results[$pair['pre']]; @endphp
                                @foreach($pairRes['domains'] as $domain)
                                    <td class="px-2 py-3 border text-center whitespace-nowrap">
                                        <div class="text-[10px] font-black" style="color: {{ $domain['color'] }};">
                                            @if($domain['diff'] !== null)
                                                {{ $domain['diff'] > 0 ? '+' : '' }}{{ $domain['diff'] }}%
                                            @else
                                                ---
                                            @endif
                                        </div>
                                        <div class="text-[9px] text-zinc-400">{{ $domain['evaluation'] }}</div>
                                    </td>
                                @endforeach
                                <td class="px-2 py-3 border text-center whitespace-nowrap bg-emerald-50/10">
                                    <div class="text-xs font-black {{ ($pairRes['total']['diff'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        @if(($pairRes['total']['diff'] ?? null) !== null)
                                            {{ $pairRes['total']['diff'] > 0 ? '+' : '' }}{{ $pairRes['total']['diff'] }}%
                                        @else
                                            <span class="text-zinc-300">---</span>
                                        @endif
                                    </div>
                                    <div class="text-[9px] font-bold text-zinc-500">{{ $pairRes['total']['evaluation'] ?? '-' }}</div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </flux:card>

    @assets
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endassets
</div>
