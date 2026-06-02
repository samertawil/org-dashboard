<?php

namespace App\Livewire\OrgApp\Dashboard;


use App\Models\TeacherStudentGroup;
use App\Models\EducationalActivityDetail;
use App\Reposotries\StudentGroupRepo;
use App\Reposotries\EducationalActivityDetailRepo;
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

    protected $queryString = [
        'search'         => ['except' => ''],
        'filterDate'     => ['except' => ''],
        'filterStatus'   => ['except' => ''],
        'filterEmployee' => ['except' => ''],
        'filterGroup'    => ['except' => ''],
    ];

    public function mount()
    {
        $user = auth()->user();
        $isManager = $user->isSuperAdmin() || Gate::allows('select.any.student');

        if (!$isManager) {
            $this->filterEmployee = (string) ($user->employee?->id ?? '');
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

        $user = auth()->user();
        $isManager = $user->isSuperAdmin() || Gate::allows('select.any.student');
        if ($isManager) {
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
    public function tasks()
    {
        $user = auth()->user();
        $isManager = $user->isSuperAdmin() || Gate::allows('select.any.student');

        $query = EducationalActivityDetailRepo::getTeacherSchedulesQuery()
            ->with(['activityDetail', 'employee', 'activityDomain', 'periodGroups', 'group'])
            ->active();

        // 1. Employee Scoping
        if ($isManager && $this->filterEmployee !== '') {
            $query->where('employee_id', $this->filterEmployee);
        }

        // 2. Status Scoping
        if ($this->filterStatus !== '') {
            match ($this->filterStatus) {
                'completed' => $query->completed(),
                'delayed' => $query->delayed(),
                'required_now' => $query->requiredNow(),
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
                $q->where('activity_name', 'like', '%' . $this->search . '%')
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

        return $query->ordered()->paginate(10);
    }

    #[Title('Educational Activity Tasks')]
    public function render()
    {
        Gate::authorize('create', EducationalActivityDetail::class);
        $isManager = auth()->user()->isSuperAdmin() || Gate::allows('select.any.student');
        return view('livewire.org-app.dashboard.educational-tasks', [
            'tasks'     => $this->tasks,
            'employees' => $this->employees,
            'isManager' => $isManager,
        ]);
    }
}
