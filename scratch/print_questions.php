<?php

use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$surveyNo = 120;

$questionIds = SurveyAnswer::where('survey_no', $surveyNo)
    ->whereNotNull('question_id')
    ->distinct()
    ->pluck('question_id')
    ->toArray();

echo "Questions inside Survey 120:\n";
foreach ($questionIds as $qid) {
    $question = SurveyQuestion::find($qid);
    $questionText = $question ? $question->question_ar_text : 'Unknown';
    echo "ID: $qid | Text: $questionText\n";
}
