<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Monthly Manager Report') }}</flux:heading>
            <flux:subheading>{{ __('Detailed overview of activities, sectors, and distributed parcels by month.') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <flux:select wire:model.live="selectedYear" class="flex-1 sm:w-32">
                @foreach(range(date('Y'), date('Y') - 5) as $year)
                    <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:button icon="printer" onclick="window.print()" class="flex-1 sm:flex-none">{{ __('Print') }}</flux:button>
        </div>
    </div>

    <div class="space-y-8">
        @forelse($reportData as $month => $data)
            <flux:card class="p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b pb-4 mb-4 dark:border-zinc-800 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold shrink-0">
                            {{ $month }}
                        </div>
                        <flux:heading size="lg">{{ $data['month_name'] }} {{ $selectedYear }}</flux:heading>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
                        <div class="text-left sm:text-right">
                             <div class="text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ number_format($data['total_cost'], 2) }} $</div>
                             <div class="text-xs text-zinc-500">{{ number_format($data['total_cost_nis'], 2) }} nis</div>
                        </div>
                        <flux:badge color="indigo" size="lg" class="whitespace-nowrap">{{ $data['total_activities'] }} {{ __('Activities') }}</flux:badge>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Sectors Breakdown --}}
                    <div>
                        <flux:heading size="md" class="mb-3 flex items-center gap-2">
                            <flux:icon icon="squares-2x2" class="size-4" />
                            {{ __('Breakdown by Sector') }}
                        </flux:heading>
                        <div class="space-y-2">
                            @foreach($data['sectors'] as $sector)
                                <div class="flex flex-col gap-1 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-700">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold">{{ $sector['name'] }}</span>
                                        <flux:badge size="sm" variant="ghost">{{ $sector['count'] }} {{ __('Act') }}</flux:badge>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-zinc-500">
                                        <span>{{ number_format($sector['cost'], 2) }} $</span>
                                        <span>{{ number_format($sector['cost_nis'], 2) }} nis</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Parcels Distributed --}}
                    <div>
                        <flux:heading size="md" class="mb-3 flex items-center gap-2">
                            <flux:icon icon="archive-box" class="size-4" />
                            {{ __('Distributed Parcels') }}
                        </flux:heading>
                        <div class="space-y-2">
                            @foreach($data['parcels'] as $parcel)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/30">
                                    <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ $parcel['name'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold text-emerald-700 dark:text-emerald-300">{{ number_format($parcel['count']) }}</span>
                                        <span class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Units') }}</span>
                                    </div>
                                </div>
                            @endforeach
                            @if(count($data['parcels']) == 0)
                                <div class="text-center py-4 text-zinc-500 text-sm italic">
                                    {{ __('No parcels distributed in this month.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:card class="p-12 text-center">
                <flux:icon icon="document-magnifying-glass" class="mx-auto size-12 mb-4 text-zinc-300" />
                <flux:heading>{{ __('No data found for the selected year.') }}</flux:heading>
            </flux:card>
        @endforelse
    </div>
</div>
