<?php

namespace App\Livewire\OrgApp\TeacherStudentGroup;

use App\Models\TeacherStudentGroup;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 10;
    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function mappings()
    {
        return TeacherStudentGroup::query()
            ->with(['teacher.user', 'studentGroup'])
            ->whereHas('teacher.user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('studentGroup', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);
    }

    public function delete($id)
    {
        
        if (Gate::denies('teacher-student-groups.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        // Add gate check if needed
        $mapping = TeacherStudentGroup::findOrFail($id);
        $mapping->delete();
        session()->flash('message', __('Assignment deleted successfully.'));
    }

    public function render()
    {
        if (Gate::denies('teacher-student-groups.index')) {
            abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.org-app.teacher-student-group.index');
    }
}
