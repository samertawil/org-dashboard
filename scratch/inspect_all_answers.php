<?php

use App\Models\SurveyAnswer;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$answers = SurveyAnswer::where('answer_ar_text', 'like', '%تحويل%')
    ->orWhere('answer_ar_text', 'like', '%حول%')
    ->get();

echo "Total matching answers in entire database: " . $answers->count() . "\n";
foreach ($answers->take(30) as $a) {
    echo "Survey No: {$a->survey_no} | Student: {$a->account_id} | QID: {$a->question_id} | Answer: '{$a->answer_ar_text}'\n";
}
