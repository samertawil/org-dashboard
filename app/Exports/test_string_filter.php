<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Exports\SurveyResultsExport;

$val = "138"; // Selected from dropdown
echo "Simulating Livewire selection: '$val'\n";

$export = new SurveyResultsExport($val);
$query = $export->query();

echo "Final Base Query SQL:\n";
echo $query->toSql() . "\n";

echo "Final Bindings:\n";
print_r($query->getBindings());

$distinctSurveys = $query->get()->pluck('survey_no')->unique();
echo "Distinct Surveys in result: " . implode(', ', $distinctSurveys->toArray()) . "\n";
