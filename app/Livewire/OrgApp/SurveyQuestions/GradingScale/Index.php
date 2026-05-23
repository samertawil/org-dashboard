<?php

namespace App\Livewire\OrgApp\SurveyQuestions\GradingScale;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\SurveyGradingScaleTable;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;

class Index extends Component
{
    use WithPagination;
    
    // Search properties
    public string $search = '';
    public string $searchBatch = '';
    public string $searchSection = '';
    public string $viewType = 'table';
    public $sortField = 'id';
    public $sortDirection = 'desc';

    // Pagination
    public int $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'searchBatch' => ['except' => ''],
        'searchSection' => ['except' => ''],
        'viewType' => ['except' => 'table'],
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

    public function updatingSearchBatch()
    {
        $this->resetPage();
    }

    public function updatingSearchSection()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function gradingScales()
    {
        return SurveyGradingScaleTable::query()->with('typeRel', 'surveyForSection' )
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
        if (Gate::denies('survey.manage')) 
        { 
            abort(403, __('You do not have the necessary permissions.'));
        }
        $scale = SurveyGradingScaleTable::findOrFail($id);
        $scale->delete();
        session()->flash('message', __('Grading Scale successfully deleted.'));
    }

    /**
     * Build the grading scale tree structure grouped by Section -> Batch.
     */
    public function getGradingScalesTree()
    {
        // 1. Fetch matching grading scale records with relations
        $query = SurveyGradingScaleTable::query()
            ->with(['typeRel', 'surveyForSection'])
            ->when($this->searchBatch, function ($q) {
                $q->where('batch_no', $this->searchBatch);
            })
            ->when($this->searchSection, function ($q) {
                $q->where('survey_for_section', $this->searchSection);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('evaluation', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            });

        $scales = $query->orderBy('type', 'asc')->orderBy('from_percentage', 'asc')->get();

        // 2. Group by Section ID
        $groupedBySection = $scales->groupBy('survey_for_section');

        $tree = [];

        foreach ($groupedBySection as $sectionId => $sectionScales) {
            $sectionModel = $sectionScales->first()->surveyForSection;
            $sectionName = $sectionModel->status_name ?? __('No Section');

            // 3. Group by Batch under this Section
            $groupedByBatch = $sectionScales->groupBy('batch_no');

            $batches = [];
            foreach ($groupedByBatch as $batchNo => $batchScales) {
                $batches[] = [
                    'batch_no' => $batchNo,
                    'scales' => $batchScales,
                ];
            }

            $tree[] = [
                'section_id' => $sectionId,
                'section_name' => $sectionName,
                'batches' => $batches,
            ];
        }

        return $tree;
    }

    #[Title('Survey Grading Scales')]
    public function render()
    {
        if (Gate::denies('survey.grading.scale.index')) 
        { 
            abort(403, __('You do not have the necessary permissions.'));
        }

        $batches = \App\Models\StudentGroup::select('batch_no')->distinct()->whereNotNull('batch_no')->orderBy('batch_no')->get();
        $surveySections = \App\Reposotries\StatusRepo::statuses()->where('p_id_sub', config('appConstant.survey_for', 119));

        $gradingScalesTree = $this->viewType === 'tree' ? $this->getGradingScalesTree() : [];

        return view('livewire.org-app.survey-questions.grading-scale.index', [
            'heading' => __('Survey Grading Scales'),
            'subheading' => __('Manage the evaluation scales for surveys.'),
            'batches' => $batches,
            'surveySections' => $surveySections,
            'gradingScales' => $this->gradingScales,
            'gradingScalesTree' => $gradingScalesTree,
        ]);
    }
}
