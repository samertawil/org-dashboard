<?php

namespace App\Livewire\OrgApp\EducationalActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Reposotries\StudentGroupRepo;
use App\Exports\EducationalActivitySchedulesExport;
use Maatwebsite\Excel\Facades\Excel;


class Index extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $filterDomain   = '';
    public string $filterCategory = '';
    public string $filterGroup    = '';
    public string $filterBatch    = '';
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

    // Report Modal
    public $reportModalAction = null;
    public $selectedScheduleId = null;
    public $selectedDetailId = null;

    protected $queryString = [
        'search'         => ['except' => ''],
        'filterDomain'   => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterBatch'    => ['except' => ''],
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

    public function updatingFilterBatch(): void
    {
        $this->filterGroup = '';
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

    /**
     * Get the student groups accessible by the current user.
     */
    #[Computed()]
    public function accessibleGroups()
    {
        return StudentGroupRepo::educationPoints();
    }

    /**
     * Get the student group IDs accessible by the current teacher.
     * Returns null for super admins (no restriction).
     */
    #[Computed()]
    public function accessibleGroupIds()
    {

        $user = auth()->user();
        if ($user->isSuperAdmin() || Gate::allows('select.any.student')) {
            return null;
        }
        return $this->accessibleGroups->pluck('id')->toArray();
    }

    /**
     * Available batches filtered by teacher's accessible groups.
     */
    #[Computed()]
    public function availableBatches()
    {
        return $this->accessibleGroups->pluck('batch_no')->filter()->unique()->values()->toArray();
    }

    /**
     * Available groups filtered by batch and teacher's accessible groups.
     */
    #[Computed()]
    public function availableGroups()
    {
        $groups = $this->accessibleGroups;
        if (!empty($this->filterBatch)) {
            $groups = $groups->where('batch_no', $this->filterBatch);
        }
        return $groups->values();
    }

    #[Computed()]
    public function schedules()
    {
        if (empty($this->filterBatch)) {
            $emptyQuery = ActivitySchedule::query()->where('id', '<', 0);
            $limit = $this->viewType === 'tree' ? 1000 : $this->perPage;
            return $emptyQuery->paginate($limit);
        }

        $teacherGroupIds = $this->accessibleGroupIds;

        $query = ActivitySchedule::query()
            ->with(['activityDomain', 'group', 'employee', 'periodGroups', 'activityDetail'])
            ->whereHas('group', function ($q) {
                $q->where('batch_no', $this->filterBatch);
            })
            ->when(
                $teacherGroupIds !== null,
                fn($q) =>
                $q->whereIn('group_id', $teacherGroupIds)
                    ->where(function ($query) {
                        $query->where('employee_id', auth()->user()->employee?->id)
                            ->orWhereNull('employee_id');
                    })
            )
            ->when(
                $this->search,
                fn($q) =>
                $q->where('activity_name', 'like', '%' . $this->search . '%')
                    ->orWhere('target_category', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%')
            )
            ->when(
                $this->filterDomain,
                fn($q) =>
                $q->where('educational_activity_domain', $this->filterDomain)
            )
            ->when(
                $this->filterCategory,
                fn($q) =>
                $q->where('target_category', $this->filterCategory)
            )
            ->when(
                $this->filterGroup,
                fn($q) =>
                $q->where('group_id', $this->filterGroup)
            )
            ->when(
                $this->filterDateFrom,
                fn($q) =>
                $q->where('period_start', '>=', $this->filterDateFrom . ' 00:00:00')
            )
            ->when(
                $this->filterDateTo,
                fn($q) =>
                $q->where('period_start', '<=', $this->filterDateTo . ' 23:59:59')
            )
            ->orderBy($this->sortField, $this->sortDirection);

        $limit = $this->viewType === 'tree' ? 5000 : $this->perPage;
        return $query->paginate($limit);
    }

    public function delete(int $id): void
    {
        $schedule = ActivitySchedule::findOrFail($id);

        Gate::authorize('delete', $schedule);
        $schedule->delete();

        session()->flash('message', __('Schedule successfully deleted.'));
    }

    public function openReportModal($action, $scheduleId)
    {
        $this->selectedScheduleId = $scheduleId;
        $this->reportModalAction = $action;

        $detail = \App\Models\EducationalActivityDetail::where('educational_activity_id', $scheduleId)->first();

        if ($detail) {
            $this->selectedDetailId = $detail->id;
        } else {
            $this->selectedDetailId = null;
        }

        if (in_array($action, ['edit', 'show', 'gallery']) && !$this->selectedDetailId) {
            session()->flash('error', __('No report exists for this schedule yet.'));
            $this->reportModalAction = null;
            return;
        }

        if ($action === 'create' && $this->selectedDetailId) {
            $this->reportModalAction = 'edit';
        }

        $this->dispatch('modal-show', name: 'report-modal');
    }


    public function openCloneMonthModal(): void
    {
        Gate::authorize('duplicate', ActivitySchedule::class);

        $this->cloneSourceMonth     = now()->month;
        $this->cloneSourceYear      = now()->year;
        $this->cloneSourceGroupId   = '';
        $this->cloneTargetGroupIds  = [];
        $this->showCloneMonthModal  = true;
    }

    public function cloneMonthSchedules(): void
    {

        Gate::authorize('duplicate', ActivitySchedule::class);

        $this->validate([
            'cloneSourceMonth'      => 'required|integer|between:1,12',
            'cloneSourceYear'       => 'required|integer|min:2020|max:2050',
            'cloneSourceGroupId'    => 'required|exists:student_groups,id',
            'cloneTargetGroupIds'   => 'required|array|min:1',
            'cloneTargetGroupIds.*' => 'required|exists:student_groups,id',
        ]);

        $startDate = \Carbon\Carbon::create($this->cloneSourceYear, $this->cloneSourceMonth, 1, 0, 0, 0);
        $endDate   = $startDate->copy()->endOfMonth();

        $teacherGroupIds = $this->accessibleGroupIds;

        $schedules = ActivitySchedule::where('group_id', $this->cloneSourceGroupId)
            ->whereBetween('period_start', [$startDate, $endDate])
            ->when(
                $teacherGroupIds !== null,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('employee_id', auth()->user()->employee?->id)
                        ->orWhereNull('employee_id');
                })
            )
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
        Gate::authorize('export', ActivitySchedule::class);
        if (empty($this->filterBatch)) {
            session()->flash('error', __('Please select a Batch before exporting.'));
            return;
        }

        $teacherGroupIds = $this->accessibleGroupIds;

        $query = ActivitySchedule::query()
            ->with(['activityDomain', 'group', 'employee', 'periodGroups', 'activityDetail'])
            ->whereHas('group', function ($q) {
                $q->where('batch_no', $this->filterBatch);
            })
            ->when(
                $teacherGroupIds !== null,
                fn($q) =>
                $q->whereIn('group_id', $teacherGroupIds)
                    ->where(function ($query) {
                        $query->where('employee_id', auth()->user()->employee?->id)
                            ->orWhereNull('employee_id');
                    })
            )
            ->when(
                $this->search,
                fn($q) =>
                $q->where('activity_name', 'like', '%' . $this->search . '%')
                    ->orWhere('target_category', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%')
            )
            ->when(
                $this->filterDomain,
                fn($q) =>
                $q->where('educational_activity_domain', $this->filterDomain)
            )
            ->when(
                $this->filterCategory,
                fn($q) =>
                $q->where('target_category', $this->filterCategory)
            )
            ->when(
                $this->filterGroup,
                fn($q) =>
                $q->where('group_id', $this->filterGroup)
            )
            ->when(
                $this->filterDateFrom,
                fn($q) =>
                $q->where('period_start', '>=', $this->filterDateFrom . ' 00:00:00')
            )
            ->when(
                $this->filterDateTo,
                fn($q) =>
                $q->where('period_start', '<=', $this->filterDateTo . ' 23:59:59')
            )
            ->orderBy($this->sortField, $this->sortDirection);

        return Excel::download(new EducationalActivitySchedulesExport($query), 'educational-activity-schedules.xlsx');
    }

    public function render()
    {
        Gate::authorize('viewAny', ActivitySchedule::class);

        return view('livewire.org-app.educational-activity-schedules.index');
    }
}
