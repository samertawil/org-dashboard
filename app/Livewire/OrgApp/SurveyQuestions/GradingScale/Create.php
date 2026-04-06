<?php

namespace App\Livewire\OrgApp\SurveyQuestions\GradingScale;

use Livewire\Component;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Models\SurveyGradingScaleTable;
use App\Models\StudentGroup;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

class Create extends Component
{
    #[Validate('required|integer|min:0|max:100')]
    public $from_percentage = 0;

    #[Validate('required|integer|min:0|max:100')]
    public $to_percentage = 100;

    #[Validate('required|string|max:255')]
    public $evaluation = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('nullable|integer|exists:statuses,id')]
    public $type = '';

    #[Validate('required|integer')]
    public $batch_no = '';

    #[Validate('nullable|integer|exists:statuses,id')]
    public $survey_for_section = '';

    #[Validate('nullable|integer|exists:statuses,id')]
    public $question_type = '';

    public function save()
    {
        $this->validate();

        if (Gate::denies('survey.manage')) 
        { 
            abort(403, __('You do not have the necessary permissions.'));
        }

        SurveyGradingScaleTable::create([
            'from_percentage' => $this->from_percentage,
            'to_percentage' => $this->to_percentage,
            'evaluation' => $this->evaluation,
            'description' => $this->description,
            'type' => $this->type ?: null,
            'batch_no' => $this->batch_no,
            'survey_for_section' => $this->survey_for_section ?: null,
            'question_type' => $this->question_type ?: null,
            'created_by' => Auth::user()->employee->id ,
        ]);

        session()->flash('message', __('Survey Grading Scale successfully created.'));

        return $this->redirect(route('survey.grading.scale.index'), navigate: true);
    }

    #[Computed()]
    public function batches()
    {
        return StudentGroup::select('batch_no')->distinct()->whereNotNull('batch_no')->orderBy('batch_no')->get();
    }

    #[Computed()]
    public function surveySections()
    {
        return StatusRepo::statuses()->where('p_id_sub', config('appConstant.survey_for', 119));
    }

    #[Computed()]
    public function gradingTypes()
    {
        return StatusRepo::statuses()->where('p_id_sub', 149);
    }

    #[Computed()]
    public function questionTypes()
    {
        return StatusRepo::statuses()->where('p_id_sub', 152);
    }

    #[Title('Create Survey Grading Scale')]
    public function render()
    {
      
        if (Gate::denies('survey.manage')) 
        { 
            abort(403, __('You do not have the necessary permissions.'));
        }

        return view('livewire.org-app.survey-questions.grading-scale.create', [
            'heading' => __('Create Survey Grading Scale'),
            'type' => 'save',
        ]);
    }
}
