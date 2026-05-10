<?php

namespace App\Livewire\OrgApp\SurveyQuestions\GradingScale;

use App\Models\SurveyGradingScaleDescription;
use App\Models\SurveyGradingScaleTable;
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Component;

class ManageDescriptions extends Component
{
    public $surveyForSection = null;
    public $batch_no = null;
    public $domain_id = null;
    
    public $scales = [];
    public $descriptions = [];

    protected $rules = [
        'descriptions.*.description' => 'required|string',
        'descriptions.*.need_processing' => 'nullable|string',
    ];

    public function mount()
    {
        // Load defaults if needed
    }

    public function updatedSurveyForSection()
    {
        $this->domain_id = null;
        $this->loadScales();
    }

    public function updatedBatchNo()
    {
        $this->loadScales();
    }

    public function updatedDomainId()
    {
        $this->loadDescriptions();
    }

    public function loadScales()
    {
        if (!$this->surveyForSection) {
            $this->scales = [];
            return;
        }

        $this->scales = SurveyGradingScaleTable::query()
            ->where('survey_for_section', $this->surveyForSection)
            ->where('type',150)
            ->when($this->batch_no, function ($query) {
                $query->where('batch_no', $this->batch_no);
            })
            ->orderBy('from_percentage')
            ->get();
            
        $this->loadDescriptions();
    }

    public function loadDescriptions()
    {
        $this->descriptions = [];

        if (!$this->domain_id || empty($this->scales)) {
            return;
        }

        foreach ($this->scales as $scale) {
            $existing = SurveyGradingScaleDescription::where('domain_id', $this->domain_id)
                ->where('survey_grading_scale_id', $scale->id)
                ->first();

            $this->descriptions[$scale->id] = [
                'description' => $existing ? $existing->description : '',
                'need_processing' => $existing ? $existing->need_processing : '',
            ];
        }
    }

    public function save()
    {
        if (Gate::denies('survey.manage')) {
            abort(403, __('You do not have the necessary permissions.'));
        }

        $this->validate();

        foreach ($this->descriptions as $scaleId => $data) {
            SurveyGradingScaleDescription::updateOrCreate(
                [
                    'domain_id' => $this->domain_id,
                    'survey_grading_scale_id' => $scaleId,
                ],
                [
                    'description' => $data['description'],
                    'need_processing' => $data['need_processing'],
                ]
            );
        }

        session()->flash('message', __('Descriptions saved successfully.'));
    }

    #[Title('Manage Domain Descriptions')]
    public function render()
    {
        if (Gate::denies('survey.grading.scale.index')) {
            abort(403, __('You do not have the necessary permissions.'));
        }

        $sections = StatusRepo::statuses()->where('p_id_sub', config('appConstant.survey_for', 119));
        $batches = \App\Models\StudentGroup::select('batch_no')->distinct()->whereNotNull('batch_no')->orderBy('batch_no')->get();
        $domains = StatusRepo::statuses()->where('p_id_sub', config('appConstant.domains_of_assessment', 145));

        return view('livewire.org-app.survey-questions.grading-scale.manage-descriptions', [
            'heading' => __('Manage Domain Descriptions'),
            'subheading' => __('Define domain-specific descriptions for grading scales.'),
            'sections' => $sections,
            'batches' => $batches,
            'domains' => $domains,
        ]);
    }
}
