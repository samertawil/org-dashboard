<?php

namespace App\Livewire\OrgApp\EducationalActivityDetail;

use App\Models\EducationalActivityDetail;
use Illuminate\Support\Facades\Gate;
use App\Reposotries\EducationalActivityDetailRepo;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Computed;

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
    }

    public function render()
    {

        Gate::authorize('viewAny', EducationalActivityDetail::class);

        return view('livewire.org-app.educational-activity-detail.index', [
            'details' => $this->details,
        ]);
    }
}
