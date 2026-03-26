<?php

namespace App\Livewire\OrgApp\SurveyAnswers;

use App\Concerns\SurveyAnswers\SurveyAnswersTrait;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    use SurveyAnswersTrait;

    #[Validate('required|integer')]
    public $surveyForSection = '';

    public array $answers = [];
    public $surveyAnswers = [];

    #[Computed()]
    public function questionsBySurveyForSection()
    {
        if (!$this->surveyForSection) {
            return collect();
        }
        return SurveyQuestion::where('survey_for_section', $this->surveyForSection)
            ->orderBy('question_order', 'asc')
            ->get();
    }

    // Triggered automatically by Livewire when surveyForSection is changed
    public function updatedSurveyForSection()
    {
        $this->answers = [];
        $this->loadAnswers();
    }

    

    // Triggered automatically by Livewire when account_id is changed
    public function updatedAccountId()
    {
        $this->loadAnswers();
    }

    public function loadAnswers()
    {
        if (empty($this->surveyForSection) || empty($this->account_id)) {
            return;
        }

        // Fetch any existing answers for this specific student and survey section
        $this->surveyAnswers = SurveyAnswer::where('survey_no', $this->surveyForSection)
            ->where('account_id', $this->account_id)
            ->get();

        // Populate the $answers array so the blade form inputs show the previously saved answers
        foreach ($this->surveyAnswers as $answer) {
            $arText = $answer->answer_ar_text;
            
            // Check if what is saved is a JSON array (like for checked checkboxes)
            $decoded = json_decode($arText, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->answers[$answer->question_id] = $decoded;
            } else {
                $this->answers[$answer->question_id] = $arText;
            }
        }
    }

    public function save()
    {
        $this->validate([
            'surveyForSection' => 'required|integer',
            'account_id' => 'required|integer', // Ensure student is selected
        ]);

        $questions = collect($this->questionsBySurveyForSection);
        $savedCount = 0;

        foreach ($questions as $question) {
            $arText = $this->answers[$question->id] ?? null;

            if ($arText !== null && $arText !== '') {
                // Encode array choices into JSON smoothly
                if (is_array($arText)) {
                    $arText = json_encode($arText, JSON_UNESCAPED_UNICODE);
                }

                // Fetch existing or initialize a new model
                $surveyAnswer = SurveyAnswer::firstOrNew([
                    'survey_no' => $this->surveyForSection,
                    'account_id' => $this->account_id,
                    'question_id' => $question->id,
                ]);

                // Fill values securely
                $surveyAnswer->fill([
                    'answer_ar_text' => $arText,
                    'answer_en_text' => null,
                    'created_by' => auth()->user()?->employee?->id ?? null,
                ]);

                // Only save and increment count if the values actually changed
                if ($surveyAnswer->isDirty()) {
                    $surveyAnswer->save();
                    $savedCount++;
                }
            } else {
                // If answer was cleared out by user, check if we need to delete
                $deletedCount = SurveyAnswer::where('survey_no', $this->surveyForSection)
                    ->where('account_id', $this->account_id)
                    ->where('question_id', $question->id)
                    ->delete();

                if ($deletedCount > 0) {
                    $savedCount++;
                }
            }
        }

        if ($savedCount > 0) {
            session()->flash('message', __(':count Survey Answers successfully saved/updated.', ['count' => $savedCount]));
        } else {
            session()->flash('message', __('No answers were provided.'));
        }

        return $this->redirect(route('survey-answers.index'), navigate: true);
    }

    public function render()
    {
        $surceyFor = StatusRepo::statuses()->where('p_id_sub',config('appConstant.survey_for')) ;

        if (Gate::denies('survey.create')) {
            abort(403, 'You do not have the necessary permissions');
        }

        return view('livewire.org-app.survey-answers.create', [
            'heading' => __('Create Survey Answer'),
            'type' => 'save',
            'surveyFor' => $surceyFor,
        ]);
    }
}
