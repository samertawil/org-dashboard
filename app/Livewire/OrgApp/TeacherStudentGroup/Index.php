<?php

namespace App\Livewire\OrgApp\TeacherStudentGroup;

use App\Models\TeacherStudentGroup;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;
use App\Concerns\AccessibleGroupsTrait;

class Index extends Component
{
    use WithPagination;
    use AccessibleGroupsTrait;

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
        $groupIds = $this->accessibleGroupIds;

        return TeacherStudentGroup::query()
            ->with(['teacher.user', 'studentGroup', 'jobTitle'])
            ->when($groupIds !== null, function ($q) use ($groupIds) {
                $q->whereIn('student_group_id', $groupIds);
            })
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

        $mapping = TeacherStudentGroup::findOrFail($id);
        $groupIds = $this->accessibleGroupIds;

        if ($groupIds !== null && !in_array($mapping->student_group_id, $groupIds)) {
            abort(403, 'You do not have the necessary permissions');
        }

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
