<flux:card class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg" icon="clipboard-document-list">{{ __('My Priority Tasks') }}</flux:heading>
        <flux:button href="{{ route('calendar.index') }}" variant="ghost" size="sm" icon="arrow-right">
            {{ __('View Calendar') }}</flux:button>
    </div>

    @if ($tasks->isEmpty())
        <div class="text-zinc-500 text-sm py-4 text-center">
            {{ __('No pending tasks assigned to you.') }}
        </div>
    @else
        <div class="space-y-3">
            @foreach ($tasks as $task)
                <div class="p-3 border rounded-lg bg-white dark:bg-zinc-800 flex flex-col gap-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold text-sm">{{ $task->event->title ?? 'Untitled Event' }}</div>
                            <div class="text-xs text-zinc-500">
                                {{ $task->event->start->format('M d, H:i') }}
                                @if ($task->assigner)
                                    <span class="text-xs text-blue-600 dark:text-blue-400">by
                                        {{ $task->assigner->name }}</span>
                                @endif
                                @if ($task->notes)
                                    - <span class="italic text-zinc-600">{{ Str::limit($task->notes, 50) }}</span>
                                @endif
                            </div>
                        </div>
                        <flux:badge size="sm" color="{{ $task->status === 'pending' ? 'yellow' : 'zinc' }}">
                            {{ ucfirst($task->status) }}</flux:badge>
                    </div>
 
                    <flux:input type="text" wire:model="responses.{{ $task->id }}" :label="__('Response')"
                placeholder="Employee Response..." />
                      
                    <div class="flex gap-2 justify-end">
                        @if ($task->status !== 'completed')
                            <flux:button wire:click="updateStatus({{ $task->id }}, 'completed')" size="xs"
                                variant="primary">{{ __('Complete') }}</flux:button>
                        @endif
                    </div>
                </div>
            @endforeach
        
        </div>
    @endif
</flux:card>
