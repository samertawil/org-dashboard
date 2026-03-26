<?php

namespace App\Livewire\OrgApp\SurveyAnswers;

use Livewire\Component;
use App\Models\SurveyAnswer;
use Illuminate\Support\Facades\Gate;
use App\Concerns\SurveyAnswers\SurveyAnswersTrait;

class Create extends Component
{
    use SurveyAnswersTrait;

    public function save()
    {
        $this->validate();

        SurveyAnswer::create([
            'account_id' => $this->account_id ?: null,
            'survey_no' => $this->survey_no,
            'question_id' => $this->question_id ?: null,
            'answer_ar_text' => $this->answer_ar_text,
            'answer_en_text' => $this->answer_en_text,
            'created_by' => $this->created_by ?: null,
        ]);

        session()->flash('message', __('Survey Answer successfully created.'));
        return $this->redirect(route('survey-answers.index'), navigate: true);
    }

    public function render()
    {
        if (Gate::denies('survey.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        
        return view('livewire.org-app.survey-answers.create', [
            'heading' => __('Create Survey Answer'),
            'type' => 'save',
        ]);
    }
}
