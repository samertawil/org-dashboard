<div class="flex flex-col gap-6" x-on:modal-show.window="Flux.modal($event.detail.name).show()"
    x-on:modal-close.window="Flux.modal($event.detail.name).hide()">
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Educational Activity Schedules') }}</flux:heading>
            <flux:subheading>{{ __('Manage scheduled educational activities for children') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            {{-- View Switcher --}}
            <div
                class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-900/50 p-1 rounded-lg border border-zinc-200 dark:border-zinc-700/50 shrink-0">
                <button wire:click="$set('viewType', 'table')" type="button"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1.5 transition-all {{ $viewType === 'table' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-sm border border-zinc-200 dark:border-zinc-700' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 border border-transparent' }}">
                    <flux:icon name="table-cells" class="size-3.5" />
                    {{ __('Table') }}
                </button>
                <button wire:click="$set('viewType', 'tree')" type="button"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1.5 transition-all {{ $viewType === 'tree' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-sm border border-zinc-200 dark:border-zinc-700' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 border border-transparent' }}">
                    <flux:icon name="queue-list" class="size-3.5" />
                    {{ __('Tree View') }}
                </button>
            </div>
            @can('export', \App\Models\ActivitySchedule::class)
                <flux:button wire:click="export" variant="outline" icon="document-arrow-down">
                    {{ __('Export Excel') }}
                </flux:button>
            @endcan
            @can('duplicate', \App\Models\ActivitySchedule::class)
                <flux:button wire:click="openCloneMonthModal" variant="outline" icon="document-duplicate">
                    {{ __('Clone Month') }}
                </flux:button>
            @endcan
            @can('create', \App\Models\ActivitySchedule::class)
                <flux:button href="{{ route('educational-activity-schedules.create') }}" wire:navigate variant="primary"
                    icon="plus">
                    {{ __('Add Schedule') }}
                </flux:button>
            @endcan

        </div>
    </div>

    {{-- Success/Error Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />
    @if (session('error'))
        <div
            class="p-3 bg-red-100 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 text-center rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">

        {{-- Filters --}}
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 relative">

                {{-- Search --}}
                <div class="md:col-span-1 relative">
                    <flux:input wire:model.live="search" :placeholder="__('Search by activity name or notes...')"
                        icon="magnifying-glass" />
                    <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                    </div>
                </div>

                {{-- Batch Filter --}}
                <div>
                    <flux:select wire:model.live="filterBatch">
                        <option value="">-- {{ __('Select Batch (Required)') }} --</option>
                        @foreach ($this->availableBatches as $batch)
                            <option value="{{ $batch }}">{{ $batch }}</option>
                        @endforeach
                    </flux:select>
                </div>

                {{-- Student Group Filter --}}
                <div>
                    <flux:select wire:model.live="filterGroup" :disabled="empty($this->filterBatch)">
                        <option value="">-- {{ __('Select Student Group (Required)') }} --</option>
                        @foreach ($this->availableGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                {{-- Category Filter --}}
                <div>
                    <flux:select wire:model.live="filterCategory">
                        <option value="">-- {{ __('All Categories') }} --</option>
                        @foreach (\App\Models\ActivitySchedule::TARGET_CATEGORIES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </div>

                {{-- Per Page --}}
                <div>
                    <flux:select wire:model.live="perPage">
                        <option value="10">10 {{ __('per page') }}</option>
                        <option value="25">25 {{ __('per page') }}</option>
                        <option value="50">50 {{ __('per page') }}</option>
                    </flux:select>
                </div>
            </div>

            {{-- Date Range Row --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="flex items-center gap-2">
                    <flux:label class="shrink-0 text-xs text-zinc-500">{{ __('From') }}</flux:label>
                    <flux:input wire:model.live="filterDateFrom" type="date" class="flex-1" />
                </div>
                <div class="flex items-center gap-2">
                    <flux:label class="shrink-0 text-xs text-zinc-500">{{ __('To') }}</flux:label>
                    <flux:input wire:model.live="filterDateTo" type="date" class="flex-1" />
                </div>
                <div></div>
            </div>

            @if ($search || $filterCategory || $filterDomain || $filterGroup || $filterDateFrom || $filterDateTo)
                <div class="flex items-center justify-end">
                    <flux:button
                        wire:click="$set('search', ''); $set('filterCategory', ''); $set('filterDomain', ''); $set('filterGroup', ''); $set('filterDateFrom', ''); $set('filterDateTo', '');"
                        variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </div>
            @endif
        </div>

        {{-- Pagination Info --}}
        <div class="px-6 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                @if ($this->schedules instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ __('Showing') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->schedules->firstItem() }}</span>
                    {{ __('to') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->schedules->lastItem() }}</span>
                    {{ __('of') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->schedules->total() }}</span>
                    {{ __('results') }}
                @else
                    {{ __('Total') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->schedules->count() }}</span>
                    {{ __('results') }}
                @endif
            </p>
        </div>

        <div class="relative min-h-[400px]">
            {{-- Loading Overlay --}}
            <div wire:loading.delay
                wire:target="search, filterDomain, filterCategory, filterBatch, filterGroup, filterDateFrom, filterDateTo, sortField, sortDirection, perPage, viewType"
                class="absolute inset-0 z-10 bg-white/50 dark:bg-zinc-800/50 backdrop-blur-sm flex items-center justify-center">
                <flux:icon name="arrow-path" class="size-8 animate-spin text-zinc-500" />
            </div>

            @if (empty($this->filterBatch) || empty($this->filterGroup))
                <div class="flex flex-col items-center justify-center py-20 px-4">
                    <flux:icon name="funnel" class="size-16 text-zinc-300 dark:text-zinc-600 mb-4" />
                    <h3 class="text-xl font-medium text-zinc-900 dark:text-white">{{ __('Batch & Group Selection Required') }}
                    </h3>
                    <p class="text-zinc-500 dark:text-zinc-400 mt-2 text-center max-w-md">
                        {{ __('To ensure high performance and precise data loading, please select both a Batch and a Student Group from the filters above before viewing the schedules.') }}
                    </p>
                </div>
            @else
                @if ($viewType === 'table')
                    {{-- Mobile Cards View (Hidden on medium and larger screens) --}}
                    <div class="block md:hidden">
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($this->schedules as $schedule)
                                <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-zinc-900 dark:text-white">{{ $schedule->activityNameStatus?->status_name ?? '—' }}</span>
                                            @if ($schedule->activity_description)
                                                <span
                                                    class="text-xs text-zinc-500 truncate max-w-[200px]">{{ $schedule->activity_description }}</span>
                                            @endif
                                        </div>
                                        <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">
                                            {{ $schedule->activityDomain?->status_name ?? '—' }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span
                                                class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-1">{{ __('Category') }}</span>
                                            <div class="text-xs text-zinc-600 dark:text-zinc-300">
                                                @if ($schedule->target_category === 'work_team')
                                                    <flux:badge color="blue" size="sm">{{ __('Work Team') }}
                                                    </flux:badge>
                                                @elseif($schedule->target_category === 'children')
                                                    <flux:badge color="green" size="sm">{{ __('Children') }}
                                                    </flux:badge>
                                                @elseif($schedule->target_category === 'parents')
                                                    <flux:badge color="purple" size="sm">{{ __('Parents') }}
                                                    </flux:badge>
                                                @else
                                                    <span class="text-zinc-400">—</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <span
                                                class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-1">{{ __('Assigned Groups') }}</span>
                                            <div class="leading-tight">
                                                <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                                                    {{ $schedule->periodGroups?->status_name ?? '—' }}
                                                </span>
                                                @if ($schedule->periodGroups?->description)
                                                    <span
                                                        class="block text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">
                                                        {{ $schedule->periodGroups->description }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-2">
                                        <div>
                                            <span
                                                class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-1">{{ __('Schedule Time') }}</span>
                                            <div class="text-xs text-zinc-600 dark:text-zinc-300">
                                                @if ($schedule->period_start)
                                                    <span
                                                        class="font-medium text-zinc-700 dark:text-zinc-300">{{ $schedule->period_start->format('Y-m-d') }}</span>
                                                    @if ($schedule->period_end && $schedule->period_start->isSameDay($schedule->period_end))
                                                        <span
                                                            class="text-zinc-500">({{ $schedule->period_start->format('H:i') }}
                                                            → {{ $schedule->period_end->format('H:i') }})</span>
                                                    @else
                                                        <span
                                                            class="text-zinc-500">({{ $schedule->period_start->format('H:i') }})</span>
                                                        @if ($schedule->period_end)
                                                            <span class="block text-zinc-500 mt-1">To: <span
                                                                    class="font-medium text-zinc-700 dark:text-zinc-300">{{ $schedule->period_end->format('Y-m-d H:i') }}</span></span>
                                                        @endif
                                                    @endif
                                                @else
                                                    —
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800/50">
                                        <div class="text-xs text-zinc-500">
                                            <span
                                                class="font-medium text-zinc-700 dark:text-zinc-400">{{ __('Moderator / Teacher') }}:</span>
                                            {{ $schedule->employee?->full_name ?? '—' }}
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span title="{{ __('View Details') }}">
                                                <flux:button target="_blank"
                                                    href="{{ route('educational-activity-schedules.show', $schedule) }}"
                                                    wire:navigate variant="ghost" size="xs" icon="eye" />
                                            </span>
                                            @if ($this->canCreateSchedule)
                                                <span title="{{ __('Copy Schedule') }}">
                                                    <flux:button
                                                        href="{{ route('educational-activity-schedules.create', ['copy_from' => $schedule->id]) }}"
                                                        wire:navigate variant="ghost" size="xs"
                                                        icon="document-duplicate" class="text-amber-500" />
                                                </span>
                                            @endif
                                            @if ($this->canUpdateSchedule)
                                                <span title="{{ __('Edit') }}">
                                                    <flux:button
                                                        href="{{ route('educational-activity-schedules.edit', $schedule) }}"
                                                        wire:navigate variant="ghost" size="xs"
                                                        icon="pencil-square" />
                                                </span>
                                            @endif
                                            @if ($this->canDeleteSchedule)
                                                <span title="{{ __('Delete') }}">
                                                    <flux:button wire:click="delete({{ $schedule->id }})"
                                                        wire:confirm="{{ __('Are you sure?') }}" variant="ghost"
                                                        size="xs" icon="trash" class="text-red-500" />
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center p-10 text-center">
                                    <flux:icon name="calendar"
                                        class="size-12 text-zinc-300 dark:text-zinc-600 mb-3" />
                                    <h4 class="text-base font-medium text-zinc-900 dark:text-blue-100">
                                        {{ __('No schedules found') }}</h4>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                                        {{ __('Try adjusting your search or filters to find what you are looking for.') }}
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Desktop Table View --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-900">
                                <tr>
                                    <th wire:click="sortBy('activity_name')"
                                        class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                        <div class="flex items-center gap-1">
                                            {{ __('Activity Name') }}
                                            @if ($sortField === 'activity_name')
                                                <flux:icon
                                                    name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                                    class="size-3" />
                                            @else
                                                <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                            @endif
                                        </div>
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Domain') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Category') }}
                                    </th>
                                    <th wire:click="sortBy('period_start')"
                                        class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                        <div class="flex items-center gap-1">
                                            {{ __('Schedule Time') }}
                                            @if ($sortField === 'period_start')
                                                <flux:icon
                                                    name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                                    class="size-3" />
                                            @else
                                                <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                            @endif
                                        </div>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Group') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                @forelse($this->schedules as $schedule)
                                    <tr
                                        class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors duration-150">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                            <div class="flex flex-col gap-1 max-w-[220px]">
                                                <span class="font-semibold truncate text-zinc-900 dark:text-zinc-100"
                                                    title="{{ $schedule->activityNameStatus?->status_name ?? '' }}">{{ $schedule->activityNameStatus?->status_name ?? '—' }}</span>
                                                @if ($schedule->activity_description)
                                                    <span class="text-xs text-zinc-400 dark:text-zinc-500 truncate"
                                                        title="{{ $schedule->activity_description }}">
                                                        {{ $schedule->activity_description }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-700/50 text-zinc-700 dark:text-zinc-300 border border-zinc-200/50 dark:border-zinc-700/30">
                                                {{ $schedule->activityDomain?->status_name ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($schedule->target_category === 'work_team')
                                                <flux:badge color="blue" size="sm">{{ __('Work Team') }}
                                                </flux:badge>
                                            @elseif($schedule->target_category === 'children')
                                                <flux:badge color="green" size="sm">{{ __('Children') }}
                                                </flux:badge>
                                            @elseif($schedule->target_category === 'parents')
                                                <flux:badge color="purple" size="sm">{{ __('Parents') }}
                                                </flux:badge>
                                            @else
                                                <span class="text-zinc-400">—</span>
                                            @endif
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                            <div class="flex flex-col gap-1">
                                                <div
                                                    class="flex items-center gap-1.5 text-zinc-900 dark:text-zinc-100 font-medium">
                                                    <flux:icon name="calendar"
                                                        class="size-3.5 text-zinc-400 dark:text-zinc-500 shrink-0" />
                                                    <span>{{ $schedule->period_start?->format('Y-m-d') ?? '—' }}</span>
                                                </div>
                                                <div
                                                    class="flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                                    <flux:icon name="clock"
                                                        class="size-3 text-zinc-400 dark:text-zinc-500 shrink-0" />
                                                    @if ($schedule->period_start && $schedule->period_end)
                                                        @if ($schedule->period_start->isSameDay($schedule->period_end))
                                                            <span>{{ $schedule->period_start->format('H:i') }} &rarr;
                                                                {{ $schedule->period_end->format('H:i') }}</span>
                                                        @else
                                                            <span>{{ $schedule->period_start->format('H:i') }} &rarr;
                                                                {{ $schedule->period_end->format('Y-m-d H:i') }}</span>
                                                        @endif
                                                    @else
                                                        <span>{{ $schedule->period_start?->format('H:i') ?? '—' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                            <div class="flex flex-col gap-0.5">
                                                <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                                    {{ $schedule->periodGroups?->status_name ?? '—' }}
                                                </span>
                                                @if ($schedule->periodGroups?->description)
                                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">
                                                        {{ $schedule->periodGroups->description }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <flux:button
                                                    x-on:click="$dispatch('open-schedule-details', { id: {{ $schedule->id }} })"
                                                    variant="ghost" size="sm" icon="eye" />
                                                @if ($this->canCreateSchedule)
                                                    <flux:button
                                                        href="{{ route('educational-activity-schedules.create', ['copy_from' => $schedule->id]) }}"
                                                        wire:navigate variant="ghost" size="sm"
                                                        icon="document-duplicate" title="{{ __('Copy Schedule') }}"
                                                        class="text-amber-500 hover:text-amber-600" />
                                                    <flux:button
                                                        href="{{ route('educational-activity-schedules.edit', $schedule) }}"
                                                        wire:navigate variant="ghost" size="sm"
                                                        icon="pencil-square" />
                                                    <flux:button wire:click="delete({{ $schedule->id }})"
                                                        wire:confirm="{{ __('Are you sure you want to delete this schedule?') }}"
                                                        variant="ghost" size="sm" icon="trash"
                                                        class="text-red-500 hover:text-red-600" />
                                                @endif
                                                @if ($this->canViewAnyReport)
                                                    <flux:dropdown>
                                                        <flux:button variant="ghost" size="sm" icon="document-text"
                                                            style="{{ $schedule->activityDetail?->status_id == 195 ? 'color: #ef4444 !important;' : ($schedule->activityDetail?->status_id == 194 ? 'color: #eab308 !important;' : ($schedule->activityDetail ? 'color: #3b82f6 !important;' : '')) }}"
                                                            class="relative {{ !$schedule->activityDetail ? 'text-zinc-500 dark:text-zinc-400' : '' }}">
                                                            {{ __('Report') }}
                                                            @if ($schedule->activityDetail)
                                                                <span
                                                                    class="absolute top-0 right-0 block h-1.5 w-1.5 rounded-full bg-{{ $schedule->activityDetail?->status_id == 195 ? 'red-500' : ($schedule->activityDetail?->status_id == 194 ? 'yellow-500' : 'blue-500') }} ring-1 ring-white dark:ring-zinc-900"></span>
                                                            @endif
                                                        </flux:button>
                                                        <flux:menu>
                                                            @if ($this->canCreateReport)
                                                                <flux:menu.item
                                                                    wire:click="openReportModal('create', {{ $schedule->id }})"
                                                                    icon="plus">{{ __('Add Report') }}</flux:menu.item>
                                                            @endif
                                                            @if ($this->canUpdateReport)
                                                                <flux:menu.item
                                                                    wire:click="openReportModal('edit', {{ $schedule->id }})"
                                                                    icon="pencil-square">{{ __('Edit Report') }}
                                                                </flux:menu.item>
                                                            @endif
                                                            @if ($this->isSuperAdminOrSelectAnyStudent || $schedule->employee_id == $this->userEmployeeId || in_array($schedule->group_id, $this->userGroupIds))
                                                                <flux:menu.item
                                                                    wire:click="openReportModal('show', {{ $schedule->id }})"
                                                                    icon="eye">{{ __('Show Report') }}</flux:menu.item>
                                                            @endif
                                                            @if ($this->canCreateReport)
                                                                <flux:menu.item
                                                                    wire:click="openReportModal('gallery', {{ $schedule->id }})"
                                                                    icon="photo">{{ __('Add Attachments') }}
                                                                </flux:menu.item>
                                                            @endif
                                                        </flux:menu>
                                                    </flux:dropdown>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="bg-zinc-100 dark:bg-zinc-800/50 p-4 rounded-full mb-4">
                                                    <flux:icon name="calendar"
                                                        class="size-8 text-zinc-400 dark:text-zinc-500" />
                                                </div>
                                                <h4 class="text-base font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ __('No schedules found') }}</h4>
                                                <p
                                                    class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 max-w-sm mx-auto">
                                                    {{ __('We couldn\'t find any schedules matching your current filters. Try changing the criteria or adding a new schedule.') }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Tree View (Grouped by Student Group -> Month -> Date) --}}
                    <div x-data="{ openGroups: {}, openMonths: {}, openDates: {} }" x-init="@if ($lastGroupId) openGroups['{{ $lastGroupId }}'] = true; @endif
                    @if ($lastGroupId && $lastMonth) openMonths['{{ $lastGroupId }}_{{ $lastMonth }}'] = true; @endif
                    @if ($lastGroupId && $lastMonth && $lastDate) openDates['{{ $lastGroupId }}_{{ $lastMonth }}_{{ $lastDate }}'] = true; @endif
                    
                    $nextTick(() => {
                        let lastDateKey = '{{ $lastGroupId }}_{{ $lastMonth }}_{{ $lastDate }}';
                        let element = document.getElementById('date-' + lastDateKey);
                        if (element) {
                            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            element.classList.add('ring-2', 'ring-indigo-500', 'ring-offset-2', 'dark:ring-offset-zinc-800');
                            setTimeout(() => {
                                element.classList.remove('ring-2', 'ring-indigo-500', 'ring-offset-2', 'dark:ring-offset-zinc-800');
                            }, 3000);
                        }
                    })" class="p-6 space-y-4">
                        @php
                            $schedulesData =
                                $this->schedules instanceof \Illuminate\Database\Eloquent\Builder
                                    ? $this->schedules->get()
                                    : ($this->schedules instanceof \Illuminate\Pagination\LengthAwarePaginator
                                        ? $this->schedules->getCollection()
                                        : collect($this->schedules));

                            $groupedByGroup = $schedulesData->groupBy(function ($item) {
                                return $item->group?->id ?? 'no_group';
                            });
                        @endphp

                        @forelse($groupedByGroup as $groupId => $groupSchedules)
                            @php
                                $groupSchedules = collect($groupSchedules);
                                $groupModel = $groupSchedules->first()->group;
                                $groupName = $groupModel?->name ?? __('No Group');
                                $parts = array_map('trim', explode(' - ', $groupName));
                                $shortGroupName =
                                    count($parts) > 2 ? implode(' - ', array_slice($parts, -2)) : $groupName;
                                $totalCount = $groupSchedules->count();
                            @endphp

                            <div
                                class="border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden bg-white dark:bg-zinc-800 shadow-sm">
                                {{-- Group Header --}}
                                <button
                                    @click="openGroups['{{ $groupId }}'] = !openGroups['{{ $groupId }}']"
                                    type="button"
                                    class="w-full flex items-center justify-between p-4 bg-zinc-50/70 dark:bg-zinc-900/40 hover:bg-zinc-100/50 dark:hover:bg-zinc-900/60 transition-colors text-right">
                                    <div class="flex items-center gap-3">
                                        {{-- Toggle Icon --}}
                                        {{-- Toggle Icon --}}
                                        <svg class="size-4 text-zinc-400 transition-transform duration-200" ::class="openGroups['{{ $groupId }}'] ? '' : '-rotate-90'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                        <div class="flex items-center gap-2">
                                            <svg class="size-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.115c0 1.223-.08 2.427-.235 3.609M18.75 10.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM12.75 12a4.5 4.5 0 0 1-7.5 0 4.5 4.5 0 0 1 7.5 0ZM3.75 19.5a4.5 4.5 0 0 1 9 0M21 12c.007-.06.012-.12.012-.181A3.75 3.75 0 0 0 17.25 8.25c-.328 0-.646.042-.95.121M16.5 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span
                                                class="font-bold text-zinc-900 dark:text-zinc-100 text-sm md:text-base"
                                                title="{{ $groupName }}">
                                                {{ $groupName }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/30">
                                        {{ $totalCount }} {{ __('Activities') }}
                                    </span>
                                </button>

                                {{-- Group Content (Months List) --}}
                                <div x-show="openGroups['{{ $groupId }}']" x-collapse
                                    class="border-t border-zinc-200 dark:border-zinc-700 divide-y divide-zinc-150 dark:divide-zinc-750">
                                    @php
                                        $groupedByMonth = $groupSchedules->groupBy(function ($item) {
                                            return $item->period_start
                                                ? $item->period_start->format('Y-m')
                                                : 'unscheduled';
                                        });
                                    @endphp

                                    @foreach ($groupedByMonth as $monthKey => $monthSchedules)
                                        @php
                                            $monthSchedules = collect($monthSchedules);
                                            $monthCount = $monthSchedules->count();
                                            if ($monthKey === 'unscheduled') {
                                                $monthLabel = __('Unscheduled');
                                            } else {
                                                $carbonDate = \Carbon\Carbon::parse($monthKey . '-01');
                                                $monthLabel =
                                                    __('Month') . ' ' . $carbonDate->translatedFormat('F / Y');
                                            }
                                            $monthUniqueKey = $groupId . '_' . $monthKey;
                                        @endphp

                                        <div class="bg-white dark:bg-zinc-800">
                                            {{-- Month Header --}}
                                            <button
                                                @click="openMonths['{{ $monthUniqueKey }}'] = !openMonths['{{ $monthUniqueKey }}']"
                                                type="button"
                                                class="w-full flex items-center justify-between px-6 py-3.5 bg-zinc-50/20 dark:bg-zinc-900/10 hover:bg-zinc-100/30 dark:hover:bg-zinc-900/30 transition-colors text-right">
                                                <div class="flex items-center gap-3">
                                                    <svg class="size-3.5 text-zinc-400 transition-transform duration-200" ::class="openMonths['{{ $monthUniqueKey }}'] ? '' : '-rotate-90'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                    </svg>
                                                    <div
                                                        class="flex items-center gap-2 text-xs md:text-sm font-bold text-zinc-850 dark:text-zinc-150">
                                                        <svg class="size-4.5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                                        </svg>
                                                        <span>{{ $monthLabel }}</span>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-100 dark:border-blue-800/30">
                                                    {{ $monthCount }} {{ __('Activities') }}
                                                </span>
                                            </button>

                                            {{-- Month Content (Dates List) --}}
                                            <div x-show="openMonths['{{ $monthUniqueKey }}']" x-collapse
                                                class="px-4 md:px-6 pb-4 pt-2 space-y-3 bg-zinc-50/5 dark:bg-zinc-900/5 border-t border-zinc-100 dark:border-zinc-700/30">
                                                @php
                                                    $groupedByDate = $monthSchedules->groupBy(function ($item) {
                                                        return $item->period_start
                                                            ? $item->period_start->format('Y-m-d')
                                                            : 'unscheduled';
                                                    });
                                                @endphp

                                                @foreach ($groupedByDate as $date => $daySchedules)
                                                    @php
                                                        $daySchedules = collect($daySchedules);
                                                        $dateKey = $monthUniqueKey . '_' . $date;
                                                        $dayCount = $daySchedules->count();
                                                    @endphp

                                                    <div
                                                        class="border border-zinc-150 dark:border-zinc-700/80 rounded-lg overflow-hidden bg-white dark:bg-zinc-800 shadow-sm">
                                                        {{-- Date Sub-Header --}}
                                                        <button
                                                            @click="openDates['{{ $dateKey }}'] = !openDates['{{ $dateKey }}']"
                                                            type="button"
                                                            class="w-full flex items-center justify-between px-4 py-2.5 bg-zinc-50/40 dark:bg-zinc-900/20 hover:bg-zinc-100/20 dark:hover:bg-zinc-900/40 transition-colors text-right">
                                                            <div class="flex items-center gap-3">
                                                                <svg class="size-3 text-zinc-400 transition-transform duration-200" ::class="openDates['{{ $dateKey }}'] ? '' : '-rotate-90'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                                </svg>
                                                                <div
                                                                    class="flex items-center gap-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                                                                    <svg class="size-4 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                                                    </svg>
                                                                    @if ($date === 'unscheduled')
                                                                        {{ __('Unscheduled') }}
                                                                    @else
                                                                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l - d F Y') }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <span class="text-xs text-zinc-500 font-medium">
                                                                {{ $dayCount }} {{ __('Activities') }}
                                                            </span>
                                                        </button>

                                                        {{-- Date Content (Activities Details) --}}
                                                        <div x-show="openDates['{{ $dateKey }}']" x-collapse
                                                            class="px-4 pb-4 pt-2">
                                                            <div
                                                                class="overflow-x-auto rounded-lg border border-zinc-100 dark:border-zinc-700">
                                                                <table
                                                                    class="w-full divide-y divide-zinc-200 dark:divide-zinc-700 text-right">
                                                                    <thead
                                                                        class="bg-zinc-50/50 dark:bg-zinc-900/20 text-xs text-zinc-500 font-medium">
                                                                        <tr>
                                                                            <th class="px-4 py-2 text-right">
                                                                                {{ __('Time') }}</th>
                                                                            <th class="px-4 py-2 text-right">
                                                                                {{ __('Activity') }}</th>
                                                                            <th class="px-4 py-2 text-right">
                                                                                {{ __('Domain') }}</th>
                                                                            <th class="px-4 py-2 text-right">
                                                                                {{ __('Category') }}</th>
                                                                            <th class="px-4 py-2 text-right">
                                                                                {{ __('Assigned Groups') }}</th>
                                                                            <th class="px-4 py-2 text-right">
                                                                                {{ __('Teacher') }}</th>
                                                                            <th class="px-4 py-2 text-left">
                                                                                {{ __('Actions') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody
                                                                        class="divide-y divide-zinc-100 dark:divide-zinc-700/50 text-xs text-zinc-700 dark:text-zinc-300">
                                                                        @foreach ($daySchedules as $schedule)
                                                                            <tr
                                                                                class="hover:bg-zinc-50/30 dark:hover:bg-zinc-700/10">
                                                                                {{-- Time --}}
                                                                                <td
                                                                                    class="px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">
                                                                                    @if ($schedule->period_start)
                                                                                        {{ $schedule->period_start->format('H:i') }}
                                                                                        @if ($schedule->period_end)
                                                                                            →
                                                                                            {{ $schedule->period_end->format('H:i') }}
                                                                                        @endif
                                                                                    @else
                                                                                        —
                                                                                    @endif
                                                                                </td>
                                                                                {{-- Activity Name --}}
                                                                                <td
                                                                                    class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">
                                                                                    {{ $schedule->activityNameStatus?->status_name ?? '—' }}
                                                                                </td>
                                                                                {{-- Domain --}}
                                                                                <td class="px-4 py-3">
                                                                                    {{ $schedule->activityDomain?->status_name ?? '—' }}
                                                                                </td>
                                                                                {{-- Category --}}
                                                                                <td class="px-4 py-3">
                                                                                    @if ($schedule->target_category === 'work_team')
                                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-100 dark:border-blue-800/30">
                                                                                            {{ __('Work Team') }}
                                                                                        </span>
                                                                                    @elseif($schedule->target_category === 'children')
                                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/30">
                                                                                            {{ __('Children') }}
                                                                                        </span>
                                                                                    @elseif($schedule->target_category === 'parents')
                                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-100 dark:border-purple-800/30">
                                                                                            {{ __('Parents') }}
                                                                                        </span>
                                                                                    @else
                                                                                        —
                                                                                    @endif
                                                                                </td>
                                                                                {{-- Assigned Groups --}}
                                                                                <td class="px-4 py-3">
                                                                                    @if ($schedule->periodGroups)
                                                                                        <div
                                                                                            class="flex flex-col gap-0.5">
                                                                                            <span
                                                                                                class="font-semibold text-zinc-800 dark:text-zinc-200">
                                                                                                {{ $schedule->periodGroups->status_name }}
                                                                                            </span>
                                                                                            @if ($schedule->periodGroups->description)
                                                                                                <span
                                                                                                    class="text-[10px] text-zinc-400 dark:text-zinc-500">
                                                                                                    {{ $schedule->periodGroups->description }}
                                                                                                </span>
                                                                                            @endif
                                                                                        </div>
                                                                                    @else
                                                                                        —
                                                                                    @endif
                                                                                </td>
                                                                                {{-- Teacher --}}
                                                                                <td class="px-4 py-3">
                                                                                    {{ $schedule->employee?->full_name ?? '—' }}
                                                                                </td>
                                                                                {{-- Actions --}}
                                                                                <td class="px-4 py-3 text-left">
                                                                                    <div
                                                                                        class="flex items-center gap-1 justify-end">
                                                                                        <button x-on:click="$dispatch('open-schedule-details', { id: {{ $schedule->id }} })" type="button" class="p-1 rounded-md text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800/50 transition-colors inline-flex items-center justify-center shrink-0 cursor-pointer" title="{{ __('View Details') }}">
                                                                                            <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                                                                        </button>
                                                                                        @if ($this->canCreateSchedule)
                                                                                            <a href="{{ route('educational-activity-schedules.create', ['copy_from' => $schedule->id]) }}" wire:navigate class="p-1 rounded-md text-amber-500 hover:text-amber-600 hover:bg-zinc-100 dark:hover:bg-zinc-800/50 transition-colors inline-flex items-center justify-center shrink-0 cursor-pointer" title="{{ __('Copy Schedule') }}">
                                                                                                <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 8.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v8.25A2.25 2.25 0 0 0 6 16.5h2.25m8.25-8.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-7.5A2.25 2.25 0 0 1 8.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 0 0-2.25 2.25v6" /></svg>
                                                                                            </a>
                                                                                            <a href="{{ route('educational-activity-schedules.edit', $schedule) }}" wire:navigate class="p-1 rounded-md text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800/50 transition-colors inline-flex items-center justify-center shrink-0 cursor-pointer" title="{{ __('Edit') }}">
                                                                                                <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                                                                            </a>
                                                                                            <button wire:click="delete({{ $schedule->id }})" wire:confirm="{{ __('Are you sure?') }}" type="button" class="p-1 rounded-md text-red-500 hover:text-red-600 hover:bg-zinc-100 dark:hover:bg-zinc-800/50 transition-colors inline-flex items-center justify-center shrink-0 cursor-pointer" title="{{ __('Delete') }}">
                                                                                                <svg class="size-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 9m-4.78 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                                                                            </button>
                                                                                        @endif
                                                                                        @if ($this->canViewAnyReport)
                                                                                            <flux:dropdown>
                                                                                                <flux:button
                                                                                                    variant="ghost"
                                                                                                    size="xs"
                                                                                                    icon="document-text"
                                                                                                    style="{{ $schedule->activityDetail?->status_id == 195 ? 'color: #ef4444 !important;' : ($schedule->activityDetail?->status_id == 194 ? 'color: #eab308 !important;' : ($schedule->activityDetail ? 'color: #3b82f6 !important;' : '')) }}"
                                                                                                    class="px-2 flex items-center gap-1 relative {{ !$schedule->activityDetail ? 'text-zinc-500 dark:text-zinc-400' : '' }}">
                                                                                                    {{ __('Report') }}
                                                                                                    @if ($schedule->activityDetail)
                                                                                                        <span
                                                                                                            class="absolute top-0 right-0 block h-1.5 w-1.5 rounded-full bg-{{ $schedule->activityDetail?->status_id == 195 ? 'red-500' : ($schedule->activityDetail?->status_id == 194 ? 'yellow-500' : 'blue-500') }} ring-1 ring-white dark:ring-zinc-900"></span>
                                                                                                    @endif
                                                                                                </flux:button>
                                                                                                <flux:menu>
                                                                                                    @if ($this->canCreateReport)
                                                                                                        <flux:menu.item
                                                                                                            wire:click="openReportModal('create', {{ $schedule->id }})"
                                                                                                            icon="plus">
                                                                                                            {{ __('Add Report') }}
                                                                                                        </flux:menu.item>
                                                                                                    @endif
                                                                                                    @if ($this->canUpdateReport)
                                                                                                        <flux:menu.item
                                                                                                            wire:click="openReportModal('edit', {{ $schedule->id }})"
                                                                                                            icon="pencil-square">
                                                                                                            {{ __('Edit Report') }}
                                                                                                        </flux:menu.item>
                                                                                                    @endif
                                                                                                    @if ($this->isSuperAdminOrSelectAnyStudent || $schedule->employee_id == $this->userEmployeeId || in_array($schedule->group_id, $this->userGroupIds))
                                                                                                        <flux:menu.item
                                                                                                            wire:click="openReportModal('show', {{ $schedule->id }})"
                                                                                                            icon="eye">
                                                                                                            {{ __('Show Report') }}
                                                                                                        </flux:menu.item>
                                                                                                    @endif
                                                                                                    @if ($this->canCreateReport)
                                                                                                        <flux:menu.item
                                                                                                            wire:click="openReportModal('gallery', {{ $schedule->id }})"
                                                                                                            icon="photo">
                                                                                                            {{ __('Add Attachments') }}
                                                                                                        </flux:menu.item>
                                                                                                    @endif
                                                                                                </flux:menu>
                                                                                            </flux:dropdown>
                                                                                        @endif
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div
                                class="flex flex-col items-center justify-center py-20 px-4 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 border-dashed">
                                <div
                                    class="bg-zinc-50 dark:bg-zinc-800/80 p-5 rounded-full mb-4 ring-8 ring-zinc-50 dark:ring-zinc-800/50">
                                    <flux:icon name="folder-open" class="size-10 text-zinc-400 dark:text-zinc-500" />
                                </div>
                                <h4 class="text-lg font-medium text-zinc-900 dark:text-zinc-100"
                                    style="color: rgb(71, 71, 255);">{{ __('No schedules found in this tree') }}</h4>
                                <p class="text-sm text-black-500 dark:text-zinc-400 mt-2 max-w-md text-center">
                                    {{ __('There are no schedules available for the selected batch. Please try selecting a different batch or modify your search filters.') }}
                                </p>
                            </div>
                        @endforelse
                    </div>
                @endif

                {{-- Pagination --}}
                @if ($this->schedules instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                        {{ $this->schedules->links() }}
                    </div>
                @endif
            @endif
        </div>


        {{-- Clone Month Modal --}}
        <flux:modal wire:model="showCloneMonthModal" class="md:w-[550px]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Clone Month\'s Schedules') }}</flux:heading>
                    <flux:subheading>
                        {{ __('Copy all schedules from one group for a specific month/year to other groups. Teacher assignments will not be copied.') }}
                    </flux:subheading>
                </div>

                <form wire:submit.prevent="cloneMonthSchedules" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <flux:select wire:model="cloneSourceMonth" :label="__('Month')">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">
                                        {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                                @endfor
                            </flux:select>
                        </div>
                        <div>
                            <flux:select wire:model="cloneSourceYear" :label="__('Year')">
                                @for ($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </flux:select>
                        </div>
                    </div>

                    <div>
                        <flux:select wire:model.live="cloneSourceGroupId" :label="__('Source Group')">
                            <option value="">-- {{ __('Select Source Group') }} --</option>
                            @foreach ($this->accessibleGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('cloneSourceGroupId')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:label>{{ __('Select Target Groups') }}</flux:label>
                        <div
                            class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-60 overflow-y-auto border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                            @foreach ($this->accessibleGroups as $group)
                                @if ($group->id != $cloneSourceGroupId)
                                    <label
                                        class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 p-1.5 rounded cursor-pointer">
                                        <input type="checkbox" wire:model="cloneTargetGroupIds"
                                            value="{{ $group->id }}"
                                            class="rounded border-zinc-300 dark:border-zinc-700 text-indigo-600 focus:ring-indigo-500">
                                        <span>{{ $group->name }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                        @error('cloneTargetGroupIds')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <flux:button wire:click="$set('showCloneMonthModal', false)" variant="ghost">
                            {{ __('Cancel') }}</flux:button>
                        <flux:button type="submit" variant="primary">{{ __('Confirm & Clone Month') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Report Modal --}}
        <flux:modal name="report-modal" class="w-full md:w-[800px]">
            <div class="space-y-4">
                @php
                    $detailModel = $selectedDetailId
                        ? \App\Models\EducationalActivityDetail::find($selectedDetailId)
                        : null;
                @endphp
                @if ($reportModalAction === 'create')
                    <livewire:org-app.educational-activity-detail.create :educational_activity_id="$selectedScheduleId" :isModal="true"
                        wire:key="create-{{ $selectedScheduleId }}" />
                @elseif($reportModalAction === 'edit' && $detailModel)
                    <livewire:org-app.educational-activity-detail.edit :detail="$detailModel" :isModal="true"
                        wire:key="edit-{{ $selectedDetailId }}" />
                @elseif($reportModalAction === 'show' && $detailModel)
                    <livewire:org-app.educational-activity-detail.show :detail="$detailModel" :isModal="true"
                        wire:key="show-{{ $selectedDetailId }}" />
                @elseif($reportModalAction === 'gallery' && $detailModel)
                    <div class="h-[600px] overflow-y-auto">
                        <livewire:org-app.educational-activity-detail.gallery :detail="$detailModel" :isModal="true"
                            wire:key="gallery-{{ $selectedDetailId }}" />
                    </div>
                @endif
            </div>
        </flux:modal>
    </div>
