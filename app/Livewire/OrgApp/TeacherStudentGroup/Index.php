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

    public $student_group_id;

    protected $queryString = [
        'search' => ['except' => ''],
        'student_group_id' => ['except' => null],
    ];

    public function mount($student_group_id = null)
    {
        if ($student_group_id) {
            $this->student_group_id = $student_group_id;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function mappings()
    {
        return TeacherStudentGroup::query()
            ->with(['teacher.user', 'studentGroup', 'jobTitle'])
            ->when($this->student_group_id, function ($q) {
                $q->where('student_group_id', $this->student_group_id);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('teacher.user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('studentGroup', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
                });
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
