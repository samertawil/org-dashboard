<?php

use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\DB;

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

$out = "================ SURVEY 120 QUESTIONS & ANSWERS ================\n";
foreach ($questionIds as $qid) {
    $question = SurveyQuestion::find($qid);
    $questionText = $question ? $question->question_ar_text : 'Unknown Question';
    $answerOptions = $question ? json_encode($question->answer_options, JSON_UNESCAPED_UNICODE) : 'N/A';
    
    $out .= "Question ID: $qid\n";
    $out .= "Text: $questionText\n";
    $out .= "Answer Options (JSON): $answerOptions\n";
    
    // Get unique answers actually submitted in survey_answers
    $uniqueAnswers = SurveyAnswer::where('survey_no', $surveyNo)
        ->where('question_id', $qid)
        ->select('answer_ar_text', 'answer_label')
        ->distinct()
        ->get();
        
    $out .= "Unique Submitted Answers:\n";
    foreach ($uniqueAnswers as $ua) {
        $label = $ua->answer_label ? " (Label: {$ua->answer_label})" : "";
        $out .= "  - '{$ua->answer_ar_text}'$label\n";
    }
    $out .= "--------------------------------------------------------\n\n";
}

file_put_contents(__DIR__ . '/survey_120_summary.txt', $out);
echo "Done! Output written to scratch/survey_120_summary.txt\n";
