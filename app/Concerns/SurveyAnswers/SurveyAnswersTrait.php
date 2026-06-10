<?php

namespace App\Concerns\SurveyAnswers;

use App\Models\SurveyQuestion;
use App\Reposotries\employeeRepo;
use App\Reposotries\StudentRepo;
use App\Reposotries\SurveyTableRepo;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;


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
    public $answer_label = '';

    #[Validate('nullable|exists:employees,id')]
    public $created_by = '';

    #[Computed]
    public function questions()
    {
        return SurveyQuestion::all();
    }

    #[Computed]
    public function employees()
    {
        return employeeRepo::employees();
    }

    #[Computed]
    public function students()
    {
        return StudentRepo::students();
    }

    #[Computed()]
    public function surveys()
    {
        return SurveyTableRepo::surveys();
    }
}
