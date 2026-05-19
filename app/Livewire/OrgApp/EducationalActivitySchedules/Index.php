<?php

namespace App\Livewire\OrgApp\EducationalActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\EducationalActivitySchedulesExport;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $filterDomain   = '';
    public string $filterCategory = '';
    public string $filterGroup    = '';
    public string $filterDateFrom = '';
    public string $filterDateTo   = '';
    public $sortField             = 'period_start';
    public $sortDirection         = 'desc';
    public int $perPage           = 10;
    public string $viewType       = 'tree';



    // Clone month properties
    public bool $showCloneMonthModal = false;
    public $cloneSourceMonth;
    public $cloneSourceYear;
    public $cloneSourceGroupId;
    public array $cloneTargetGroupIds = [];

    protected $queryString = [
        'search'         => ['except' => ''],
        'filterDomain'   => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterGroup'    => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo'   => ['except' => ''],
        'viewType'       => ['except' => 'tree'],
    ];

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDomain(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatingFilterGroup(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo(): void
    {
        $this->resetPage();
    }

    #[Computed()]
    public function schedules()
    {
        return ActivitySchedule::query()
            ->with(['activityDomain', 'group', 'employee', 'periodGroups'])
            ->when($this->search, fn($q) =>
                $q->where('activity_name', 'like', '%' . $this->search . '%')
                  ->orWhere('target_category', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterDomain, fn($q) =>
                $q->where('educational_activity_domain', $this->filterDomain)
            )
            ->when($this->filterCategory, fn($q) =>
                $q->where('target_category', $this->filterCategory)
            )
            ->when($this->filterGroup, fn($q) =>
                $q->where('group_id', $this->filterGroup)
            )
            ->when($this->filterDateFrom, fn($q) =>
                $q->where('period_start', '>=', $this->filterDateFrom . ' 00:00:00')
            )
            ->when($this->filterDateTo, fn($q) =>
                $q->where('period_start', '<=', $this->filterDateTo . ' 23:59:59')
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function delete(int $id): void
    {
        if (Gate::denies('educational-activity-schedules.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $schedule = ActivitySchedule::findOrFail($id);
        $schedule->delete();

        session()->flash('message', __('Schedule successfully deleted.'));
    }


    #[Computed()]
    public function studentGroups()
    {
        $user = auth()->user();
        $allActiveGroups = \App\Reposotries\StudentGroupRepo::activeToday();

        if ($user->isSuperAdmin()) {
            return $allActiveGroups;
        }

        $teacherGroupIds = $user->teacher()->pluck('student_group_id');
        return $allActiveGroups->whereIn('id', $teacherGroupIds)->values();
    }


    public function openCloneMonthModal(): void
    {
        if (Gate::denies('educational-activity-schedules.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $this->cloneSourceMonth     = now()->month;
        $this->cloneSourceYear      = now()->year;
        $this->cloneSourceGroupId   = '';
        $this->cloneTargetGroupIds  = [];
        $this->showCloneMonthModal  = true;
    }

    public function cloneMonthSchedules(): void
    {
        if (Gate::denies('educational-activity-schedules.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $this->validate([
            'cloneSourceMonth'      => 'required|integer|between:1,12',
            'cloneSourceYear'       => 'required|integer|min:2020|max:2050',
            'cloneSourceGroupId'    => 'required|exists:student_groups,id',
            'cloneTargetGroupIds'   => 'required|array|min:1',
            'cloneTargetGroupIds.*' => 'required|exists:student_groups,id',
        ]);

        $startDate = \Carbon\Carbon::create($this->cloneSourceYear, $this->cloneSourceMonth, 1, 0, 0, 0);
        $endDate   = $startDate->copy()->endOfMonth();

        $schedules = ActivitySchedule::where('group_id', $this->cloneSourceGroupId)
            ->whereBetween('period_start', [$startDate, $endDate])
            ->get();

        if ($schedules->isEmpty()) {
            session()->flash('error', __('No schedules found for the selected source group and month.'));
            return;
        }

        $count = 0;
        $skipped = 0;
        foreach ($schedules as $sourceSchedule) {
            foreach ($this->cloneTargetGroupIds as $targetGroupId) {
                // Skip cloning to the same group
                if ($targetGroupId == $this->cloneSourceGroupId) {
                    continue;
                }

                // Check if identical schedule already exists in the target group
                $exists = ActivitySchedule::where('group_id', $targetGroupId)
                    ->where('activity_name', $sourceSchedule->activity_name)
                    ->where('period_start', $sourceSchedule->period_start)
                    ->where('period_end', $sourceSchedule->period_end)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                ActivitySchedule::create([
                    'activity_id'                 => $sourceSchedule->activity_id,
                    'group_id'                    => $targetGroupId,
                    'educational_activity_domain' => $sourceSchedule->educational_activity_domain,
                    'target_category'             => $sourceSchedule->target_category,
                    'activity_name'               => $sourceSchedule->activity_name,
                    'activity_description'        => $sourceSchedule->activity_description,
                    'period_start'                => $sourceSchedule->period_start,
                    'period_end'                  => $sourceSchedule->period_end,
                    'educational_period_groups'   => $sourceSchedule->educational_period_groups,
                    'notes'                       => $sourceSchedule->notes,
                    'sort_order'                  => $sourceSchedule->sort_order,
                    'activation'                  => $sourceSchedule->activation,
                    'employee_id'                 => null, // per user request, don't copy employee_id
                    'created_by'                  => auth()->id(),
                    'updated_by'                  => auth()->id(),
                ]);
                $count++;
            }
        }

        $this->showCloneMonthModal = false;
        $this->cloneTargetGroupIds = [];

        if ($skipped > 0) {
            session()->flash('message', __('Successfully cloned :count schedules. (:skipped duplicates were skipped).', ['count' => $count, 'skipped' => $skipped]));
        } else {
            session()->flash('message', __('Successfully cloned :count schedules to the selected groups.', ['count' => $count]));
        }
    }

    public function export()
    {
        if (Gate::denies('educational-activity-schedules.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $query = ActivitySchedule::query()
            ->with(['activityDomain', 'group', 'employee', 'periodGroups'])
            ->when($this->search, fn($q) =>
                $q->where('activity_name', 'like', '%' . $this->search . '%')
                  ->orWhere('target_category', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterDomain, fn($q) =>
                $q->where('educational_activity_domain', $this->filterDomain)
            )
            ->when($this->filterCategory, fn($q) =>
                $q->where('target_category', $this->filterCategory)
            )
            ->when($this->filterGroup, fn($q) =>
                $q->where('group_id', $this->filterGroup)
            )
            ->when($this->filterDateFrom, fn($q) =>
                $q->where('period_start', '>=', $this->filterDateFrom . ' 00:00:00')
            )
            ->when($this->filterDateTo, fn($q) =>
                $q->where('period_start', '<=', $this->filterDateTo . ' 23:59:59')
            )
            ->orderBy($this->sortField, $this->sortDirection);

        return Excel::download(new EducationalActivitySchedulesExport($query), 'educational-activity-schedules.xlsx');
    }

    public function render()
    {
        if (Gate::denies('educational-activity-schedules.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        return view('livewire.org-app.educational-activity-schedules.index');
    }
}
