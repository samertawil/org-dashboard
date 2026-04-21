<?php

namespace App\Livewire\OrgApp\SurveyQuestions\ComparisonScale;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\SurveyComparisonScale;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;

class Index extends Component
{
    use WithPagination;
    
    public string $search = '';
    public string $searchBatch = '';
    public string $searchSection = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'searchBatch' => ['except' => ''],
        'searchSection' => ['except' => ''],
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

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSearchBatch() { $this->resetPage(); }
    public function updatingSearchSection() { $this->resetPage(); }

    #[Computed()]
    public function comparisonScales()
    {
        return SurveyComparisonScale::query()
            ->with(['domain', 'surveyForSection'])
            ->when($this->searchBatch, function ($query) {
                $query->where('batch_no', $this->searchBatch);
            })
            ->when($this->searchSection, function ($query) {
                $query->where('survey_for_section', $this->searchSection);
            })
            ->where(function ($query) {
                $query->where('evaluation', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);      
    }

    public function delete($id)
    {
        if (Gate::denies('survey.manage')) { 
            abort(403, __('You do not have the necessary permissions.'));
        }
        $scale = SurveyComparisonScale::findOrFail($id);
        $scale->delete();
        session()->flash('success', __('Comparison Scale successfully deleted.'));
    }

    #[Title('Survey Comparison Scales')]
    public function render()
    {
        if (Gate::denies('survey.grading.scale.index')) { 
            abort(403, __('You do not have the necessary permissions.'));
        }

        $batches = \App\Models\StudentGroup::select('batch_no')->distinct()->whereNotNull('batch_no')->orderBy('batch_no')->get();
        $surveySections = \App\Reposotries\StatusRepo::statuses()->where('p_id_sub', config('appConstant.survey_for', 119));

        return view('livewire.org-app.survey-questions.comparison-scale.index', [
            'heading' => __('Survey Comparison Scales'),
            'subheading' => __('Manage the smart evaluation rules for survey comparison.'),
            'batches' => $batches,
            'surveySections' => $surveySections,
        ]);
    }
}
