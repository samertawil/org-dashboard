<div class="flex flex-col gap-6">

    {{-- Page Header --}}
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Calendar') }}</flux:heading>
            <flux:subheading>{{ __('Manage your events and schedules.') }}</flux:subheading>
        </div>
        {{-- Desktop: show full button --}}
        <flux:button wire:click="newEvent" icon="plus" variant="primary">
            {{ __('Add Event') }}
        </flux:button>
    </div>


    {{-- Calendar Card --}}
    <flux:card class="p-0 overflow-hidden">
        <div wire:ignore
             x-data="{
                calendar: null,
                events: @entangle('events'),
                jumpMonth: '',
                goToMonth(val) {
                    if (val && this.calendar) {
                        this.calendar.gotoDate(val + '-01');
                    }
                },
                init() {
                    let calendarEl = this.$refs.calendar;
                    let isMobile = window.innerWidth < 640;

                    // Set the jump input to current month on init
                    let now = new Date();
                    this.jumpMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');

                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: isMobile ? 'listWeek' : 'dayGridMonth',
                        headerToolbar: isMobile
                            ? { left: 'prev,next', center: 'title', right: 'today' }
                            : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
                        editable: true,
                        selectable: true,
                        events: this.events,
                        loading: function(isLoading) {},
                        select: (info) => {
                            $wire.newEvent(info.startStr);
                        },
                        eventClick: (info) => {
                            // Any event with a url → navigate there (read-only: activities, schedules)
                            if (info.event.url) {
                                info.jsEvent.preventDefault();
                                window.location.href = info.event.url;
                                return;
                            }
                            // Regular calendar events → open edit modal
                            $wire.editEvent(info.event.id);
                        },
                        eventDrop: (info) => {
                            if (!info.event.extendedProps.type || info.event.extendedProps.type === 'event') {
                                $wire.updateEventDrop(
                                    info.event.id,
                                    info.event.start.toISOString(),
                                    info.event.end ? info.event.end.toISOString() : null,
                                    info.event.allDay
                                );
                            } else {
                                info.revert();
                            }
                        },
                        eventResize: (info) => {
                             $wire.updateEventDrop(
                                info.event.id,
                                info.event.start.toISOString(),
                                info.event.end ? info.event.end.toISOString() : null,
                                info.event.allDay
                            );
                        },
                        eventDidMount: function(info) {
                            if (info.event.extendedProps.description) {
                                info.el.setAttribute('title', info.event.extendedProps.description);
                            }
                        }
                    });
                    this.calendar.render();

                    Livewire.on('refresh-calendar', ({ events }) => {
                        this.calendar.removeAllEvents();
                        this.calendar.addEventSource(events);
                    });
                }
             }"
        >
            {{-- Mobile-only: jump to month/year picker --}}
            <div class="sm:hidden flex items-center gap-2 px-3 pt-3">
                <flux:icon name="calendar-days" class="size-4 text-zinc-400 shrink-0" />
                <input
                    type="month"
                    x-model="jumpMonth"
                    x-on:change="goToMonth($event.target.value)"
                    class="flex-1 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div x-ref="calendar" class="min-h-[400px] sm:min-h-[600px] bg-white text-zinc-800 p-2 sm:p-4"></div>
        </div>
    </flux:card>


    {{-- Event Modal --}}
    <flux:modal wire:model="showModal" class="w-full sm:max-w-lg">
        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <div>
                <flux:heading size="lg">{{ $event_id ? __('Edit Event') : __('New Event') }}</flux:heading>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="title" label="{{ __('Event Title') }}" placeholder="e.g. Team Meeting" />

                {{-- Stack on mobile, 2 cols on sm+ --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:input type="datetime-local" wire:model="start" label="{{ __('Start') }}" />
                    <flux:input type="datetime-local" wire:model="end" label="{{ __('End') }}" />
                </div>

                <flux:checkbox wire:model="all_day" label="{{ __('All Day Event') }}" />

                <flux:textarea wire:model="description" label="{{ __('Description') }}" rows="3" />

                <flux:radio.group wire:model="class_name" label="{{ __('Color') }}">
                    <div class="grid grid-cols-3 gap-2 mt-1">
                        <flux:radio value="bg-blue-500"   label="Blue" />
                        <flux:radio value="bg-green-500"  label="Green" />
                        <flux:radio value="bg-red-500"    label="Red" />
                        <flux:radio value="bg-yellow-500" label="Yellow" />
                        <flux:radio value="bg-purple-500" label="Purple" />
                        <flux:radio value="bg-pink-500"   label="Pink" />
                        <flux:radio value="bg-lime-500"   label="Lime" />
                        <flux:radio value="bg-gray-300"   label="Gray" />
                    </div>
                </flux:radio.group>

                <div class="space-y-4 border-t pt-4">
                    <div class="flex items-center justify-between">
                        <flux:heading size="md">{{ __('Assignments / Tasks') }}</flux:heading>
                        <flux:button wire:click="addAssignee" size="sm" icon="plus">{{ __('Add Task') }}</flux:button>
                    </div>

                    @foreach($assignees as $index => $assignee)
                        <div class="p-3 sm:p-4 border rounded-lg space-y-3 bg-gray-50 dark:bg-zinc-800 relative">
                            <div class="flex justify-end">
                                <button wire:click="removeAssignee({{ $index }})" class="text-red-500 hover:text-red-700">
                                    <flux:icon name="trash" class="w-4 h-4" />
                                </button>
                            </div>

                            <flux:select wire:model="assignees.{{ $index }}.employee_id" label="{{ __('Employee') }}" placeholder="Select Employee">
                                @foreach($employees_list as $emp)
                                    <option value="{{ $emp['id'] }}">{{ $emp['name'] }}</option>
                                @endforeach
                            </flux:select>

                            <flux:textarea wire:model="assignees.{{ $index }}.notes" label="{{ __('Task Notes') }}" rows="2" placeholder="Instructions..." />

                            {{-- Stack on mobile --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <flux:select wire:model="assignees.{{ $index }}.status" label="{{ __('Status') }}">
                                    <option value="pending">{{ __('Pending') }}</option>
                                    <option value="completed">{{ __('Completed') }}</option>
                                    <option value="cancelled">{{ __('Cancelled') }}</option>
                                    <option value="postponed">{{ __('Postponed') }}</option>
                                    <option value="clarification_needed">{{ __('Clarification Needed') }}</option>
                                </flux:select>

                                <flux:textarea wire:model="assignees.{{ $index }}.response" label="{{ __('Response') }}" rows="1" placeholder="Employee Response..." />
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer Actions --}}
                <div class="flex justify-between pt-2">
                    @if($event_id)
                        <flux:button wire:click="deleteEvent" variant="danger" icon="trash">{{ __('Delete') }}</flux:button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex gap-2">
                        <flux:button wire:click="$set('showModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
                        <flux:button wire:click="saveEvent" variant="primary">{{ __('Save') }}</flux:button>
                    </div>
                </div>
            </div>
        </div>
    </flux:modal>


    @assets
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        /* Minimal Customization to match Metronic/Tailwind */
        .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 600 !important; }
        .fc-button-primary { background-color: #3b82f6 !important; border-color: #3b82f6 !important; }
        .fc-button-primary:hover { background-color: #2563eb !important; border-color: #2563eb !important; }
        .fc-event { cursor: pointer; border: none; font-size: 0.75rem !important; } 
        
        /* Allow text wrap */
        .fc-event-title, .fc-event-main {
            white-space: normal !important;
            overflow-wrap: break-word !important;
        }
        .fc-daygrid-event {
            white-space: normal !important;
            height: auto !important;
            margin-bottom: 6px !important; /* Spacing between events */
            margin-right: 4px !important; /* Spacing between events */
            margin-left: 4px !important; /* Spacing between events */
        }
        
        /* Map tailwind colors - Light variants for Activities */
        /* Ensure text color applies to all children to override FC defaults */
        
        /* Blue (Planned) */
        .bg-blue-100 { background-color: #dbeafe !important; border-color: #bfdbfe !important; }
        .text-blue-800, .text-blue-800 * { color: #1e40af !important; }

        /* Green (Completed) */
        .bg-green-100 { background-color: #dcfce7 !important; border-color: #bbf7d0 !important; }
        .text-green-800, .text-green-800 * { color: #166534 !important; }

        /* Red (Rejected/Cancelled) */
        .bg-red-100 { background-color: #fee2e2 !important; border-color: #fecaca !important; }
        .text-red-800, .text-red-800 * { color: #991b1b !important; }

        /* Yellow (In Progress) */
        .bg-yellow-100 { background-color: #fef9c3 !important; border-color: #fde047 !important; }
        .text-yellow-800, .text-yellow-800 * { color: #854d0e !important; }

        /* Purple */
        .bg-purple-100 { background-color: #f3e8ff !important; border-color: #e9d5ff !important; }
        .text-purple-800, .text-purple-800 * { color: #6b21a8 !important; }

        /* Teal */
        .bg-teal-100 { background-color: #ccfbf1 !important; border-color: #99f6e4 !important; }
        .text-teal-800, .text-teal-800 * { color: #115e59 !important; }

        /* Indigo (Undefined) */
        .bg-indigo-100 { background-color: #e0e7ff !important; border-color: #c7d2fe !important; }
        .text-indigo-800, .text-indigo-800 * { color: #3730a3 !important; }

        /* Orange (Need Procedure) */
        .bg-orange-100 { background-color: #ffedd5 !important; border-color: #fed7aa !important; }
        .text-orange-800, .text-orange-800 * { color: #9a3412 !important; }

        /* Zinc/Gray */
        .bg-zinc-100 { background-color: #f4f4f5 !important; border-color: #e4e4e7 !important; }
        .text-zinc-800, .text-zinc-800 * { color: #27272a !important; }


        /* Original strong colors for standard events if needed */
        .bg-blue-500   { background-color: #2563eb !important; }
        .bg-green-500  { background-color: #22c55e !important; }
        .bg-red-500    { background-color: #ef4444 !important; }
        .bg-yellow-500 { background-color: #eab308 !important; }
        .bg-purple-500 { background-color: #a855f7 !important; }
        .bg-gray-300   { background-color: #d1d5db !important; }
        .bg-lime-500   { background-color: #84cc16 !important; }
        .bg-pink-500   { background-color: #ec4899 !important; }
    </style>
    @endassets
</div>
