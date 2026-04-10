<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$surveyTable = DB::table('survey_table')->get();
echo "Survey Table Sample:\n";
print_r($surveyTable->take(2)->toArray());

$surveyQuestions = DB::table('survey_questions')->select('batch_no', 'survey_for_section')->limit(5)->get();
echo "\nSurvey Questions Sample:\n";
print_r($surveyQuestions->toArray());

$status119 = DB::table('statuses')->where('p_id_sub', 119)->pluck('id')->toArray();
echo "\nStatus 119 sub IDs:\n";
print_r($status119);

$allSurveySectionsInTable = DB::table('survey_table')->pluck('survey_for_section')->unique()->toArray();
echo "\nSurvey Sections in survey_table:\n";
print_r($allSurveySectionsInTable);
