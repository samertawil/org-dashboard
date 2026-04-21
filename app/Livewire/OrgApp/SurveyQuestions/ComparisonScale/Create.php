<?php

namespace App\Livewire\OrgApp\SurveyQuestions\ComparisonScale;

use Livewire\Component;
use App\Models\SurveyComparisonScale;
use App\Models\Status;
use App\Models\StudentGroup;
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    public $from_percentage = 0;
    public $to_percentage = 100;
    public $evaluation = '';
    public $description = '';
    public $color = '#3b82f6';
    public $domain_id = null;
    public $batch_no = '';
    public $survey_for_section = null;

    protected $rules = [
        'from_percentage' => 'required|numeric',
        'to_percentage' => 'required|numeric',
        'evaluation' => 'required|string|max:255',
        'description' => 'nullable|string',
        'color' => 'nullable|string|max:7',
        'domain_id' => 'nullable|exists:statuses,id',
        'batch_no' => 'nullable|string',
        'survey_for_section' => 'nullable|exists:statuses,id',
    ];

    public function mount()
    {
        $this->survey_for_section = config('appConstant.survey_question_ages_6_9', 137);
    }

    public function save()
    {
        if (Gate::denies('survey.manage')) {
            abort(403, __('You do not have the necessary permissions.'));
        }

        $this->validate();

        SurveyComparisonScale::create([
            'from_percentage' => $this->from_percentage,
            'to_percentage' => $this->to_percentage,
            'evaluation' => $this->evaluation,
            'description' => $this->description,
            'color' => $this->color,
            'domain_id' => $this->domain_id,
            'batch_no' => $this->batch_no,
            'survey_for_section' => $this->survey_for_section,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', __('Comparison Scale created successfully.'));
        return redirect()->route('org-app.survey-questions.comparison-scale.index');
    }

    public function render()
    {
        $domains = StatusRepo::statuses()->where('p_id_sub', config('appConstant.domains_of_assessment', 145));
        $batches = StudentGroup::select('batch_no')->distinct()->whereNotNull('batch_no')->orderBy('batch_no')->get();
        $surveySections = StatusRepo::statuses()->where('p_id_sub', config('appConstant.survey_for', 119));

        return view('livewire.org-app.survey-questions.comparison-scale.create', [
            'domains' => $domains,
            'batches' => $batches,
            'surveySections' => $surveySections,
        ]);
    }
}
