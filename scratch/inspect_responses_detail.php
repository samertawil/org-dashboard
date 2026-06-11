<?php

use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$surveyNo = 120;
$targetQids = [6, 7, 9, 10, 11, 12, 13, 101];

foreach ($targetQids as $qid) {
    $question = SurveyQuestion::find($qid);
    $questionText = $question ? $question->question_ar_text : 'Unknown';
    echo "ID: $qid | Question: $questionText\n";
    
    $uniqueAnswers = SurveyAnswer::where('survey_no', $surveyNo)
        ->where('question_id', $qid)
        ->select('answer_ar_text', 'answer_label')
        ->distinct()
        ->get();
        
    foreach ($uniqueAnswers as $ua) {
        echo "  - Text: '{$ua->answer_ar_text}' | Label: '{$ua->answer_label}'\n";
    }
    echo "\n";
}
