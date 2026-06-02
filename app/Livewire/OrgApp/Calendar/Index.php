<?php

namespace App\Livewire\OrgApp\Calendar;



use App\Models\Activity;
use App\Models\ActivitySchedule;
use App\Models\Employee;
use App\Models\Event;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    public $events = [];
    public $showModal = false;
    public $employees_list = [];

    // Form fields
    public $event_id;
    public $title;
    public $start;
    public $end;
    public $description;
    public $class_name = 'bg-blue-500'; // Default blue
    public $all_day = false;

    public $assignees = []; // [['employee_id' => '', 'notes' => '', 'status' => 'pending', 'response' => '']]

    public function mount()
    {
        $this->loadEmployees();
        $this->loadEvents();
    }

    public function loadEmployees()
    {
        $this->employees_list = Employee::with('user')->get()->map(function ($emp) {
            return [
                'id' => $emp->id,
                'name' => $emp->full_name,
            ];
        })->values()->toArray();
    }

    public function loadEvents()
    {
        $events = Event::with('assignees.employee.user')->get()->map(function ($event) {

            $assigneeSummary = '';
            if ($event->assignees->count() > 0) {
                $assigneeSummary = ' [' . $event->assignees->count() . ' Tasks]';
            }

            return [
                'id' => $event->id,
                'title' => $event->title . $assigneeSummary,
                'start' => $event->start->toIso8601String(),
                'end' => $event->end ? $event->end->toIso8601String() : null,
                'allDay' => $event->all_day,
                'className' => $event->class_name,
                'description' => $event->description,
                'extendedProps' => [
                    'description' => $event->description,
                    'type' => 'event',
                    'assignees' => $event->assignees->map(fn($a) => [
                        'name' => $a->employee->user->name ?? 'Unknown',
                        'status' => $a->status
                    ])
                ]
            ];
        });

        $events = $events->toBase();

        $activities = collect();
        if (Gate::allows('manager.reports.all') || Gate::allows('calendar.activity.sector')) {

            $activities = \App\Models\Activity::with(['activityStatus', 'attachments', 'statusSpecificSector'])->get()->map(function ($activity) {

                $statusName = $activity->status_info['name'] ?? 'Unknown';
                $statusColor = $activity->status_info['color'] ?? 'zinc'; // Default to zinc

                $fullSectorName = $activity->statusSpecificSector->status_name ?? '';
                // Get only the part before '-'
                $sectorName = trim(explode('-', $fullSectorName)[0]);

                // Use light background (-100) and dark text (-800) for "light" look
                $className = 'bg-' . $statusColor . '-100 text-' . $statusColor . '-800 border border-' . $statusColor . '-200';

                // Construct title: Name - Sector (if exists) - (Status)
                $title = $activity->name;
                if ($sectorName) {
                    $title .= ' - ' . $sectorName;
                }
                $title .= ' (' . $statusName . ')';

                return [
                    'id' => 'activity-' . $activity->id, // Unique ID prefix
                    'title' => $title,
                    'start' => \Carbon\Carbon::parse($activity->start_date)->toIso8601String(),
                    'end' => $activity->end_date ? \Carbon\Carbon::parse($activity->end_date)->toIso8601String() : null,
                    'allDay' => true, // Activities usually span days
                    'className' => $className, // Dynamic light color
                    'url' => route('activity.show', $activity->id), // Direct link
                    'editable' => false, // Activities are read-only in calendar
                    'extendedProps' => [
                        'description' => 'Activity: ' . $activity->name . ($sectorName ? ' | Sector: ' . $sectorName : ''),
                        'type' => 'activity',
                        'status' => $statusName,
                        'sector' => $sectorName,
                    ]
                ];
            });

            $activities = $activities->toBase();
        }

        $schedules = collect();
        // Educational Activity Schedules
        if (Gate::allows('manager.reports.all') || Gate::allows('calendar.education.sector')) {

            $schedules = ActivitySchedule::with(['group', 'activityDomain', 'employee'])
                ->whereNotNull('period_start')
                ->whereNotNull('period_end')
                ->where('activation', 1)
                ->get()
                ->groupBy(function ($schedule) {
                    // Group by activity_name, start time, end time, and activity domain to identify duplicates
                    return $schedule->activity_name . '___' .
                        $schedule->period_start->toDateTimeString() . '___' .
                        $schedule->period_end->toDateTimeString() . '___' .
                        $schedule->educational_activity_domain;
                })
                ->map(function ($groupSchedules) {
                    $firstSchedule = $groupSchedules->first();

                    // Extract group names and shorten them
                    $groupNames = [];
                    foreach ($groupSchedules as $s) {
                        if ($s->group) {
                            $groupNames[] = $s->group->short_name;
                        }
                    }
                    $groupNames = array_unique(array_filter($groupNames));
                    $allGroupsStr = implode(', ', $groupNames);

                    $domainName   = $firstSchedule->activityDomain?->status_name ?? '';
                    $employeeNames = $groupSchedules->map(fn($s) => $s->employee?->full_name)->filter()->unique()->implode(', ');

                    $title = $firstSchedule->activity_name .  ' - ' . $firstSchedule->periodGroups?->description ?? '';
                    if ($allGroupsStr) {
                        $title .= ' | ' . $allGroupsStr;
                    }

                    return [
                        'id'        => 'schedule-' . $firstSchedule->id,
                        'title'     => $title,
                        'start'     => Carbon::parse($firstSchedule->period_start)->toIso8601String(),
                        'end'       => Carbon::parse($firstSchedule->period_end)->toIso8601String(),
                        'allDay'    => false,
                        'className' => 'bg-teal-100 text-teal-800 border border-teal-300',
                        'url'       => route('educational-activity-schedules.show', $firstSchedule->id),
                        'editable'  => false,
                        'extendedProps' => [
                            'description' => implode(' | ', array_filter([
                                $firstSchedule->activity_description,
                                $domainName   ? 'Group: ('   . $firstSchedule->periodGroups?->description . ')/' . $firstSchedule->periodGroups?->status_name  : null,
                                // $allGroupsStr ? 'Point: '   . $allGroupsStr : null,

                                $employeeNames ? 'Teachers: ' . $employeeNames : null,
                                'End At :' . Carbon::parse($firstSchedule->period_end)->format('g:i A')
                            ])),
                            'type'   => 'schedule',
                            'domain' => $domainName,
                            'group'  => $allGroupsStr,
                        ],
                    ];
                })
                ->values()
                ->toBase();
        }

        // Merge events + activities + schedules
        $this->events = $events->merge($activities)->merge($schedules)
            ->unique('id')
            ->values()
            ->toArray();
    }

    public function newEvent($date = null)
    {

        $this->resetForm();
        if ($date) {
            $parsed = Carbon::parse($date);
            $this->start = $parsed->format('Y-m-d\\TH:i');
            $this->end   = $parsed->addHour()->format('Y-m-d\\TH:i'); // Default end = start + 1 hour
        } else {
            $this->start = now()->format('Y-m-d\\TH:i');
            $this->end   = now()->addHour()->format('Y-m-d\\TH:i');
        }
        $this->showModal = true;
    }

    public function editEvent($id)
    {
        $event = Event::with('assignees')->find($id);
        if (!$event) return;

        $this->event_id = $event->id;
        $this->title = $event->title;
        $this->start = $event->start->format('Y-m-d\TH:i');
        $this->end = $event->end ? $event->end->format('Y-m-d\TH:i') : null;
        $this->description = $event->description;
        $this->class_name = $event->class_name;
        $this->all_day = $event->all_day;

        $this->assignees = $event->assignees->map(function ($a) {
            return [
                'employee_id' => $a->employee_id,
                'notes' => $a->notes,
                'status' => $a->status,
                'response' => $a->response
            ];
        })->toArray();

        $this->showModal = true;
    }

    public function addAssignee()
    {
        $this->assignees[] = [
            'employee_id' => '',
            'notes' => '',
            'status' => 'pending',
            'response' => ''
        ];
    }

    public function removeAssignee($index)
    {
        unset($this->assignees[$index]);
        $this->assignees = array_values($this->assignees);
    }

    public function saveEvent()
    {
        $this->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
        ]);

        $data = [
            'title' => $this->title,
            'start' => $this->start,
            'end' => $this->end,
            'description' => $this->description,
            'class_name' => $this->class_name,
            'all_day' => $this->all_day,
        ];

        if ($this->event_id) {
            $event = Event::find($this->event_id);
            $event->update($data);
        } else {
            $data['created_by'] = auth()->id();
            $event = Event::create($data);
        }

        // Handle Assignees
        // Simple approach: delete all and recreate (easiest for now, can optimize if needed)
        // Ensure we don't lose status if we are just updating details?
        // Actually, since $assignees is loaded from DB, users edit it, we can just sync.
        // But sync is for many-to-many. This is HasMany with custom fields.

        $event->assignees()->delete();
        foreach ($this->assignees as $assignee) {
            if (!empty($assignee['employee_id'])) {
                $event->assignees()->create([
                    'employee_id' => $assignee['employee_id'],
                    'notes' => $assignee['notes'] ?? null,
                    'status' => $assignee['status'] ?? 'pending',
                    'response' => $assignee['response'] ?? null,
                    'assigned_by' => auth()->id()
                    // response is usually edited by employee, but admin can see/edit it too here
                ]);
            }
        }

        $this->showModal = false;
        $this->loadEvents();
        $this->dispatch('refresh-calendar', events: $this->events); // Update JS calendar
    }

    public function updateEventDrop($id, $start, $end, $allDay)
    {
        $event = Event::find($id);
        if ($event) {
            $event->update([
                'start' => Carbon::parse($start),
                'end' => $end ? Carbon::parse($end) : null,
                'all_day' => $allDay
            ]);
        }
    }

    public function deleteEvent()
    {
        if ($this->event_id) {
            Event::destroy($this->event_id);
            $this->showModal = false;
            $this->loadEvents();
            $this->dispatch('refresh-calendar', events: $this->events);
        }
    }

    public function resetForm()
    {
        $this->event_id = null;
        $this->title = '';
        $this->start = now()->format('Y-m-d\TH:i');
        $this->end = null;
        $this->description = null;
        $this->class_name = 'bg-blue-500';
        $this->all_day = false;
        $this->assignees = [];
    }

    #[Title('Calendar')]
    public function render(): View
    {
        if (Gate::allows('manager.reports.all') || Gate::allows('calendar.activity.sector') || Gate::allows('calendar.education.sector')) {
            return view('livewire.org-app.calendar.index');
        }
        abort(403, 'You do not have the necessary permissions.');
    }
}
