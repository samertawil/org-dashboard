<?php

namespace App\Livewire\OrgApp\EducationalActivityDetail;

use App\Models\EducationalActivityDetail;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

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

    public function showDetails($id): void
    {
        $user = auth()->user();
        $this->selectedDetail = EducationalActivityDetail::with(['educationalActivity.periodGroups'])->findOrFail($id);

        if (!$user->isSuperAdmin()) {
            $groupIds = $user->teacher()->pluck('student_group_id')->toArray();
            $groupId = $this->selectedDetail->educationalActivity?->group_id;
            $employeeId = $user->employee?->id;
            if (!in_array($groupId, $groupIds) || $this->selectedDetail->educationalActivity?->employee_id !== $employeeId) {
                abort(403, 'You do not have permission to view this record.');
            }
        }

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
        if (Gate::denies('educational-activity-detail.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $user = auth()->user();
        $detail = EducationalActivityDetail::findOrFail($id);

        if (!$user->isSuperAdmin()) {
            $groupIds = $user->teacher()->pluck('student_group_id')->toArray();
            $groupId = $detail->educationalActivity?->group_id;
            $employeeId = $user->employee?->id;
            if (!in_array($groupId, $groupIds) || $detail->educationalActivity?->employee_id !== $employeeId) {
                abort(403, 'You do not have permission to delete this record.');
            }
        }

        $detail->delete();

        session()->flash('message', __('Deleted successfully.'));
    }

    public function render()
    {

        if (Gate::denies('educational-activity-detail.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $user = auth()->user();
        $employee = $user->employee;

        $details = \App\Reposotries\EducationalActivityDetailRepo::getTeacherDetailsQuery()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('educationalActivity', function ($sub) {
                        $sub->where('activity_name', 'like', '%' . $this->search . '%')
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

        return view('livewire.org-app.educational-activity-detail.index', [
            'details' => $details,
        ]);
    }
}
