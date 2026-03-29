<?php

namespace App\Livewire\OrgApp\Student;


use App\Enums\GlobalSystemConstant;
use App\Imports\StudentsImport;
use App\Models\FeedBack;
use App\Models\Student;
use App\Reposotries\StatusRepo;
use App\Reposotries\StudentGroupRepo;
use App\Reposotries\StudentRepo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

 
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;

    public $excelFile; // For file upload

    public $feedbackComment = '';
    public $feedbackRating = 5;
    public $feedbackType = null;
    public $studentFeedBackTime = null;
    public $selectedStudentId = null;
    public $searchIdentityNumber = '';
    public $searchStudentName = '';
    public $searchStudentGroupName = '';
    public $searchEnrollment = '';
    public $searchActivation = '';
    public $readyToLoad = false;

    protected $queryString = [
 
        'searchIdentityNumber' => ['except' => ''],
        'searchStudentName' => ['except' => ''],
        'searchStudentGroupName' => ['except' => ''],
        'searchEnrollment' => ['except' => ''],
        'searchActivation' => ['except' => ''],
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

   
    public function updating($property)
    {
        if (in_array($property, ['searchIdentityNumber', 'searchStudentName', 'searchStudentGroupName', 'searchEnrollment', 'searchActivation'])) {
            $this->resetPage();
            $this->readyToLoad = false;
        }
    }


    public function clearFilters()
    {
        $this->reset(['searchIdentityNumber', 'searchStudentName', 'searchStudentGroupName', 'searchEnrollment', 'searchActivation']);
        $this->readyToLoad = false;
        $this->resetPage();
    }

    public function searchData()
    {
        $this->readyToLoad = true;
        $this->resetPage();
    }



    #[Computed()]
    public function studentsNames() {
        return StudentRepo::students();
    }

    #[Computed()]
    public function educationPoints() {
        return StudentGroupRepo::studentGroups();
    }

    #[Computed()]
    public function students()
    {
        if($this->readyToLoad) {
            
        return Student::query()
            ->with(['studentGroup', 'status', 'city', 'region'])
            ->withCount('feedbacks')

            ->when($this->searchIdentityNumber !== '', fn($q) => $q->where('identity_number', $this->searchIdentityNumber))
            ->when($this->searchStudentName !== '', fn($q) => $q->where('id',$this->searchStudentName))
            ->when($this->searchStudentGroupName !== '', fn($q) => $q->where('student_groups_id',$this->searchStudentGroupName))
            ->when($this->searchEnrollment !== '', fn($q) => $q->where('enrollment_type',$this->searchEnrollment))
            ->when($this->searchActivation !== '', fn($q) => $q->where('activation',$this->searchActivation))
          
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
        } else {
            return new LengthAwarePaginator(
                collect([]), // empty collection
                0, // total
                $this->perPage, // per page
                1 // current page
            );
        }
    }

    public function delete($id)
    {
        if (Gate::denies('student.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $student = Student::findOrFail($id);
        $student->delete();
        session()->flash('message', __('Student successfully deleted.'));
    }

    public function import()
    {
        if (Gate::denies('student.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new StudentsImport, $this->excelFile);

            // Archive the file
            $date = now()->format('Y-m-d');
            $userName = \Illuminate\Support\Str::slug(auth()->user()->name);
            $filename = "Student_{$date}_{$userName}.xlsx";

            // Ensure directory exists
            if (!\Illuminate\Support\Facades\Storage::exists('student_imported_sheet_files')) {
                \Illuminate\Support\Facades\Storage::makeDirectory('student_imported_sheet_files');
            }

            $this->excelFile->storeAs('student_imported_sheet_files', $filename);

            $this->reset('excelFile');
            session()->flash('message', __('Students imported successfully.'));
            $this->dispatch('close-modal', 'import-modal');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            $messages = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $value = $failure->values()[$attribute] ?? 'N/A';
                $errors = implode(', ', $failure->errors());
                $messages[] = "Row {$row}: ({$value}) - {$errors}";
            }

            session()->flash('error', implode('<br>', $messages));
        }
    }


    public function manageFeedback($studentId)
    {
        $this->selectedStudentId = $studentId;
        $this->feedbackComment = '';
        $this->feedbackRating = 5; // Default rating
        $this->feedbackType = null;
        $this->studentFeedBackTime = null;
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'feedback-modal');
    }

    public function saveFeedback()
    {
        $this->validate([
            'feedbackComment' => 'nullable|string',
            'feedbackRating' => 'required|integer|min:1|max:5',
            'feedbackType' => 'required|exists:statuses,id',
            'selectedStudentId' => 'required|exists:students,id',
        ]);

        FeedBack::create([
            'student_id' => $this->selectedStudentId,
            'comment' => $this->feedbackComment,
            'rating' => $this->feedbackRating,
            'feed_back_type' => $this->feedbackType,
            'student_feed_back_time' => $this->studentFeedBackTime,
        ]);

        $this->feedbackComment = '';
        $this->feedbackRating = 5;
        $this->feedbackType = null;
        $this->studentFeedBackTime = null;
        session()->flash('feedback_success', __('Feedback added successfully.'));
    }

    #[Computed()]
    public function feedbackTypes()
    {
        return StatusRepo::statuses()->whereIn('p_id_sub', [56, 60]);
    }

    public function deleteFeedback($feedbackId)
    {
        if (Gate::denies('student.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $feedback = FeedBack::findOrFail($feedbackId);
        $feedback->delete();
        session()->flash('feedback_success', __('Feedback deleted successfully.'));
    }

    #[Computed()]
    public function selectedStudent()
    {
        return $this->selectedStudentId ? Student::find($this->selectedStudentId) : null;
    }

    #[Computed()]
    public function studentFeedbacks()
    {
        if (Gate::denies('student.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        if (!$this->selectedStudentId) return [];
        return FeedBack::where('student_id', $this->selectedStudentId)
            ->with('feedbackTypeStatus')
            ->latest()
            ->get();
    }

    public function render()
    {
       
        if (Gate::denies('student.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $activations = GlobalSystemConstant::options()->where('type', 'status'); 

        return view('livewire.org-app.student.index', [
            'activations' => $activations,
        ]);
    }
}
