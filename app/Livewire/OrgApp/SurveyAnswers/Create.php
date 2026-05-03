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
    
    public function mount()
    {
        $this->account_id = request()->query('account_id', $this->account_id);
        $this->loadAnswers();
    }

    #[Validate('required|integer')]
    public $surveyForSection = '';

    public array $answers = [];

    #[Computed()]
    public function filledSurveys()
    {
        if (!$this->account_id) {
            return [];
        }
        return SurveyAnswer::where('account_id', $this->account_id)
            ->pluck('survey_no')
            ->unique()
            ->toArray();
    }

    #[Computed()]
    public function questionsBySurveyForSection()
    {
        if (!$this->surveyForSection) {
            return collect();
        }

        $student = $this->student;
        $batch_no = $student?->studentGroup?->batch_no;

        if (!$batch_no) {
            return collect();
        }

        return SurveyQuestion::with('domainRel')
            ->where('survey_for_section', $this->surveyForSection)
            ->where('batch_no', $batch_no)
            ->orderBy('question_order', 'asc')
            ->get();
    }

    #[Computed()]
    public function student()
    {
        if (!$this->account_id) {
            return null;
        }
        return \App\Models\Student::where('identity_number', $this->account_id)->first();
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
        $existingAnswers = SurveyAnswer::where('survey_no', $this->surveyForSection)
            ->where('account_id', $this->account_id)
            ->get();

        $this->answers = [];
        // Populate the $answers array so the blade form inputs show the previously saved answers
        foreach ($existingAnswers as $answer) {
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
            'account_id' => 'required|integer', 
        ]);

        $questions = $this->questionsBySurveyForSection;
        
        // Eager load all existing answers for this section and account to avoid N+1 in the loop
        $existingAnswers = SurveyAnswer::where('survey_no', $this->surveyForSection)
            ->where('account_id', $this->account_id)
            ->get()
            ->keyBy('question_id');

        $savedCount = 0;
        $employeeId = auth()->user()?->employee?->id ?? null;

        foreach ($questions as $question) {
            $arText = $this->answers[$question->id] ?? null;

            if ($arText !== null && $arText !== '') {
                if (is_array($arText)) {
                    $arText = json_encode($arText, JSON_UNESCAPED_UNICODE);
                }

                $surveyAnswer = $existingAnswers->get($question->id) ?? new SurveyAnswer([
                    'survey_no' => $this->surveyForSection,
                    'account_id' => $this->account_id,
                    'question_id' => $question->id,
                ]);

                $surveyAnswer->fill([
                    'answer_ar_text' => $arText,
                    'answer_en_text' => null,
                    'created_by' => $employeeId,
                    
                ]);

                if ($surveyAnswer->isDirty()) {
                    $surveyAnswer->save();
                    $savedCount++;
                }
            } else {
                // If answer was cleared, delete if it exists
                $answerToDelete = $existingAnswers->get($question->id);
                if ($answerToDelete instanceof SurveyAnswer) {
                    $answerToDelete->delete();
                    $savedCount++;
                }
            }
        }

        if ($savedCount > 0) {
            session()->flash('message', __(':count Survey Answers successfully saved/updated.', ['count' => $savedCount]));
        } else {
            session()->flash('message', __('No changes were made.'));
        }
        $this->dispatch('scroll-to-top');
        // return $this->redirect(route('survey-answers.index'), navigate: true);
    }

    #[Computed()]
    public function calcAnswers() {
       if( ($this->surveyForSection) &&  ($this->account_id)) {
         $answersCount = SurveyAnswer::calculateAnswer($this->surveyForSection, $this->account_id );
         $questionsCount = count($this->questionsBySurveyForSection);
          return [
              'answersCount' => $answersCount,
              'questionsCount' => $questionsCount
          ];
        } else {
            return [
                'answersCount' => 0,
                'questionsCount' => 0
            ];
        }
    }
    public function render()
    {
        
        $surceyFor = StatusRepo::statuses()->where('p_id_sub',config('appConstant.survey_for')) ;

        if (Gate::denies('survey-answers.create')) {
            abort(403, 'You do not have the necessary permissions');
        }

        return view('livewire.org-app.survey-answers.create', [
            'heading' => __('Create Survey Answer'),
            'type' => 'save',
            'surveyFor' => $surceyFor,
        ]);
    }
}
