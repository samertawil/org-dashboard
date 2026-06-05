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
                {{ __('Export Files') }}
            </flux:button>
        </span>
    </div>

    {{-- Filters Card --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <flux:icon name="funnel" class="size-5 text-blue-600 dark:text-blue-400" />
            </div>
            <flux:heading size="lg">{{ __('Filter Options') }}</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-zinc-200 dark:divide-zinc-700">
            {{-- Survey --}}
            <flux:field class="px-5 py-2 first:ps-0 last:pe-0">
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
            <flux:field class="px-5 py-2 last:pe-0">
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

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-5 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon name="users" class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <flux:text class="text-xs text-zinc-500 font-arabic">إجمالي الطلاب</flux:text>
                </div>
                <span
                    class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($grandTotal) }}</span>
            </div>

            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-5 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <flux:icon name="check-circle" class="size-5 text-green-600 dark:text-green-400" />
                    </div>
                    <flux:text class="text-xs text-zinc-500 font-arabic">المستجيبون</flux:text>
                </div>
                <span
                    class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($grandRespondents) }}</span>
            </div>

            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-5 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <flux:icon name="x-circle" class="size-5 text-red-500 dark:text-red-400" />
                    </div>
                    <flux:text class="text-xs text-zinc-500 font-arabic">لم يستجيبوا</flux:text>
                </div>
                <span
                    class="text-3xl font-bold text-red-500 dark:text-red-400">{{ number_format($grandNotReplied) }}</span>
            </div>

            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-5 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div
                        class="p-1.5 rounded-lg
                        {{ $grandColor === 'green' ? 'bg-green-100 dark:bg-green-900/30' : ($grandColor === 'amber' ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-red-100 dark:bg-red-900/30') }}">
                        <flux:icon name="chart-bar"
                            class="size-5
                            {{ $grandColor === 'green' ? 'text-green-600' : ($grandColor === 'amber' ? 'text-amber-500' : 'text-red-500') }}" />
                    </div>
                    <flux:text class="text-xs text-zinc-500 font-arabic">نسبة الاستجابة الكلية</flux:text>
                </div>
                <span
                    class="text-3xl font-bold
                    {{ $grandColor === 'green' ? 'text-green-600 dark:text-green-400' : ($grandColor === 'amber' ? 'text-amber-500 dark:text-amber-400' : 'text-red-500 dark:text-red-400') }}">
                    {{ $grandRate }}%
                </span>
            </div>
        </div>

        {{-- Comparison Table --}}
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 dark:border-zinc-700">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                        <flux:icon name="table-cells" class="size-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <flux:heading size="lg" class="font-arabic">مقارنة نقاط التعليم</flux:heading>
                </div>
                <flux:text class="text-xs text-zinc-400 font-arabic">
                    مرتبة تنازلياً حسب نسبة الاستجابة • {{ count($stats) }} مجموعة
                </flux:text>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 dark:text-zinc-400 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-right font-semibold">#</th>
                            <th class="px-6 py-3 text-right font-semibold">نقطة التعليم</th>
                            <th class="px-6 py-3 text-center font-semibold">إجمالي الطلاب</th>
                            <th class="px-6 py-3 text-center font-semibold text-green-600">المستجيبون</th>
                            <th class="px-6 py-3 text-center font-semibold text-red-500">لم يستجيبوا</th>
                            <th class="px-6 py-3 text-center font-semibold">نسبة الاستجابة</th>
                            <th class="px-6 py-3 text-right font-semibold">شريط التقدم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                        @foreach ($stats as $index => $row)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors">
                                <td class="px-6 py-4 text-right text-zinc-400 font-mono">{{ $index + 1 }}</td>

                                <td class="px-6 py-4 text-right">
                                    <span class="font-medium text-zinc-800 dark:text-zinc-200 font-arabic">
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
                                <span class="font-arabic text-sm">الإجمالي</span>
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
            class="bg-white dark:bg-zinc-800 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 p-16 flex flex-col items-center justify-center text-center gap-4">
            <div class="p-4 bg-zinc-100 dark:bg-zinc-700 rounded-full">
                <flux:icon name="magnifying-glass" class="size-10 text-zinc-400" />
            </div>
            <flux:heading size="lg" class="text-zinc-500 font-arabic">لا توجد بيانات للعرض</flux:heading>
            <flux:text class="text-sm text-zinc-400 font-arabic">لم يتم العثور على مجموعات أو إجابات بالفلاتر المحددة
            </flux:text>
        </div>
    @else
        {{-- Empty State --}}
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 p-16 flex flex-col items-center justify-center text-center gap-4">
            <div class="p-4 bg-zinc-100 dark:bg-zinc-700 rounded-full">
                <flux:icon name="chart-bar" class="size-10 text-zinc-400" />
            </div>
            <div class="flex flex-col gap-1">
                <flux:heading size="lg" class="text-zinc-500 font-arabic">Select a survey type and batch to view
                    statistics</flux:heading>
                <flux:text class="text-sm text-zinc-400 font-arabic">
                    You can leave the "Education Point" field empty to compare all groups at once
                </flux:text>
            </div>
        </div>
    @endif

</div>
