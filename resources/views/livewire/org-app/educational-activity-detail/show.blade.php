<div class="flex flex-col gap-6">
    @if(!$this->isModal)
        <div class="flex justify-between items-center">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            
            <div class="flex gap-2">
                <flux:button href="{{ route('educational-activity-detail.gallery', $detail->id) }}" wire:navigate variant="ghost" icon="photo" class="text-green-600">
                    {{ __('Gallery') }}
                </flux:button>
                <flux:button href="{{ route('educational-activity-detail.edit', $detail->id) }}" wire:navigate variant="ghost" icon="pencil-square" class="text-blue-600">
                    {{ __('Edit') }}
                </flux:button>
                <flux:button href="{{ route('educational-activity-detail.index') }}" wire:navigate variant="ghost" icon="list-bullet">
                    {{ __('List') }}
                </flux:button>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
        <dl class="grid grid-cols-1 {{ $this->isModal ? '' : 'sm:grid-cols-2' }} gap-x-6 gap-y-8">
            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-1' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Activity Name') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $detail->educationalActivity?->activity_name }}</dd>
            </div>
            
            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-1' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Consistent') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $detail->consistent ?? '-' }}</dd>
            </div>

            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-2' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('What Learned') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white whitespace-pre-wrap">{{ $detail->what_learned ?? '-' }}</dd>
            </div>

            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-2' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Teacher Report Detail') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white whitespace-pre-wrap">{{ $detail->teacher_report_detail ?? '-' }}</dd>
            </div>
        </dl>
    </div>
</div>
