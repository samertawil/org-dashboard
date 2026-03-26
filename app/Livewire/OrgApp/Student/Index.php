<?php

namespace App\Livewire\OrgApp\Student;


use App\Imports\StudentsImport;
use App\Models\FeedBack;
use App\Models\Student;
use App\Reposotries\StatusRepo;
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

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    
    public $excelFile; // For file upload

    public $feedbackComment = '';
    public $feedbackRating = 5;
    public $feedbackType = null;
    public $studentFeedBackTime = null;
    public $selectedStudentId = null;

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
    public function students()
    {
        return Student::query()
            ->with(['studentGroup', 'status', 'city', 'region'])
            ->withCount('feedbacks')
            ->where('full_name', 'like', '%' . $this->search . '%')
            ->orWhere('identity_number', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function delete($id)
    {
        if(Gate::denies('student.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $student = Student::findOrFail($id);
        $student->delete();
        session()->flash('message', __('Student successfully deleted.'));
    }

    public function import()
    {
        if(Gate::denies('student.create')) {
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
        $this->studentFeedBackTime= null;
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
        $this->studentFeedBackTime= null;
        session()->flash('feedback_success', __('Feedback added successfully.'));
    }

    #[Computed()]
    public function feedbackTypes()
    {
        return StatusRepo::statuses()->whereIn('p_id_sub',[ 56,60]);
    }

    public function deleteFeedback($feedbackId)
    {
        if(Gate::denies('student.create')) {
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
        if(Gate::denies('student.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        if (!$this->selectedStudentId) return [];
        return FeedBack::where('student_id', $this->selectedStudentId)
            ->with('feedbackTypeStatus')
            ->latest()
            ->get();
    }

    public $surveyAnswerNo = '';
    public $surveyAnswerQuestionId = '';
    public $surveyAnswerArText = '';
    public $surveyAnswerEnText = '';
    public $surveySelectedStudentId = null;

    public function takeSurveyAnswer($studentId)
    {
        $this->surveySelectedStudentId = $studentId;
        $this->surveyAnswerNo = '';
        $this->surveyAnswerQuestionId = '';
        $this->surveyAnswerArText = '';
        $this->surveyAnswerEnText = '';
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'survey-answer-modal');
    }

    public function saveSurveyAnswer()
    {
        $this->validate([
            'surveyAnswerNo' => 'required|integer',
            'surveyAnswerQuestionId' => 'nullable|exists:survey_questions,id',
            'surveyAnswerArText' => 'nullable|string',
            'surveyAnswerEnText' => 'nullable|string',
            'surveySelectedStudentId' => 'required|exists:students,id',
        ]);

        \App\Models\SurveyAnswer::create([
            'account_id' => $this->surveySelectedStudentId,
            'survey_no' => $this->surveyAnswerNo,
            'question_id' => $this->surveyAnswerQuestionId ?: null,
            'answer_ar_text' => $this->surveyAnswerArText,
            'answer_en_text' => $this->surveyAnswerEnText,
            'created_by' => auth()->user()?->employee?->id ?? null,
        ]);

        $this->dispatch('close-modal', 'survey-answer-modal');
        $this->surveyAnswerNo = '';
        $this->surveyAnswerQuestionId = '';
        $this->surveyAnswerArText = '';
        $this->surveyAnswerEnText = '';
        session()->flash('message', __('Survey answer saved successfully.'));
    }

    #[Computed()]
    public function surveyQuestions()
    {
        return \App\Models\SurveyQuestion::all();
    }

    public function render()
    {
        if(Gate::denies('student.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.student.index');
    }
}
