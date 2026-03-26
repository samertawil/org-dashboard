<?php

namespace App\Livewire\OrgApp\SurveyQuestions;

use App\Models\SurveyQuestion;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
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
    ];

    public function mount()
    {
        // Load data on mount if surveyForSection is already set .
        if ($this->surveyForSection) {
            $this->loadQuestions();
        }
    }

    #[Computed()]
    public function surveySections()
    {
        // Get all statuses that are used as a survey section
        return SurveyQuestion::with('surveyForSection:id,status_name')->select('survey_for_section')->distinct()->get();
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
                ->map(function($q) {
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

        foreach ($this->questions as $index => $q) {
            if (!empty($q['id'])) {
                SurveyQuestion::where('id', $q['id'])->update([
                    'question_order' => $q['question_order'] ?? null,
                    'question_ar_text' => $q['question_ar_text'] ?? '',
                    'question_en_text' => $q['question_en_text'] ?? null,
                    'answer_input_type' => $q['answer_input_type'] ?? 1,
                    'answer_options' => ($q['answer_input_type'] == 2 && !empty($q['answer_options'])) ? $q['answer_options'] : null,
                    'require_detail' => !empty($q['require_detail']) ? 1 : 0,
                    'detail' => $q['detail'] ?? null,
                    'note' => $q['note'] ?? null,
                ]);
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
                ]);
                
                $this->questions[$index]['id'] = $created->id;
            }
        }

        session()->flash('message', __('Saved successfully'));
        $this->dispatch('scroll-to-top');
    }

    #[Title('Survey Questions')]
    public function render()
    {
        return view('livewire.org-app.survey-questions.index', [
            'heading' => __('Survey Questions'),
            // 'subheading' => __('إدارة أسئلة الاستبيان بطريقة مرنة وسهلة'),
        ]);
    }
}
