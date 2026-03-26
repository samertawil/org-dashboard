<?php

namespace App\Livewire\OrgApp\SurveyAnswers;

use Livewire\Component;
use App\Models\SurveyAnswer;
use Illuminate\Support\Facades\Gate;
use App\Concerns\SurveyAnswers\SurveyAnswersTrait;

class Edit extends Component
{
    use SurveyAnswersTrait;

    public SurveyAnswer $surveyAnswer;

    public function mount(SurveyAnswer $surveyAnswer)
    {
        $this->surveyAnswer = $surveyAnswer;
        $this->account_id = $surveyAnswer->account_id;
        $this->survey_no = $surveyAnswer->survey_no;
        $this->question_id = $surveyAnswer->question_id;
        $this->answer_ar_text = $surveyAnswer->answer_ar_text;
        $this->answer_en_text = $surveyAnswer->answer_en_text;
        $this->created_by = $surveyAnswer->created_by;
    }

    public function save()
    {
        $this->validate();

        $this->surveyAnswer->update([
            'account_id' => $this->account_id ?: null,
            'survey_no' => $this->survey_no,
            'question_id' => $this->question_id ?: null,
            'answer_ar_text' => $this->answer_ar_text,
            'answer_en_text' => $this->answer_en_text,
            'created_by' => $this->created_by ?: null,
        ]);

        session()->flash('message', __('Survey Answer successfully updated.'));
        return $this->redirect(route('survey-answers.index'), navigate: true);
    }

    public function render()
    {
        if (Gate::denies('survey.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        
        return view('livewire.org-app.survey-answers.edit', [
            'heading' => __('Edit Survey Answer'),
            'type' => 'update',
        ]);
    }
}
