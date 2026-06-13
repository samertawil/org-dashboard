<?php

namespace App\Livewire\OrgApp\EducationalActivityDetail;

use App\Models\EducationalActivityDetail;
use Illuminate\Support\Facades\Gate;
use App\Reposotries\EducationalActivityDetailRepo;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;

#[Lazy]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterDate = '';
    public ?EducationalActivityDetail $selectedDetail = null;

    protected $queryString = [
        'search'     => ['except' => ''],
        'filterDate' => ['except' => ''],
    ];

    // Cached permissions and IDs to avoid N+1 queries in view authorization checks
    public bool $isSuperAdmin = false;
    public bool $canViewAllStudents = false;
    public ?int $employeeId = null;
    public array $userGroupIds = [];
    public bool $canCreateDetail = false;
    public bool $canUpdateDetail = false;
    public bool $canDeleteDetail = false;
    public array $lockedDetailIds = [];

    public function mount(): void
    {
        $user = auth()->user();
        $this->isSuperAdmin = $user->isSuperAdmin();
        $this->canViewAllStudents = $user->can('select.any.student');
        $this->employeeId = $user->employee?->id;

        $groupIds167 = \App\Services\SupervisorService::getSupervisedGroupIds($user);
        $groupIds166 = $user->teacher()->where('job_title', 166)->pluck('student_group_id')->unique()->toArray();
        $this->userGroupIds = array_values(array_unique(array_merge($groupIds167, $groupIds166)));

        $this->canCreateDetail = $user->can('educational-activity-detail.create') || $this->isSuperAdmin || $this->canViewAllStudents;
        $this->canUpdateDetail = $this->canCreateDetail;
        $this->canDeleteDetail = $user->can('educational-activity-detail.create') || $this->isSuperAdmin;
    }

    public function showDetails($id): void
    {
        $this->selectedDetail = EducationalActivityDetail::with(['educationalActivity.periodGroups'])->findOrFail($id);

        Gate::authorize('view', $this->selectedDetail);

        $this->dispatch('modal-show', name: 'show-detail-modal');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDate(): void
    {
        $this->resetPage();
    }

    public function delete($id): void
    {
        $detail = EducationalActivityDetail::findOrFail($id);
        Gate::authorize('view', $detail);
        $detail->delete();

        session()->flash('message', __('Deleted successfully.'));
    }

    #[Computed]
    public function details()
    {

        return EducationalActivityDetailRepo::getTeacherDetailsQuery()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('educationalActivity', function ($sub) {
                        $sub->whereHas('activityNameStatus', function ($statusQuery) {
                            $statusQuery->where('activity_name', 'like', '%' . $this->search . '%');
                        })
                            ->orWhere('period_start', 'like', '%' . $this->search . '%');
                    })
                        ->orWhere('what_learned', 'like', '%' . $this->search . '%')
                        ->orWhere('teacher_report_detail', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterDate, function ($query) {
                $query->whereHas('educationalActivity', function ($q) {
                    $q->whereDate('period_start', $this->filterDate);
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        Gate::authorize('viewAny', EducationalActivityDetail::class);

        $details = $this->details;
        $detailIds = $details->pluck('id')->toArray();

        $this->lockedDetailIds = [];
        if (!empty($detailIds)) {
            $this->lockedDetailIds = \DB::table('reports')
                ->where(function ($query) use ($detailIds) {
                    foreach ($detailIds as $id) {
                        $query->orWhereJsonContains('covered_educational_activity_details_ids', (int) $id);
                    }
                })
                ->pluck('covered_educational_activity_details_ids')
                ->flatMap(function ($json) {
                    return json_decode($json ?? '[]', true);
                })
                ->unique()
                ->toArray();
        }

        return view('livewire.org-app.educational-activity-detail.index', [
            'details' => $details,
        ]);
    }
}
