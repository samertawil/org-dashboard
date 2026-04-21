<?php

namespace App\Livewire\OrgApp\SurveyQuestions\ComparisonScale;

use Livewire\Component;
use App\Models\SurveyComparisonScale;
use App\Models\Status;
use App\Models\StudentGroup;
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\Gate;

class Edit extends Component
{
    public $scale_id;
    public $from_percentage;
    public $to_percentage;
    public $evaluation;
    public $description;
    public $color;
    public $domain_id;
    public $batch_no;
    public $survey_for_section;

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

    public function mount($id)
    {
        $scale = SurveyComparisonScale::findOrFail($id);
        $this->scale_id = $scale->id;
        $this->from_percentage = $scale->from_percentage;
        $this->to_percentage = $scale->to_percentage;
        $this->evaluation = $scale->evaluation;
        $this->description = $scale->description;
        $this->color = $scale->color;
        $this->domain_id = $scale->domain_id;
        $this->batch_no = $scale->batch_no;
        $this->survey_for_section = $scale->survey_for_section;
    }

    public function save()
    {
        if (Gate::denies('survey.manage')) {
            abort(403, __('You do not have the necessary permissions.'));
        }

        $this->validate();

        $scale = SurveyComparisonScale::findOrFail($this->scale_id);
        $scale->update([
            'from_percentage' => $this->from_percentage,
            'to_percentage' => $this->to_percentage,
            'evaluation' => $this->evaluation,
            'description' => $this->description,
            'color' => $this->color,
            'domain_id' => $this->domain_id,
            'batch_no' => $this->batch_no,
            'survey_for_section' => $this->survey_for_section,
            'updated_by' => auth()->id(),
        ]);

        session()->flash('success', __('Comparison Scale updated successfully.'));
        return redirect()->route('org-app.survey-questions.comparison-scale.index');
    }

    public function render()
    {
        $domains = StatusRepo::statuses()->where('p_id_sub', config('appConstant.domains_of_assessment', 145));
        $batches = StudentGroup::select('batch_no')->distinct()->whereNotNull('batch_no')->orderBy('batch_no')->get();
        $surveySections = StatusRepo::statuses()->where('p_id_sub', config('appConstant.survey_for', 119));

        return view('livewire.org-app.survey-questions.comparison-scale.edit', [
            'domains' => $domains,
            'batches' => $batches,
            'surveySections' => $surveySections,
        ]);
    }
}
