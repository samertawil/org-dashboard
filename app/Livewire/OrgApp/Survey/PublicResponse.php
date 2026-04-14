<?php

namespace App\Livewire\OrgApp\Survey;

use App\Models\SurveyTable;
use App\Models\SurveyQuestion;
use App\Models\SurveyAnswer;
use Livewire\Component;

class PublicResponse extends Component
{
    public $survey;
    public $account_id;
    public $answers = [];
    public $step = 1; // 1: Login, 2: Survey, 0: Closed

    protected $rules = [
        'account_id' => 'required|string|min:3',
    ];

    public function mount($id)
    {
        $this->survey = SurveyTable::with('questions')->findOrFail($id);
        
        if (!$this->survey->is_active) {
            $this->step = 0; // Closed state
        }
    }

    public function startSurvey()
    {
        $this->validate();
        
        // Initialize answers array
        foreach ($this->survey->questions as $question) {
            $this->answers[$question->id] = [
                'answer' => '',
                'detail' => '',
            ];
        }

        $this->step = 2;
    }

    public function submit()
    {
        foreach ($this->survey->questions as $question) {
            $answerData = $this->answers[$question->id];
            
            SurveyAnswer::create([
                'survey_table_id' => $this->survey->id,
                'account_id' => $this->account_id,
                'question_id' => $question->id,
                'answer_ar_text' => is_array($answerData['answer']) ? json_encode($answerData['answer']) : $answerData['answer'],
                'survey_no' => $this->survey->survey_for_section, // Using section as survey_no for legacy compatibility
            ]);
        }

        session()->flash('message', __('Thank you! Your response has been recorded.'));
        $this->step = 3; // Thank you step
    }

    public function render()
    {
        return view('livewire.org-app.survey.public-response')
            ->layout('layouts.guest'); // Use guest layout for public access
    }
}
