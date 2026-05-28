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
        $this->answer_label = $surveyAnswer->answer_label;
        $this->created_by = $surveyAnswer->created_by;
    }

    public function save()
    {
        $this->validate();

        // Resolve answer_label if question has answer_options
        $answerLabel = null;
        $question = $this->surveyAnswer->question;
        if ($question) {
            $options = $question->answer_options;
            if (!empty($options)) {
                if (is_string($options)) {
                    $options = json_decode($options, true);
                }
                if (is_array($options)) {
                    $decodedVal = json_decode($this->answer_ar_text, true);
                    $values = (json_last_error() === JSON_ERROR_NONE && is_array($decodedVal)) ? $decodedVal : [$this->answer_ar_text];
                    
                    $labels = [];
                    foreach ($values as $val) {
                        $found = $val;
                        foreach ($options as $option) {
                            if (is_array($option) && isset($option['value']) && isset($option['label'])) {
                                if ((string) $option['value'] === (string) $val) {
                                    $found = $option['label'];
                                    break;
                                }
                            } elseif (is_string($option)) {
                                if ((string) $option === (string) $val) {
                                    $found = $option;
                                    break;
                                }
                            }
                        }
                        $labels[] = $found;
                    }
                    $answerLabel = implode('، ', $labels);
                }
            }
        }

        $this->surveyAnswer->update([
            'account_id' => $this->account_id ?: null,
            'survey_no' => $this->survey_no,
            'question_id' => $this->question_id ?: null,
            'answer_ar_text' => $this->answer_ar_text,
            'answer_label' => $answerLabel,
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
