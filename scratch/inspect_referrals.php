<?php

use App\Models\SurveyAnswer;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$surveyNo = 120;

$answers = SurveyAnswer::where('survey_no', $surveyNo)
    ->where(function($q) {
        $q->where('answer_ar_text', 'like', '%تحويل%')
          ->orWhere('answer_ar_text', 'like', '%جهة%')
          ->orWhere('answer_ar_text', 'like', '%حول%')
          ->orWhere('answer_ar_text', 'like', '%مؤسسة%');
    })
    ->get();

echo "Total matching answers: " . $answers->count() . "\n";
foreach ($answers as $a) {
    echo "Student: {$a->account_id} | QID: {$a->question_id} | Answer: '{$a->answer_ar_text}'\n";
}
