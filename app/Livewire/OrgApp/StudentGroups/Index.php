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
    public $selectedGroup = null;
    public $showDetailsModal = false;

    // Pagination
    public int $perPage = 5;

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
            ->with(['region', 'city', 'status','partner'])  ->withCount(['students' => function ($query) {
    $query->where('activation', 1);
}])
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('Moderator', 'like', '%' . $this->search . '%')
                      ->orWhere('batch_no', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);      
    }

    public function delete($id)
    {
        if (Gate::denies('student.group.create')) 
        { 
            abort(403, 'You do not have the necessary permissions');
        }
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

    public function viewGroupDetails($groupId)
    {
        $this->selectedGroup = StudentGroup::with([
            'region',
            'city',
            'neighbourhood',
            'location',
            'status',
            'partner'
        ])->withCount(['students' => function ($query) {
            $query->where('activation', 1);
        }])->findOrFail($groupId);

        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->selectedGroup = null;
        $this->showDetailsModal = false;
    }
    
    public function generateSchedule($groupId)
    {
        if (Gate::denies('student.group.create')) {
            abort(403, 'You do not have the necessary permissions');
        }

        $group = StudentGroup::findOrFail($groupId);

        // Check if schedule already exists
        if (\App\Models\StudentGroupSchedule::where('student_group_id', $groupId)->exists()) {
            session()->flash('message', __('Schedule already exists for this group.'));
            return;
        }

        if ($group->start_date && $group->end_date && $group->start_time && $group->end_time) {
            DB::beginTransaction();
            try {
                $startDate = \Carbon\Carbon::parse($group->start_date);
                $endDate = \Carbon\Carbon::parse($group->end_date);

                while ($startDate->lte($endDate)) {
                    $hours = 0;
                    $s = \Carbon\Carbon::parse($group->start_time);
                    $e = \Carbon\Carbon::parse($group->end_time);
                    $hours = $s->diffInHours($e);

                    $dayName = $startDate->format('l');
                    $isOffDay = in_array($dayName, ['Friday']);

                    \App\Models\StudentGroupSchedule::create([
                        'student_group_id' => $group->id,
                        'schedule_date' => $startDate->format('Y-m-d'),
                        'day' => $dayName,
                        'start_time' => $group->start_time->format('H:i'),
                        'end_time' => $group->end_time->format('H:i'),
                        'hours' => $hours,
                        'name' => $group->name,
                        'activation' => 1,
                        'is_off_day' => $isOffDay,
                    ]);
                    
                    $startDate->addDay();
                }
                DB::commit();
                session()->flash('message', __('Schedule successfully generated for :name', ['name' => $group->name]));
            } catch (\Exception $e) {
                DB::rollBack();
                session()->flash('message', __('Error generating schedule: ') . $e->getMessage());
            }
        } else {
            session()->flash('message', __('Please ensure start date, end date, and times are set for this group.'));
        }
    }

    public function render()
    {
        if (Gate::denies('student.group.index')) 
        { 
            abort(403, 'You do not have the necessary permissions');
        }
        //          $data = Student::groupBy('student_groups_id')->select('student_groups_id', DB::raw('count(*) as total'))->get();

        // dd($data);
        return view('livewire.org-app.student-groups.index', [
            'subjectsMap' => StudentSubjectForLearn::all()->keyBy('id')
        ]);
    }
}
