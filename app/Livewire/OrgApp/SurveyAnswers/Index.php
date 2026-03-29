<?php

namespace App\Livewire\OrgApp\SurveyAnswers;

use App\Models\Employee;
use App\Models\Student;
use App\Models\SurveyAnswer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $searchAccountId = '';
    public $searchSurveyNo = '';
    public $searchCreatedBy = '';
    public $searchCreatedAt = '';
    public $searchAccountName = '';

    public $sortField = 'created_at';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $readyToLoad = false;
 


    protected $queryString = [
        'searchAccountId' => ['except' => ''],
        'searchAccountName' => ['except' => ''],
        'searchSurveyNo' => ['except' => ''],
        'searchCreatedBy' => ['except' => ''],
        'searchCreatedAt' => ['except' => ''],
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
        if (in_array($property, ['searchAccountId', 'searchSurveyNo', 'searchCreatedBy', 'searchCreatedAt', 'searchAccountName'])) {
            $this->resetPage();
            $this->readyToLoad = false;
        }
    }

    public function clearFilters()
    {
        $this->reset(['searchAccountId', 'searchSurveyNo', 'searchCreatedBy', 'searchCreatedAt','searchAccountName']);
        $this->readyToLoad = false;
        $this->resetPage();
    }

    public function searchData()
    {
        $this->readyToLoad = true;
        $this->resetPage();
    }

    #[Computed()]
    public function answers()
    {
        if ($this->readyToLoad && $this->searchAccountId !== '' || $this->searchAccountName!== '') {

            return SurveyAnswer::query()
                ->select('survey_answers.*')
                ->join('survey_questions', 'survey_answers.question_id', '=', 'survey_questions.id')
                ->with(['question', 'creator', 'surveyfor', 'student'])
                ->when($this->searchAccountId !== '', fn($q) => $q->where('survey_answers.account_id', $this->searchAccountId))
                ->when($this->searchAccountName !== '', fn($q) => $q->where('survey_answers.account_id', $this->searchAccountName))
                ->when($this->searchSurveyNo !== '', fn($q) => $q->where('survey_answers.survey_no', $this->searchSurveyNo))
                ->when($this->searchCreatedBy !== '', fn($q) => $q->where('survey_answers.created_by', $this->searchCreatedBy))
                ->when($this->searchCreatedAt !== '', fn($q) => $q->whereDate('survey_answers.created_at', $this->searchCreatedAt))

                ->orderBy('survey_questions.survey_for_section')
                ->orderBy('survey_questions.question_order', $this->sortDirection)
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
        if (Gate::denies('survey.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        $answer = SurveyAnswer::findOrFail($id);
        $answer->delete();
        session()->flash('message', __('Survey Answer successfully deleted.'));
    }

    public function render()
    {
 
        if (Gate::denies('survey.index')) {
            abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.org-app.survey-answers.index', [
            'employees' => Employee::all(),
             'students' => Student::get(),
        ]);
    }
}
 