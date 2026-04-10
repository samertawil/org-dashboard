<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$batch = 3; // From research before
$section = 120; // From research before

$scales = DB::table('survey_grading_scale_tables')
    ->where('batch_no', $batch)
    ->where('survey_for_section', $section)
    ->get();

echo "Scales for Batch $batch, Section $section:\n";
print_r($scales->toArray());

$allReq = DB::table('survey_table')->pluck('survey_for_section')->unique()->toArray();
echo "\nAll Required Sections:\n";
print_r($allReq);
