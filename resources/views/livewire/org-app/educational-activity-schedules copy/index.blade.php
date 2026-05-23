<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Educational Activity Schedules') }}</flux:heading>
            <flux:subheading>{{ __('Manage scheduled educational activities for children') }}</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:button wire:click="export" variant="outline" icon="document-arrow-down">
                {{ __('Export Excel') }}
            </flux:button>
            @can('educational-activity-schedules.create')
                <flux:button wire:click="openCloneMonthModal" variant="outline" icon="document-duplicate">
                    {{ __('Clone Month') }}
                </flux:button>
                <flux:button href="{{ route('educational-activity-schedules.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('Add Schedule') }}
                </flux:button>
            @endcan
        </div>
    </div>

    {{-- Success/Error Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />
    @if (session('error'))
        <div class="p-3 bg-red-100 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 text-center rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">

        {{-- Filters --}}
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 relative">

                {{-- Search --}}
                <div class="md:col-span-1 relative">
                    <flux:input wire:model.live="search"
                        :placeholder="__('Search by activity name or notes...')"
                        icon="magnifying-glass" />
                    <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                    </div>
                </div>

                {{-- Student Group Filter --}}
                <div>
                    <flux:select wire:model.live="filterGroup">
                        <option value="">-- {{ __('All Student Groups') }} --</option>
                        @foreach(\App\Reposotries\StudentGroupRepo::studentGroups()->sortByDesc('id') as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                {{-- Category Filter --}}
                <div>
                    <flux:select wire:model.live="filterCategory">
                        <option value="">-- {{ __('All Categories') }} --</option>
                        @foreach(\App\Models\ActivitySchedule::TARGET_CATEGORIES as $key => $label)
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

            @if($search || $filterCategory || $filterDomain || $filterGroup || $filterDateFrom || $filterDateTo)
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
                {{ __('Showing') }}
                <span class="font-medium text-zinc-900 dark:text-white">{{ $this->schedules->firstItem() }}</span>
                {{ __('to') }}
                <span class="font-medium text-zinc-900 dark:text-white">{{ $this->schedules->lastItem() }}</span>
                {{ __('of') }}
                <span class="font-medium text-zinc-900 dark:text-white">{{ $this->schedules->total() }}</span>
                {{ __('results') }}
            </p>
        </div>

        {{-- Mobile Cards View (Hidden on medium and larger screens) --}}
        <div class="block md:hidden">
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($this->schedules as $schedule)
                    <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $schedule->activity_name }}</span>
                                @if($schedule->activity_description)
                                    <span class="text-xs text-zinc-500 truncate max-w-[200px]">{{ $schedule->activity_description }}</span>
                                @endif
                            </div>
                            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">
                                {{ $schedule->activityDomain?->status_name ?? '—' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-1">{{ __('Category') }}</span>
                                <div class="text-xs text-zinc-600 dark:text-zinc-300">
                                    @if($schedule->target_category === 'work_team')
                                        <flux:badge color="blue" size="sm">{{ __('Work Team') }}</flux:badge>
                                    @elseif($schedule->target_category === 'children')
                                        <flux:badge color="green" size="sm">{{ __('Children') }}</flux:badge>
                                    @elseif($schedule->target_category === 'parents')
                                        <flux:badge color="purple" size="sm">{{ __('Parents') }}</flux:badge>
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-1">{{ __('Group') }}</span>
                                <div class="text-xs font-medium text-zinc-700 dark:text-zinc-300 leading-tight">
                                    {{ $schedule->periodGroups?->status_name ?? '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-1">{{ __('Schedule Time') }}</span>
                                <div class="text-xs text-zinc-600 dark:text-zinc-300">
                                    @if($schedule->period_start)
                                        <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $schedule->period_start->format('Y-m-d') }}</span>
                                        @if($schedule->period_end && $schedule->period_start->isSameDay($schedule->period_end))
                                            <span class="text-zinc-500">({{ $schedule->period_start->format('H:i') }} → {{ $schedule->period_end->format('H:i') }})</span>
                                        @else
                                            <span class="text-zinc-500">({{ $schedule->period_start->format('H:i') }})</span>
                                            @if($schedule->period_end)
                                                <span class="block text-zinc-500 mt-1">To: <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $schedule->period_end->format('Y-m-d H:i') }}</span></span>
                                            @endif
                                        @endif
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800/50">
                            <div class="text-xs text-zinc-500">
                                <span class="font-medium text-zinc-700 dark:text-zinc-400">{{ __('Moderator / Teacher') }}:</span> 
                                {{ $schedule->employee?->full_name ?? '—' }}
                            </div>
                            <div class="flex items-center gap-1">
                                <span title="{{ __('View Details') }}">
                                    <flux:button href="{{ route('educational-activity-schedules.show', $schedule) }}" wire:navigate variant="ghost" size="xs" icon="eye" />
                                </span>
                                @can('educational-activity-schedules.create')
                                    <span title="{{ __('Edit') }}">
                                        <flux:button href="{{ route('educational-activity-schedules.edit', $schedule) }}" wire:navigate variant="ghost" size="xs" icon="pencil-square" />
                                    </span>
                                    <span title="{{ __('Delete') }}">
                                        <flux:button wire:click="delete({{ $schedule->id }})" wire:confirm="{{ __('Are you sure?') }}" variant="ghost" size="xs" icon="trash" class="text-red-500" />
                                    </span>
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-zinc-500 italic">
                        {{ __('No schedules found.') }}
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-auto custom-scrollbar" style="max-height: 70vh;">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700 border-separate border-spacing-0">
                <thead class="bg-zinc-50 dark:bg-zinc-900 sticky top-0 z-20">
                    <tr>
                        {{-- Sticky Left: Activity Name --}}
                        <th wire:click="sortBy('activity_name')"
                            class="sticky left-0 bg-zinc-50 dark:bg-zinc-900 z-30 px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors border-b border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center gap-1">
                                {{ __('Activity Name') }}
                                @if($sortField === 'activity_name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                            {{ __('Domain') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                            {{ __('Category') }}
                        </th>
                        <th wire:click="sortBy('period_start')"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors border-b border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center gap-1">
                                {{ __('Start') }}
                                @if($sortField === 'period_start')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('period_end')"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors border-b border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center gap-1">
                                {{ __('End') }}
                                @if($sortField === 'period_end')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                            {{ __('Group') }}
                        </th>

                        {{-- Sticky Right: Actions --}}
                        <th scope="col"
                            class="sticky right-0 bg-zinc-50 dark:bg-zinc-900 z-30 px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-700">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->schedules as $schedule)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">

                            {{-- Sticky Left: Activity Name --}}
                            <td class="sticky left-0 bg-white dark:bg-zinc-800 z-10 px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-700/50 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] dark:shadow-[2px_0_5px_-2px_rgba(0,0,0,0.3)]">
                                <div class="max-w-[220px]">
                                    <p class="font-semibold truncate">{{ $schedule->activity_name }}</p>
                                    @if($schedule->activity_description)
                                        <p class="text-xs text-zinc-400 mt-0.5 truncate">{{ $schedule->activity_description }}</p>
                                    @endif
                                </div>
                            </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300 border-b border-zinc-100 dark:border-zinc-700/50">
                                {{ $schedule->activityDomain?->status_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm border-b border-zinc-100 dark:border-zinc-700/50">
                                @if($schedule->target_category === 'work_team')
                                    <flux:badge color="blue" size="sm">{{ __('Work Team') }}</flux:badge>
                                @elseif($schedule->target_category === 'children')
                                    <flux:badge color="green" size="sm">{{ __('Children') }}</flux:badge>
                                @elseif($schedule->target_category === 'parents')
                                    <flux:badge color="purple" size="sm">{{ __('Parents') }}</flux:badge>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300 border-b border-zinc-100 dark:border-zinc-700/50">
                                {{ $schedule->period_start?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300 border-b border-zinc-100 dark:border-zinc-700/50">
                                @if($schedule->period_end)
                                    @if($schedule->period_start && $schedule->period_start->isSameDay($schedule->period_end))
                                        {{-- Same day: show time only --}}
                                        <span class="text-zinc-400 text-xs mr-1">{{ $schedule->period_start->format('H:i') }}</span>
                                        <span class="font-medium">→ {{ $schedule->period_end->format('H:i') }}</span>
                                    @else
                                        {{-- Different day: show full date & time --}}
                                        {{ $schedule->period_end->format('Y-m-d H:i') }}
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                           
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300 border-b border-zinc-100 dark:border-zinc-700/50">
                                {{ $schedule->periodGroups?->status_name ?? '—' }}
                            </td>

                            {{-- Sticky Right: Actions --}}
                            <td class="sticky right-0 bg-white dark:bg-zinc-800 z-10 px-6 py-4 whitespace-nowrap text-right text-sm font-medium border-b border-zinc-100 dark:border-zinc-700/50 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)] dark:shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.3)]">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('educational-activity-schedules.show', $schedule) }}"
                                        wire:navigate variant="ghost" size="sm" icon="eye" />
                                    @can('educational-activity-schedules.create')
                                        <flux:button href="{{ route('educational-activity-schedules.edit', $schedule) }}"
                                            wire:navigate variant="ghost" size="sm" icon="pencil-square" />
                                        <flux:button wire:click="delete({{ $schedule->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this schedule?') }}"
                                            variant="ghost" size="sm" icon="trash"
                                            class="text-red-500 hover:text-red-600" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-zinc-500">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="calendar-days" class="size-10 text-zinc-300" />
                                    <p>{{ __('No schedules found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->schedules->links() }}
        </div>
    </div>


    {{-- Clone Month Modal --}}
    <flux:modal wire:model="showCloneMonthModal" class="md:w-[550px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Clone Month\'s Schedules') }}</flux:heading>
                <flux:subheading>{{ __('Copy all schedules from one group for a specific month/year to other groups. Teacher assignments will not be copied.') }}</flux:subheading>
            </div>

            <form wire:submit.prevent="cloneMonthSchedules" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <flux:select wire:model="cloneSourceMonth" :label="__('Month')">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
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
                        @foreach($this->studentGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </flux:select>
                    @error('cloneSourceGroupId')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:label>{{ __('Select Target Groups') }}</flux:label>
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-60 overflow-y-auto border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                        @foreach($this->studentGroups as $group)
                            @if ($group->id != $cloneSourceGroupId)
                                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 p-1.5 rounded cursor-pointer">
                                    <input type="checkbox" wire:model="cloneTargetGroupIds" value="{{ $group->id }}" class="rounded border-zinc-300 dark:border-zinc-700 text-indigo-600 focus:ring-indigo-500">
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
                    <flux:button wire:click="$set('showCloneMonthModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
                    <flux:button type="submit" variant="primary">{{ __('Confirm & Clone Month') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
