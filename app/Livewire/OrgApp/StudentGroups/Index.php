<?php

namespace App\Livewire\OrgApp\StudentGroups;

use App\Models\Student;
use Livewire\Component;
use App\Models\StudentGroup;
use App\Models\StudentSubjectForLearn;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use WithPagination;
    
    // Search properties
    public string $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Modal State
    public $viewingSubjects = [];
    public $viewingGroupName = '';
    public $showSubjectsModal = false;

    // Pagination
    public int $perPage = 10;

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

    #[Computed()]
    public function groups()
    {
     return  StudentGroup::query()
            ->with(['region', 'city', 'status'])  ->withCount(['students' => function ($query) {
    $query->where('activation', 1);
}])
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('Moderator', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);      
    }

    public function delete($id)
    {
        $group = StudentGroup::findOrFail($id);
        $group->delete();
        session()->flash('message', __('Student Group successfully deleted.'));
    }

    public function viewSubjects($groupId)
    {
        $group = StudentGroup::find($groupId);
        if ($group) {
            $ids = $group->subject_to_learn_id ?? [];
            if (is_array($ids) && count($ids) > 0) {
                 $this->viewingSubjects = StudentSubjectForLearn::whereIn('id', $ids)->pluck('name')->toArray();
            } else {
                 $this->viewingSubjects = [];
            }
            $this->viewingGroupName = $group->name;
            $this->showSubjectsModal = true;
        }
    }
    
    public function closeSubjectsModal()
    {
        $this->showSubjectsModal = false;
        $this->viewingSubjects = [];
        $this->viewingGroupName = '';
    }

    public function render()
    {

        //          $data = Student::groupBy('student_groups_id')->select('student_groups_id', DB::raw('count(*) as total'))->get();

        // dd($data);
        return view('livewire.org-app.student-groups.index', [
            'subjectsMap' => StudentSubjectForLearn::all()->keyBy('id')
        ]);
    }
}
