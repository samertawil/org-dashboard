<?php

namespace App\Livewire\OrgApp\SurveyAnswers;

use App\Models\SurveyAnswer;
use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use WithPagination;

    public $searchAccountId = '';
    public $searchSurveyNo = '';
    public $searchCreatedBy = '';
    public $searchCreatedAt = '';
    
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'searchAccountId' => ['except' => ''],
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
        if (in_array($property, ['searchAccountId', 'searchSurveyNo', 'searchCreatedBy', 'searchCreatedAt'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->reset(['searchAccountId', 'searchSurveyNo', 'searchCreatedBy', 'searchCreatedAt']);
        $this->resetPage();
    }

    #[Computed()]
    public function answers()
    {
        return SurveyAnswer::query()
            ->with(['question', 'creator'])
            ->when($this->searchAccountId !== '', fn($q) => $q->where('account_id', $this->searchAccountId))
            ->when($this->searchSurveyNo !== '', fn($q) => $q->where('survey_no', $this->searchSurveyNo))
            ->when($this->searchCreatedBy !== '', fn($q) => $q->where('created_by', $this->searchCreatedBy))
            ->when($this->searchCreatedAt !== '', fn($q) => $q->whereDate('created_at', $this->searchCreatedAt))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
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
            'employees' => Employee::all()
        ]);
    }
}
