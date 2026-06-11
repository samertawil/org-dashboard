<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$survey = \DB::table('survey_table')->where('id', 120)->first();
if ($survey) {
    echo "Survey 120 in survey_table:\n";
    print_r($survey);
} else {
    echo "Survey 120 not found in survey_table!\n";
}

$surveys = \DB::table('survey_table')->get();
foreach ($surveys as $s) {
    echo "ID: {$s->id} | Name: {$s->survey_name} | Section: {$s->survey_for_section}\n";
}
