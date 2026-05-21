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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete($id): void
    {
        if (Gate::denies('educational-activity-detail.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $detail = EducationalActivityDetail::findOrFail($id);
        $detail->delete();

        session()->flash('message', __('Deleted successfully.'));
    }

    public function render()
    {
        
        if (Gate::denies('educational-activity-detail.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $details = EducationalActivityDetail::query()
            ->with('educationalActivity')
            ->when($this->search, function ($query) {
                $query->whereHas('educationalActivity', function ($q) {
                    $q->where('activity_name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('what_learned', 'like', '%' . $this->search . '%')
                ->orWhere('teacher_report_detail', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.org-app.educational-activity-detail.index', [
            'details' => $details,
        ]);
    }
}
