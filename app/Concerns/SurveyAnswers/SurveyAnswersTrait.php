<?php

namespace App\Concerns\SurveyAnswers;

use Livewire\Attributes\Validate;
use App\Models\SurveyQuestion;
use App\Models\Employee;
use App\Models\Student;

trait SurveyAnswersTrait
{
    #[Validate('nullable|integer')]
    public $account_id = null;

    #[Validate('required|integer')]
    public $survey_no = '';

    #[Validate('nullable|exists:survey_questions,id')]
    public $question_id = '';

    #[Validate('nullable|string')]
    public $answer_ar_text = '';

    #[Validate('nullable|string')]
    public $answer_en_text = '';

    #[Validate('nullable|exists:employees,id')]
    public $created_by = '';

    public $questions = [];
    public $employees = [];
    public $students = [];
  

    public function bootSurveyAnswersTrait()
    {
        $this->questions = SurveyQuestion::all();
        $this->employees = Employee::all();
        $this->students = Student::get();
       
    }
}
