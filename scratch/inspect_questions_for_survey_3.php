<?php

use App\Models\SurveyQuestion;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$surveyTableId = 3;

$questions = SurveyQuestion::where('survey_table_id', $surveyTableId)
    ->orderBy('question_order')
    ->get();

echo "Total questions defined for Survey ID 3 in database: " . $questions->count() . "\n";
foreach ($questions as $q) {
    echo "ID: {$q->id} | Order: {$q->question_order} | Text: {$q->question_ar_text}\n";
}
