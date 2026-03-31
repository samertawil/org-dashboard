<?php

namespace App\Livewire\OrgApp\SurveyQuestions;

use App\Models\SurveyQuestion;
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

class Manage extends Component
{
    public $surveyForSection = null;
    public $questions = [];

    protected $rules = [
        'questions.*.question_order' => 'required|numeric',
        'questions.*.question_ar_text' => 'required|string|max:255',
        'questions.*.question_en_text' => 'nullable|string|max:255',
        'questions.*.answer_input_type' => 'required|numeric',
        'questions.*.answer_options' => 'nullable|array',
        'questions.*.answer_options.*.label' => 'nullable|string',
        'questions.*.answer_options.*.value' => 'nullable|string',
        'questions.*.require_detail' => 'nullable|boolean',
        'questions.*.detail' => 'nullable|string',
        'questions.*.note' => 'nullable|string',
        'questions.*.domain_id' => 'required|integer',
    ];

    public function mount()
    {
        // Load data on mount if surveyForSection is already set .
        if ($this->surveyForSection) {
            $this->loadQuestions();
        }
    }


    public function updatedSurveyForSection($value)
    {

        $this->loadQuestions();
    }

    public function loadQuestions()
    {
        $this->resetValidation();

        if ($this->surveyForSection) {
            $this->questions = SurveyQuestion::where('survey_for_section', $this->surveyForSection)
                ->orderBy('question_order')
                ->get()
                ->map(function ($q) {
                    $qArray = $q->toArray();
                    $qArray['require_detail'] = (bool) $q->require_detail;
                    $qArray['answer_options'] = is_array($q->answer_options) ? $q->answer_options : [];
                    return $qArray;
                })
                ->toArray();
        } else {
            $this->questions = [];
        }
    }

    public function addQuestion()
    {
        if (!$this->surveyForSection) {
            return;
        }

        $maxOrder = empty($this->questions) ? 0 : max(array_column($this->questions, 'question_order'));

        array_unshift($this->questions, [
            'id' => null,
            'survey_for_section' => $this->surveyForSection,
            'question_order' => $maxOrder + 1,
            'question_ar_text' => '',
            'question_en_text' => '',
            'answer_input_type' => 1,
            'answer_options' => [],
            'require_detail' => false,
            'detail' => '',
            'note' => '',
            'domain_id' => '',
        ]);
    }

    public function removeQuestion($index)
    {
        if (isset($this->questions[$index]['id']) && $this->questions[$index]['id']) {
            SurveyQuestion::find($this->questions[$index]['id'])->delete();
        }

        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);

        session()->flash('message', __('Deleted successfully'));
    }

    public function addAnswerOption($questionIndex)
    {
        if (!isset($this->questions[$questionIndex]['answer_options']) || !is_array($this->questions[$questionIndex]['answer_options'])) {
            $this->questions[$questionIndex]['answer_options'] = [];
        }
        $this->questions[$questionIndex]['answer_options'][] = ['label' => '', 'value' => ''];
    }

    public function removeAnswerOption($questionIndex, $optionIndex)
    {
        if (isset($this->questions[$questionIndex]['answer_options'][$optionIndex])) {
            unset($this->questions[$questionIndex]['answer_options'][$optionIndex]);
            $this->questions[$questionIndex]['answer_options'] = array_values($this->questions[$questionIndex]['answer_options']);
        }
    }

    public function save()
    {
        
        $this->validate();

        if (!$this->surveyForSection) {
            return;
        }

        $anyUpdated = false;
        $anyCreated = false;

        foreach ($this->questions as $index => $q) {
            if (!empty($q['id'])) {
                $question = SurveyQuestion::findOrFail($q['id']);
                $question->fill([
                    'question_order' => $q['question_order'] ?? null,
                    'question_ar_text' => $q['question_ar_text'] ?? '',
                    'question_en_text' => $q['question_en_text'] ?? null,
                    'answer_input_type' => $q['answer_input_type'] ?? 1,
                    'answer_options' => ($q['answer_input_type'] == 2 && !empty($q['answer_options'])) ? $q['answer_options'] : null,
                    'require_detail' => !empty($q['require_detail']) ? 1 : 0,
                    'detail' => $q['detail'] ?? null,
                    'note' => $q['note'] ?? null,
                    'domain_id' => $q['domain_id'] ?? null,
                    'updated_by'=>Auth::user()->id,
                   
                ]);
               
                if ($question->isDirty()) {
                    $question->save();
                    $anyUpdated = true;
                }
            } else {
                $created = SurveyQuestion::create([
                    'survey_for_section' => $this->surveyForSection,
                    'question_order' => $q['question_order'] ?? null,
                    'question_ar_text' => $q['question_ar_text'] ?? '',
                    'question_en_text' => $q['question_en_text'] ?? null,
                    'answer_input_type' => $q['answer_input_type'] ?? 1,
                    'answer_options' => ($q['answer_input_type'] == 2 && !empty($q['answer_options'])) ? $q['answer_options'] : null,
                    'require_detail' => !empty($q['require_detail']) ? 1 : 0,
                    'detail' => $q['detail'] ?? null,
                    'note' => $q['note'] ?? null,
                    'domain_id' => $q['domain_id'] ?? null,
                    'created_by'=>Auth::user()->id,
                   
                ]);

                $this->questions[$index]['id'] = $created->id;
                $anyCreated = true;
            }
        }

        if ($anyCreated || $anyUpdated) {
            session()->flash('message', __('Saved successfully'));
            session()->flash('type', 'success');
        } else {
            session()->flash('message', __('No changes were made!'));
            session()->flash('type', 'warning');
        }

        $this->dispatch('scroll-to-top');
    }

    #[Title('Survey Questions')]
    public function render()
    {
         
        $surceyFor = StatusRepo::statuses()->whereIn('p_id_sub', [config('appConstant.survey_for'),config('appConstant.domains_of_assessment')]);

        return view('livewire.org-app.survey-questions.manage', [
            'heading' => __('New Survey Questions'),
            'subheading' => __('Create and manage survey questions'),
            'surveyFor' => $surceyFor,

        ]);
    }
}
