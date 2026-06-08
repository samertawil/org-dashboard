<?php

namespace App\Livewire\OrgApp\EducationalActivitySchedules;


use App\Models\TeacherStudentGroup;
use App\Models\EducationalActivityDetail;
use App\Reposotries\StudentGroupRepo;
use App\Reposotries\EducationalActivityDetailRepo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class EducationalTasks extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterDate = '';
    public string $filterStatus = '';
    public string $filterEmployee = '';
    public string $filterGroup = '';
    
    public ?int $selectedGroupId = null;
    public ?string $selectedDate = null;
    public bool $showAttendanceModal = false;

    public ?int $selectedTaskIdForReport = null;
    public bool $showReportModal = false;

    protected $listeners = [
        'report-saved' => 'handleReportSaved',
        'attendance-saved' => '$refresh',
    ];

    public function handleReportSaved(): void
    {
        $this->showReportModal = false;
        $this->selectedTaskIdForReport = null;
    }

    protected $queryString = [
        'search'         => ['except' => ''],
        'filterDate'     => ['except' => ''],
        'filterStatus'   => ['except' => ''],
        'filterEmployee' => ['except' => ''],
        'filterGroup'    => ['except' => ''],
    ];

    public function mount()
    {
        if (!$this->isManager) {
            $this->filterEmployee = (string) (auth()->user()->employee?->id ?? '');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDate(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterEmployee(): void
    {
        $this->resetPage();
    }

    public function updatingFilterGroup(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterDate = '';
        $this->filterStatus = '';
        $this->filterGroup = '';

        if ($this->isManager) {
            $this->filterEmployee = '';
        }

        $this->resetPage();
    }

    #[Computed]
    public function employees()
    {
        return TeacherStudentGroup::employeesForEducationalTasks();
    }

    #[Computed]
    public function groups()
    {

        return StudentGroupRepo::activateEducationPointsWithEmployee();
    }

    #[Computed]
    public function selectedGroup()
    {
        return $this->selectedGroupId ? \App\Models\StudentGroup::find($this->selectedGroupId) : null;
    }

    #[Computed]
    public function tasks()
    {
        $isManager = $this->isManager;

        $query = EducationalActivityDetailRepo::getTeacherSchedulesQuery()
            ->with(['activityDetail', 'employee', 'activityDomain', 'activityNameStatus', 'periodGroups', 'group'])
            ->active();

        // 1. Employee Scoping
        if ($isManager && $this->filterEmployee !== '') {
            $query->where('employee_id', $this->filterEmployee);
        }

        // 2. Status Scoping
        if ($this->filterStatus !== '') {
            match ($this->filterStatus) {
                'completed' => $query->completed(),
                'happen_now' => $query->happenNow(),
                'delayed' => $query->delayed(),
                'require_today' => $query->requireToday(),
                'upcoming' => $query->upcoming(),
                default => null,
            };
        }

        // 3. Group Scoping
        if ($this->filterGroup !== '') {
            $query->where('group_id', $this->filterGroup);
        }

        // 4. Search Scoping
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->whereHas('activityNameStatus', function ($statusQuery) {
                    $statusQuery->where('status_name', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('activity_description', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%')
                    ->orWhereHas('group', function ($sub) {
                        $sub->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // 5. Date Scoping
        if ($this->filterDate !== '') {
            $query->whereDate('period_start', $this->filterDate);
        }

        return $query->ordered()->paginate(30);
    }

    #[Computed]
    public function attendanceByGroup()
    {
        // Build (group_id, date, period_group) triples from the current page of tasks
        $pairs = [];
        foreach ($this->tasks as $task) {
            if ($task->group_id && $task->period_start && $task->educational_period_groups) {
                $dateStr = $task->period_start->format('Y-m-d');
                $key = $task->group_id . '_' . $dateStr . '_' . $task->educational_period_groups;
                $pairs[$key] = [
                    'group_id'     => $task->group_id,
                    'date'         => $dateStr,
                    'period_group' => $task->educational_period_groups,
                ];
            }
        }

        if (empty($pairs)) {
            return collect();
        }

        $query = DB::table('student_daily_attendances')
            ->join('students', 'student_daily_attendances.student_id', '=', 'students.id')
            ->join('statuses', 'students.status_id', '=', 'statuses.id')
            ->where(function ($q) use ($pairs) {
                foreach ($pairs as $pair) {
                    $q->orWhere(function ($sub) use ($pair) {
                        $sub->where('student_daily_attendances.student_group_id', $pair['group_id'])
                            ->where('student_daily_attendances.attendance_date', $pair['date'])
                            ->where('students.status_id', $pair['period_group']);
                    });
                }
            })
            ->select(
                'student_daily_attendances.student_group_id',
                DB::raw("DATE(student_daily_attendances.attendance_date) as attendance_date"),
                'students.status_id',
                'statuses.status_name',
                DB::raw("SUM(CASE WHEN student_daily_attendances.status = 'present' THEN 1 ELSE 0 END) as present_count"),
                DB::raw("SUM(CASE WHEN student_daily_attendances.status = 'absent' THEN 1 ELSE 0 END) as absent_count"),
                DB::raw("COUNT(*) as total_count")
            )
            ->groupBy(
                'student_daily_attendances.student_group_id',
                DB::raw("DATE(student_daily_attendances.attendance_date)"),
                'students.status_id',
                'statuses.status_name'
            )
            ->get();

        // Key by "groupId_date_statusId" for precise per-task lookup
        return $query->groupBy(fn($row) => $row->student_group_id . '_' . $row->attendance_date . '_' . $row->status_id);
    }

    public function openAttendance(int $groupId, string $date): void
    {
        $this->selectedGroupId = $groupId;
        $this->selectedDate = $date;
        $this->showAttendanceModal = true;
    }

    public function closeAttendanceModal(): void
    {
        $this->showAttendanceModal = false;
        $this->selectedGroupId = null;
        $this->selectedDate = null;
    }

    public function openReport(int $taskId): void
    {
        $this->selectedTaskIdForReport = $taskId;
        $this->showReportModal = true;
    }

    public function closeReportModal(): void
    {
        $this->showReportModal = false;
        $this->selectedTaskIdForReport = null;
    }

    #[Computed]
    public function isManager(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || Gate::allows('select.any.student'));
    }

    #[Title('Educational Activity Tasks')]
    public function render()
    {
        Gate::authorize('create', EducationalActivityDetail::class);
        return view('livewire.org-app.educational-activity-schedules.educational-tasks', [
            'tasks'          => $this->tasks,
            'employees'      => $this->employees,
            'isManager'      => $this->isManager,
            'attendanceByGroup' => $this->attendanceByGroup,
        ]);
    }
}
