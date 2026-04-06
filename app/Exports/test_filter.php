<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Exports\SurveyResultsExport;

$surveyId = 138; // Sample ID
echo "Testing with Survey ID: $surveyId\n";

$export = new SurveyResultsExport($surveyId);
$query = $export->query();

$results = $query->get();
echo "Total Rows found: " . $results->count() . "\n";

foreach($results->take(5) as $r) {
    echo "Account: {$r->account_id} | Survey No: {$r->survey_no}\n";
}

$distinctSurveys = $results->pluck('survey_no')->unique();
echo "Distinct Survey IDs in output: " . implode(', ', $distinctSurveys->toArray()) . "\n";
