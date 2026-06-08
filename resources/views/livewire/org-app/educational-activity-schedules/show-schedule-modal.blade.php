<div>
    {{-- Global Schedule Show Modal --}}
    <flux:modal name="global-schedule-show-modal" class="w-full md:w-[900px] h-[90vh] overflow-y-auto">
        <div class="space-y-4">
            @if ($schedule)
                <livewire:org-app.educational-activity-schedules.show 
                    :schedule="$schedule" 
                    :isModal="true" 
                    wire:key="global-show-schedule-{{ $schedule->id }}" />
            @endif
        </div>
    </flux:modal>
</div>
