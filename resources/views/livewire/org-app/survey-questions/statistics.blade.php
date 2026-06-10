<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Survey Response Statistics') }}</flux:heading>
            <flux:subheading>{{ __('Track how many students have completed their surveys per group.') }}
            </flux:subheading>
        </div>
        <span title="{{ __('Export Files') }}">
            <flux:button href="{{ route('survey.export') }}" wire:navigate variant="ghost" icon="arrow-down-tray">
                <span class="hidden sm:inline">{{ __('Export Files') }}</span>
            </flux:button>
        </span>
    </div>

    {{-- Filters Card --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-4 md:p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <flux:icon name="funnel" class="size-5 text-blue-600 dark:text-blue-400" />
            </div>
            <flux:heading size="lg">{{ __('Filter Options') }}</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3    ">
            {{-- Survey --}}
            <flux:field class="px-5 py-2">
                <flux:label badge="{{ __('Required') }}" badgeColor="text-red-600">
                    {{ __('Select Survey Group') }}
                </flux:label>
                <flux:select wire:model.live="surveyNo">
                    <option value="">{{ __('Select Survey...') }}</option>
                    @foreach ($surveys as $survey)
                        <option value="{{ $survey->id }}">{{ $survey->status_name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            {{-- Batch --}}
            <flux:field class="px-5 py-2">
                <flux:label badge="{{ __('Required') }}" badgeColor="text-red-600">
                    {{ __('Select Batch Number') }}
                </flux:label>
                <flux:select wire:model.live="batchNo">
                    <option value="">{{ __('Select Batch...') }}</option>
                    @foreach ($batchNumbers as $batch)
                        <option value="{{ $batch }}">{{ __('Batch') }} {{ $batch }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            {{-- Group — Optional --}}
            <flux:field class="px-5 py-2">
                <flux:label>
                    {{ __('Education Point Name') }}
                    <span class="text-xs text-zinc-400 mr-1 font-arabic">(اختياري — اتركه فارغاً لعرض الجميع)</span>
                </flux:label>
                <flux:select wire:model.live="groupId">
                    <option value="">{{ __('All Groups') }}</option>
                    @foreach ($this->filteredGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        </div>
    </div>

    <div class="relative min-h-[300px] flex flex-col gap-6">
        {{-- Loading Overlay --}}
        <div wire:loading.delay
            wire:target="surveyNo, batchNo, groupId"
            class="absolute inset-0 z-10 bg-white/50 dark:bg-zinc-800/50 backdrop-blur-sm flex items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="size-8 animate-spin text-zinc-500" />
        </div>

        {{-- Results --}}
        @php $stats = $this->statsPerGroup; @endphp

    @if (!empty($stats))

        {{-- Summary Totals Bar --}}
        @php
            $grandTotal = array_sum(array_column($stats, 'total'));
            $grandRespondents = array_sum(array_column($stats, 'respondents'));
            $grandNotReplied = array_sum(array_column($stats, 'not_replied'));
            $grandRate = $grandTotal > 0 ? round(($grandRespondents / $grandTotal) * 100, 1) : 0;
            $grandColor = $grandRate >= 80 ? 'green' : ($grandRate >= 50 ? 'amber' : 'red');
        @endphp

        {{-- Summary Cards — 2 cols on mobile, 4 on desktop --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">

            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-4 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon name="users" class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Total Students') }}</flux:text>
                </div>
                <span
                    class="text-2xl md:text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($grandTotal) }}</span>
            </div>

            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-4 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <flux:icon name="check-circle" class="size-5 text-green-600 dark:text-green-400" />
                    </div>
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Responded') }}</flux:text>
                </div>
                <span
                    class="text-2xl md:text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($grandRespondents) }}</span>
            </div>

            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-4 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <flux:icon name="x-circle" class="size-5 text-red-500 dark:text-red-400" />
                    </div>
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Not Responded') }}</flux:text>
                </div>
                <span
                    class="text-2xl md:text-3xl font-bold text-red-500 dark:text-red-400">{{ number_format($grandNotReplied) }}</span>
            </div>

            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-4 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div
                        class="p-1.5 rounded-lg
                        {{ $grandColor === 'green' ? 'bg-green-100 dark:bg-green-900/30' : ($grandColor === 'amber' ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-red-100 dark:bg-red-900/30') }}">
                        <flux:icon name="chart-bar"
                            class="size-5
                            {{ $grandColor === 'green' ? 'text-green-600' : ($grandColor === 'amber' ? 'text-amber-500' : 'text-red-500') }}" />
                    </div>
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Response Rate') }}</flux:text>
                </div>
                <span
                    class="text-2xl md:text-3xl font-bold
                    {{ $grandColor === 'green' ? 'text-green-600 dark:text-green-400' : ($grandColor === 'amber' ? 'text-amber-500 dark:text-amber-400' : 'text-red-500 dark:text-red-400') }}">
                    {{ $grandRate }}%
                </span>
            </div>
        </div>

        {{-- Comparison Table Card --}}
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">

            {{-- Card Header --}}
            <div class="flex items-center justify-between px-4 md:px-6 py-4 border-b border-zinc-100 dark:border-zinc-700">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                        <flux:icon name="table-cells" class="size-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <flux:heading size="lg">{{ __('Group Comparison') }}</flux:heading>
                </div>
                <flux:text class="text-xs text-zinc-400 hidden sm:block">
                    {{ count($stats) }} {{ __('groups') }} • {{ __('sorted by response rate') }}
                </flux:text>
            </div>

            {{-- ── A. Mobile Card View (block md:hidden) ── --}}
            <div class="block md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($stats as $index => $row)
                    <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">

                        {{-- Row Header: rank + name + rate badge --}}
                        <div class="flex justify-between items-start gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-xs text-zinc-400 font-mono flex-shrink-0">#{{ $index + 1 }}</span>
                                <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 truncate">
                                    {{ $row['name'] }}
                                </span>
                            </div>
                            <span class="inline-flex items-center flex-shrink-0 px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $row['color'] === 'green'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'
                                    : ($row['color'] === 'amber'
                                        ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400'
                                        : 'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400') }}">
                                {{ $row['rate'] }}%
                            </span>
                        </div>

                        {{-- Stats Grid: 3 cols --}}
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div class="bg-blue-50 dark:bg-blue-900/10 rounded-lg p-2 text-center">
                                <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Total') }}</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400 text-sm">{{ number_format($row['total']) }}</span>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/10 rounded-lg p-2 text-center">
                                <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Responded') }}</span>
                                <span class="font-bold text-green-600 dark:text-green-400 text-sm">{{ number_format($row['respondents']) }}</span>
                            </div>
                            <div class="bg-red-50 dark:bg-red-900/10 rounded-lg p-2 text-center">
                                <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Pending') }}</span>
                                <span class="font-bold {{ $row['not_replied'] > 0 ? 'text-red-500 dark:text-red-400' : 'text-zinc-400' }} text-sm">{{ number_format($row['not_replied']) }}</span>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div>
                            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-500
                                    {{ $row['color'] === 'green' ? 'bg-green-500' : ($row['color'] === 'amber' ? 'bg-amber-400' : 'bg-red-500') }}"
                                    style="width: {{ min($row['rate'], 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Mobile Footer Totals --}}
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">{{ __('Total') }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div class="rounded-lg p-2 text-center border border-blue-200 dark:border-blue-900/50">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Total') }}</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400 text-sm">{{ number_format($grandTotal) }}</span>
                        </div>
                        <div class="rounded-lg p-2 text-center border border-green-200 dark:border-green-900/50">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Responded') }}</span>
                            <span class="font-bold text-green-600 dark:text-green-400 text-sm">{{ number_format($grandRespondents) }}</span>
                        </div>
                        <div class="rounded-lg p-2 text-center border border-red-200 dark:border-red-900/50">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Pending') }}</span>
                            <span class="font-bold text-red-500 dark:text-red-400 text-sm">{{ number_format($grandNotReplied) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs text-zinc-500">{{ __('Overall Response Rate') }}</span>
                        <span class="text-sm font-bold
                            {{ $grandColor === 'green' ? 'text-green-600 dark:text-green-400' : ($grandColor === 'amber' ? 'text-amber-500 dark:text-amber-400' : 'text-red-500 dark:text-red-400') }}">
                            {{ $grandRate }}%
                        </span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full {{ $grandColor === 'green' ? 'bg-green-500' : ($grandColor === 'amber' ? 'bg-amber-400' : 'bg-red-500') }}"
                            style="width: {{ min($grandRate, 100) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- ── B. Desktop Sticky Table View (hidden md:block) ── --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 dark:text-zinc-400 text-xs uppercase sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-right font-semibold">#</th>
                            <th class="px-6 py-3 text-right font-semibold">{{ __('Education Point') }}</th>
                            <th class="px-6 py-3 text-center font-semibold">{{ __('Total Students') }}</th>
                            <th class="px-6 py-3 text-center font-semibold text-green-600">{{ __('Responded') }}</th>
                            <th class="px-6 py-3 text-center font-semibold text-red-500">{{ __('Not Responded') }}</th>
                            <th class="px-6 py-3 text-center font-semibold">{{ __('Response Rate') }}</th>
                            <th class="px-6 py-3 text-right font-semibold">{{ __('Progress') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                        @foreach ($stats as $index => $row)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors">
                                <td class="px-6 py-4 text-right text-zinc-400 font-mono">{{ $index + 1 }}</td>

                                <td class="px-6 py-4 text-right">
                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ $row['name'] }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                                        {{ number_format($row['total']) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1 font-semibold text-green-600 dark:text-green-400">
                                        {{ number_format($row['respondents']) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="font-semibold {{ $row['not_replied'] > 0 ? 'text-red-500 dark:text-red-400' : 'text-zinc-400' }}">
                                        {{ number_format($row['not_replied']) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-base font-bold
                                        {{ $row['color'] === 'green'
                                            ? 'text-green-600 dark:text-green-400'
                                            : ($row['color'] === 'amber'
                                                ? 'text-amber-500 dark:text-amber-400'
                                                : 'text-red-500 dark:text-red-400') }}">
                                        {{ $row['rate'] }}%
                                    </span>
                                </td>

                                <td class="px-6 py-4 min-w-[140px]">
                                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full transition-all duration-500
                                            {{ $row['color'] === 'green' ? 'bg-green-500' : ($row['color'] === 'amber' ? 'bg-amber-400' : 'bg-red-500') }}"
                                            style="width: {{ min($row['rate'], 100) }}%">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    {{-- Footer Totals --}}
                    <tfoot class="bg-zinc-50 dark:bg-zinc-900/50 border-t-2 border-zinc-200 dark:border-zinc-600">
                        <tr class="font-bold text-zinc-700 dark:text-zinc-300">
                            <td class="px-6 py-4 text-right" colspan="2">
                                <span class="text-sm">{{ __('Total') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-blue-600 dark:text-blue-400">
                                {{ number_format($grandTotal) }}
                            </td>
                            <td class="px-6 py-4 text-center text-green-600 dark:text-green-400">
                                {{ number_format($grandRespondents) }}
                            </td>
                            <td class="px-6 py-4 text-center text-red-500 dark:text-red-400">
                                {{ number_format($grandNotReplied) }}
                            </td>
                            <td
                                class="px-6 py-4 text-center
                                {{ $grandColor === 'green'
                                    ? 'text-green-600 dark:text-green-400'
                                    : ($grandColor === 'amber'
                                        ? 'text-amber-500 dark:text-amber-400'
                                        : 'text-red-500 dark:text-red-400') }}">
                                {{ $grandRate }}%
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-2.5 rounded-full
                                        {{ $grandColor === 'green' ? 'bg-green-500' : ($grandColor === 'amber' ? 'bg-amber-400' : 'bg-red-500') }}"
                                        style="width: {{ min($grandRate, 100) }}%">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    @elseif ($surveyNo && $batchNo)
        {{-- No data found --}}
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 p-10 md:p-16 flex flex-col items-center justify-center text-center gap-4">
            <div class="p-4 bg-zinc-100 dark:bg-zinc-700 rounded-full">
                <flux:icon name="magnifying-glass" class="size-10 text-zinc-400" />
            </div>
            <flux:heading size="lg" class="text-zinc-500">{{ __('No data found') }}</flux:heading>
            <flux:text class="text-sm text-zinc-400">
                {{ __('No groups or answers found for the selected filters.') }}
            </flux:text>
        </div>
    @else
        {{-- Empty State --}}
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 p-10 md:p-16 flex flex-col items-center justify-center text-center gap-4">
            <div class="p-4 bg-zinc-100 dark:bg-zinc-700 rounded-full mt-4">
                <flux:icon name="chart-bar" class="size-10 text-zinc-400 " />
            </div>
            <div class="flex flex-col gap-1">
                <flux:heading size="lg" class="text-zinc-500">{{ __('Select a survey type and batch to view statistics') }}</flux:heading>
                <flux:text class="text-sm text-zinc-400 p-3 md:p-5">
                    {{ __('You can leave the "Education Point" field empty to compare all groups at once') }}
                </flux:text>
            </div>
        </div>
    @endif
    </div>

</div>
