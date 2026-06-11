<?php

use App\Models\SurveyQuestion;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$questions = SurveyQuestion::where('question_ar_text', 'like', '%تحويل%')
    ->orWhere('question_ar_text', 'like', '%جهة%')
    ->orWhere('question_ar_text', 'like', '%حول%')
    ->orWhere('question_ar_text', 'like', '%مؤسسة%')
    ->get();

echo "Total matching questions in database: " . $questions->count() . "\n";
foreach ($questions as $q) {
    echo "ID: {$q->id} | Survey Table ID: {$q->survey_table_id} | Text: {$q->question_ar_text}\n";
}
