<div class="flex flex-col gap-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Educational Tasks Statistics') }}</flux:heading>
            <flux:subheading>
                {{ __('Task completion overview grouped by batch, month, and student group.') }}
            </flux:subheading>
        </div>

        <span class="w-full sm:w-auto">
            <flux:button href="{{ route('educational-tasks.index') }}" wire:navigate variant="ghost"
                icon="clipboard-document-list" class="w-full">
                {{ __('Task List') }}
            </flux:button>
        </span>
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap gap-4 text-xs font-semibold text-zinc-500">
        <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-zinc-400"></span>{{ __('Total') }}</span>
        <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>{{ __('Completed') }}</span>
        <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-red-500"></span>{{ __('Delayed') }}</span>
        <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-amber-400"></span>{{ __('Required Now') }}</span>
        <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-blue-400"></span>{{ __('Upcoming') }}</span>
    </div>

    @if (count($statistics) === 0)
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-10 text-center">
            <flux:icon icon="chart-bar-square" class="size-10 text-zinc-300 mx-auto mb-3" />
            <p class="text-zinc-500 text-sm">{{ __('No scheduled activities found.') }}</p>
        </div>
    @else
        {{-- Tree --}}
        <div class="flex flex-col gap-4">

            @foreach ($statistics as $batchNo => $batchData)

                {{-- Batch Card --}}
                <div x-data="{ open: true }"
                    class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">

                    {{-- Batch Header --}}
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-zinc-50 dark:hover:bg-zinc-700/40 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-900/30">
                                <flux:icon icon="folder" class="size-5 text-indigo-500" />
                            </div>
                            <div>
                                <span class="text-base font-bold text-zinc-900 dark:text-white">
                                    {{ __('Batch') }} #{{ $batchNo }}
                                </span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <flux:badge size="sm" color="zinc">{{ $batchData['total'] }} {{ __('total') }}</flux:badge>
                                    @if ($batchData['completed'])
                                        <flux:badge size="sm" color="green">{{ $batchData['completed'] }} ✓</flux:badge>
                                    @endif
                                    @if ($batchData['delayed'])
                                        <flux:badge size="sm" color="red">{{ $batchData['delayed'] }} !</flux:badge>
                                    @endif
                                    @if ($batchData['required_now'])
                                        <flux:badge size="sm" color="amber">{{ $batchData['required_now'] }} ~</flux:badge>
                                    @endif
                                    @if ($batchData['upcoming'])
                                        <flux:badge size="sm" color="blue">{{ $batchData['upcoming'] }} →</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <flux:icon :icon="'chevron-down'" class="size-4 text-zinc-400 transition-transform duration-200"
                            ::class="open ? 'rotate-180' : ''" />
                    </button>

                    {{-- Months --}}
                    <div x-show="open" x-collapse>
                        <div class="border-t border-zinc-100 dark:border-zinc-700 divide-y divide-zinc-100 dark:divide-zinc-700/50 px-5">

                            @foreach ($batchData['months'] as $monthKey => $monthData)
                                <div x-data="{ open: false }" class="py-3">

                                    {{-- Month Row --}}
                                    <button @click="open = !open"
                                        class="w-full flex items-center justify-between py-1 group">
                                        <div class="flex items-center gap-2.5">
                                            <flux:icon icon="calendar-days"
                                                class="size-4 text-teal-500 group-hover:text-teal-600 transition-colors shrink-0" />
                                            <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                                                {{ \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->translatedFormat('F Y') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-wrap justify-end">
                                            <flux:badge size="sm" color="zinc">{{ $monthData['total'] }}</flux:badge>
                                            @if ($monthData['completed'])
                                                <flux:badge size="sm" color="green">{{ $monthData['completed'] }}</flux:badge>
                                            @endif
                                            @if ($monthData['delayed'])
                                                <flux:badge size="sm" color="red">{{ $monthData['delayed'] }}</flux:badge>
                                            @endif
                                            @if ($monthData['required_now'])
                                                <flux:badge size="sm" color="amber">{{ $monthData['required_now'] }}</flux:badge>
                                            @endif
                                            @if ($monthData['upcoming'])
                                                <flux:badge size="sm" color="blue">{{ $monthData['upcoming'] }}</flux:badge>
                                            @endif
                                            <flux:icon icon="chevron-down"
                                                class="size-3.5 text-zinc-400 transition-transform duration-150"
                                                ::class="open ? 'rotate-180' : ''" />
                                        </div>
                                    </button>

                                    {{-- Groups: Dual View --}}
                                    <div x-show="open" x-collapse>
                                        <div class="mt-2 rounded-lg border border-zinc-100 dark:border-zinc-700 overflow-hidden">

                                            {{-- A. Mobile Card View --}}
                                            <div class="block md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
                                                @foreach ($monthData['groups'] as $gId => $gData)
                                                    <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                                        <div class="flex justify-between items-start gap-3">
                                                            <div class="flex items-center gap-1.5 min-w-0">
                                                                <flux:icon icon="user-group" class="size-3.5 text-zinc-400 shrink-0" />
                                                                <span class="text-sm font-bold text-zinc-900 dark:text-white truncate">
                                                                    {{ $gData['name'] }}
                                                                </span>
                                                            </div>
                                                            <flux:badge size="sm" color="zinc" class="shrink-0">
                                                                {{ $gData['total'] }} {{ __('total') }}
                                                            </flux:badge>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-3 text-xs">
                                                            <div>
                                                                <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Completed') }}</span>
                                                                <span class="font-semibold text-green-600">{{ $gData['completed'] ?: '-' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Delayed') }}</span>
                                                                <span class="font-semibold text-red-500">{{ $gData['delayed'] ?: '-' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Required Now') }}</span>
                                                                <span class="font-semibold text-amber-500">{{ $gData['required_now'] ?: '-' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Upcoming') }}</span>
                                                                <span class="font-semibold text-blue-500">{{ $gData['upcoming'] ?: '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- B. Desktop Sticky Table --}}
                                            <div class="hidden md:block overflow-auto custom-scrollbar" style="max-height: 50vh;">
                                                <table class="w-full text-xs border-separate border-spacing-0">
                                                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 sticky top-0 z-20">
                                                        <tr>
                                                            {{-- Sticky Left: Group Name --}}
                                                            <th class="sticky left-0 bg-zinc-50 dark:bg-zinc-900 z-30 px-4 py-2 text-left font-semibold text-zinc-500 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                                                                {{ __('Group') }}
                                                            </th>
                                                            <th class="px-3 py-2 text-center font-semibold text-zinc-500 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                                                                {{ __('Total') }}
                                                            </th>
                                                            <th class="px-3 py-2 text-center font-semibold text-green-600 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                                                                {{ __('Completed') }}
                                                            </th>
                                                            <th class="px-3 py-2 text-center font-semibold text-red-500 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                                                                {{ __('Delayed') }}
                                                            </th>
                                                            <th class="px-3 py-2 text-center font-semibold text-amber-500 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                                                                {{ __('Req. Now') }}
                                                            </th>
                                                            <th class="px-3 py-2 text-center font-semibold text-blue-500 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                                                                {{ __('Upcoming') }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50 bg-white dark:bg-zinc-800">
                                                        @foreach ($monthData['groups'] as $gId => $gData)
                                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors">
                                                                {{-- Sticky Left: Group Name --}}
                                                                <td class="sticky left-0 bg-white dark:bg-zinc-800 z-10 px-4 py-2.5 text-zinc-700 dark:text-zinc-300 font-medium border-b border-zinc-100 dark:border-zinc-700/50 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.08)] dark:shadow-[2px_0_5px_-2px_rgba(0,0,0,0.25)]"
                                                                    title="{{ $gData['name'] }}">
                                                                    <div class="flex items-center gap-1.5 max-w-[200px]">
                                                                        <flux:icon icon="user-group" class="size-3 text-zinc-400 shrink-0" />
                                                                        <span class="truncate">{{ $gData['name'] }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-3 py-2.5 text-center font-bold text-zinc-700 dark:text-zinc-300">
                                                                    {{ $gData['total'] }}
                                                                </td>
                                                                <td class="px-3 py-2.5 text-center text-green-600 font-semibold">
                                                                    {{ $gData['completed'] ?: '-' }}
                                                                </td>
                                                                <td class="px-3 py-2.5 text-center text-red-500 font-semibold">
                                                                    {{ $gData['delayed'] ?: '-' }}
                                                                </td>
                                                                <td class="px-3 py-2.5 text-center text-amber-500 font-semibold">
                                                                    {{ $gData['required_now'] ?: '-' }}
                                                                </td>
                                                                <td class="px-3 py-2.5 text-center text-blue-500 font-semibold">
                                                                    {{ $gData['upcoming'] ?: '-' }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>


                                </div>
                            @endforeach

                        </div>
                    </div>

                </div>

            @endforeach
        </div>
    @endif

</div>
