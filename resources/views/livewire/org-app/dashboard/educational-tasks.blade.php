<div class="flex flex-col gap-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Educational Activity Tasks') }}</flux:heading>
            <flux:subheading>{{ __('Track, filter, and manage educational activity schedules and task statuses.') }}
            </flux:subheading>
        </div>

        <span title="{{ __('Back to Dashboard') }}" class="w-full sm:w-auto">
            <flux:button href="{{ route('dashboard') }}" wire:navigate variant="ghost" icon="chevron-left" class="w-full">
                {{ __('Dashboard') }}
            </flux:button>
        </span>
    </div>

    {{-- Session Flash Message --}}
    <x-auth-session-status class="text-center {{ session('type') == 'error' ? 'text-red-500' : '' }}"
        :status="session('message')" />

    {{-- Advanced Filters Panel --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            {{-- Search Filter --}}
            <flux:field class="relative">
                <flux:label>{{ __('Search') }}</flux:label>
                <flux:input wire:model.live.debounce.300ms="search" :placeholder="__('Search activity, group...')"
                    icon="magnifying-glass" class="w-full" />
                <div wire:loading wire:target="search" class="absolute right-3 bottom-2.5">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </flux:field>

            {{-- Date Filter --}}
            <flux:field>
                <flux:label>{{ __('Date') }}</flux:label>
                <flux:input type="date" wire:model.live="filterDate" class="w-full" />
            </flux:field>

            {{-- Status Filter --}}
            <flux:field>
                <flux:label>{{ __('Task Status') }}</flux:label>
                <flux:select wire:model.live="filterStatus" class="w-full">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="completed">{{ __('Completed') }}</option>
                    <option value="delayed">{{ __('Delayed') }}</option>
                    <option value="required_now">{{ __('Required Now') }}</option>
                    <option value="upcoming">{{ __('Upcoming') }}</option>
                </flux:select>
            </flux:field>

            {{-- Group Filter --}}
            <flux:field>
                <flux:label>{{ __('Group Name') }}</flux:label>
                <flux:select wire:model.live="filterGroup" class="w-full">
                    <option value="">{{ __('All Groups') }}</option>
                    @foreach ($this->groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            {{-- Employee Filter (Conditional for Managers) --}}
            @if ($isManager)
                <flux:field>
                    <flux:label>{{ __('Assigned Employee') }}</flux:label>
                    <flux:select wire:model.live="filterEmployee" class="w-full">
                        <option value="">{{ __('All Employees') }}</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            @else
                <flux:field>
                    <flux:label>{{ __('Employee') }}</flux:label>
                    <flux:input type="text" value="{{ auth()->user()->employee?->full_name }}" disabled
                        class="w-full bg-zinc-50 dark:bg-zinc-900/50" />
                </flux:field>
            @endif
        </div>

        {{-- Active Filters Info & Clear Button --}}
        @if ($search || $filterDate || $filterStatus || $filterGroup || ($isManager && $filterEmployee))
            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-700 flex items-center justify-between">
                <span class="text-xs text-zinc-500">
                    {{ __('Filters active') }}.
                </span>
                <flux:button wire:click="clearFilters" variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif
    </div>

    {{-- Main Content Table / Cards --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        {{-- Results Statistics --}}
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
            <div class="flex items-center justify-between">
                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                    {{ __('Showing') }}
                    <span class="font-bold text-zinc-900 dark:text-white">{{ $tasks->firstItem() ?? 0 }}</span>
                    {{ __('to') }}
                    <span class="font-bold text-zinc-900 dark:text-white">{{ $tasks->lastItem() ?? 0 }}</span>
                    {{ __('of') }}
                    <span class="font-bold text-zinc-900 dark:text-white">{{ $tasks->total() }}</span>
                    {{ __('tasks') }}
                </p>
            </div>
        </div>

        {{-- A. Mobile Card View --}}
        <div class="block md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse ($tasks as $task)
                <div wire:key="task-mobile-{{ $task->id }}"
                    class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <span
                                class="text-sm font-bold text-zinc-900 dark:text-white truncate">{{ $task->activity_name }}</span>
                            <span class="text-[10px] text-zinc-500 font-medium">
                                {{ $task->day_name }} ({{ $task->period_start?->format('Y-m-d') }})
                            </span>
                        </div>
                        <flux:badge size="sm" color="{{ $task->task_status_color }}"
                            class="font-semibold rounded-full shrink-0">
                            {{ $task->task_status_label }}
                        </flux:badge>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span
                                class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Domain') }}</span>
                            <span
                                class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $task->activityDomain?->status_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span
                                class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Group') }}</span>
                            <span
                                class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $task->group?->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span
                                class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Category') }}</span>
                            <span
                                class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $task->category_label }}</span>
                        </div>
                        <div>
                            <span
                                class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Time') }}</span>
                            <span
                                class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $task->period_start_formatted }}
                                - {{ $task->period_end_formatted }}</span>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800/50">
                        <div
                            class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold truncate max-w-[150px]">
                            {{ $task->employee?->full_name ?? __('Unassigned') }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if ($task->task_status === 'completed' && $task->activityDetail)
                                <flux:button
                                    href="{{ route('educational-activity-detail.show', $task->activityDetail->id) }}"
                                    wire:navigate size="sm" variant="ghost" icon="eye"
                                    class="text-zinc-500 hover:text-zinc-700" title="{{ __('View Detail') }}">
                                    {{ __('View') }}
                                </flux:button>
                            @else
                                <flux:button href="{{ route('educational-activity-detail.create', $task->id) }}"
                                    wire:navigate size="sm" variant="primary" icon="plus"
                                    class="bg-teal-600 hover:bg-teal-700 text-white shadow-xs">
                                    {{ __('Add Report') }}
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-zinc-500 italic">
                    {{ __('No tasks found.') }}
                </div>
            @endforelse
        </div>

        {{-- B. Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Activity Name') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Schedule Info') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Time') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Assigned Employee') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($tasks as $task)
                        <tr wire:key="task-desktop-{{ $task->id }}"
                            class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 font-bold text-zinc-900 dark:text-white text-sm">
                                <div class="flex flex-col">
                                    <span>{{ $task->activity_name }}</span>
                                    <span class="text-[10px] text-zinc-400 font-normal mt-0.5">
                                        {{ $task->category_label }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ $task->activityDomain?->status_name ?? '-' }}
                                    </span>
                                    <span class="text-xs text-zinc-400">
                                        {{ $task->group?->short_name ?? '-' }}
                                        ({{ $task->periodGroups?->description_name ?? '-' }})
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex flex-col gap-0.5 text-zinc-800 dark:text-zinc-200">
                                    <span class="font-medium">
                                        {{ $task->day_name }}
                                    </span>
                                    <span class="text-xs text-zinc-400">
                                        {{ $task->period_start?->format('Y-m-d') }}
                                        ({{ $task->period_start_formatted }} - {{ $task->period_end_formatted }})
                                    </span>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 dark:text-indigo-400 font-semibold">
                                {{ $task->employee?->full_name ?? __('Unassigned') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <flux:badge size="sm" color="{{ $task->task_status_color }}"
                                    class="font-semibold rounded-full px-2.5">
                                    {{ $task->task_status_label }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($task->task_status === 'completed' && $task->activityDetail)
                                        <flux:button
                                            href="{{ route('educational-activity-detail.show', $task->activityDetail->id) }}"
                                            wire:navigate size="sm" variant="ghost" icon="eye"
                                            class="text-zinc-500 hover:text-zinc-700"
                                            title="{{ __('View Detail') }}">
                                            {{ __('View Detail') }}
                                        </flux:button>
                                    @else
                                        <flux:button
                                            href="{{ route('educational-activity-detail.create', $task->id) }}"
                                            wire:navigate size="sm" variant="primary" icon="plus"
                                            class="bg-teal-600 hover:bg-teal-700 text-white shadow-xs">
                                            {{ __('Add Report') }}
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-zinc-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon icon="inbox" class="size-8 text-zinc-400" />
                                    <p>{{ __('No tasks found matching these filters.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-900/10">
            {{ $tasks->links() }}
        </div>
    </div>
</div>
