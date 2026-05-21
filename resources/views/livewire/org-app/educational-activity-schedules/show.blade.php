<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ __('View educational activity schedule details') }}</flux:subheading>
        </div>
        <div class="flex gap-2">
            @can('educational-activity-schedules.create')
                <flux:button href="{{ route('educational-activity-schedules.edit', $schedule) }}" wire:navigate variant="primary" icon="pencil-square">
                    {{ __('Edit') }}
                </flux:button>
            @endcan
            <flux:button href="{{ route('educational-activity-schedules.index') }}" wire:navigate variant="ghost" icon="arrow-left">
                {{ __('Back to List') }}
            </flux:button>
        </div>
    </div>

    {{-- Activity Information --}}
    <flux:card>
        <div class="flex items-center gap-2 mb-4">
            <flux:icon name="calendar-days" class="size-5 text-blue-500" />
            <flux:heading size="lg">{{ __('Activity Information') }}</flux:heading>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Activity Name') }}</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $schedule->activity_name }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Main Activity') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->activity?->name ?? '—' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Description') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->activity_description ?? '—' }}</p>
            </div>
        </div>
    </flux:card>

    {{-- Classification --}}
    <flux:card>
        <div class="flex items-center gap-2 mb-4">
            <flux:icon name="tag" class="size-5 text-purple-500" />
            <flux:heading size="lg">{{ __('Classification') }}</flux:heading>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Activity Domain') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->activityDomain?->status_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Target Category') }}</p>
                @if($schedule->target_category === 'work_team')
                    <flux:badge color="blue">{{ __('Work Team') }}</flux:badge>
                @elseif($schedule->target_category === 'children')
                    <flux:badge color="green">{{ __('Children') }}</flux:badge>
                @else
                    <p class="text-sm text-zinc-400">—</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Assigned Groups') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->periodGroups?->status_name ?? '—' }}</p>
                <span class="text-sm text-zinc-700 dark:text-zinc-300">({{ $schedule->periodGroups?->description ?? '—' }})</span>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Student Point') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->group?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Responsible Employee') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->employee?->full_name ?? '—' }}</p>
            </div>
        </div>
    </flux:card>

    {{-- Time Period --}}
    <flux:card>
        <div class="flex items-center gap-2 mb-4">
            <flux:icon name="clock" class="size-5 text-green-500" />
            <flux:heading size="lg">{{ __('Time Period') }}</flux:heading>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Day') }}</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $schedule->day_name_attribute }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Start Date & Time') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->period_start?->format('Y-m-d h:i A') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('End Date & Time') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->period_end?->format('Y-m-d h:i A') ?? '—' }}</p>
            </div>
        </div>
    </flux:card>

    {{-- Additional Info --}}
    <flux:card>
        <div class="flex items-center gap-2 mb-4">
            <flux:icon name="information-circle" class="size-5 text-zinc-500" />
            <flux:heading size="lg">{{ __('Additional Information') }}</flux:heading>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Notes') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->notes ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Sort Order') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->sort_order }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Status') }}</p>
                @if($schedule->activation)
                    <flux:badge color="green">{{ __('Active') }}</flux:badge>
                @else
                    <flux:badge color="red">{{ __('Inactive') }}</flux:badge>
                @endif
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Created By') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->createdBy?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">{{ __('Created At') }}</p>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->created_at?->format('Y-m-d H:i') ?? '—' }}</p>
            </div>
        </div>
    </flux:card>
</div>
