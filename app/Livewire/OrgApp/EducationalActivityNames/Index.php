<?php

namespace App\Livewire\OrgApp\EducationalActivityNames;

use App\Models\EducationalActivityName;
use App\Models\Status;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'activity_name';
    public $sortDirection = 'asc';
    public $perPage = 30;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        if (Gate::denies('educational-activity-names.delete') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $activity = EducationalActivityName::findOrFail($id);

        // Prevent deletion if linked to schedules
        $isUsed = \App\Models\ActivitySchedule::where('activity_name', $activity->id)->exists();
        if ($isUsed) {
            session()->flash('error', __('Cannot delete this activity name because it is linked to scheduled activities.'));
            return;
        }

        $activity->delete();
        session()->flash('message', __('Activity name successfully deleted.'));
    }

    public function toggleActivation($id)
    {
        if (Gate::denies('educational-activity-names.create') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $activity = EducationalActivityName::findOrFail($id);
        $activity->activation = $activity->activation === 1 ? 0 : 1;
        $activity->save();

        session()->flash('message', __('Activation status updated successfully.'));
    }

    #[Computed]
    public function activityNames()
    {
        return EducationalActivityName::query()
            ->with(['domain'])
            ->where('activity_name', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        if (Gate::denies('educational-activity-names.index') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have the necessary permissions.');
        }

        return view('livewire.org-app.educational-activity-names.index', [
            'activities' => $this->activityNames
        ]);
    }
}
