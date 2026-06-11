<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

config(['database.default' => 'sqlite']);
config(['database.connections.sqlite' => [
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => '',
    'foreign_key_constraints' => true,
]]);

DB::purge('sqlite');
DB::reconnect('sqlite');

Artisan::call('migrate:fresh');

// Let's replicate the setup:
echo "Seeding statuses...\n";
\App\Models\Status::forceCreate([
    'id' => 187,
    'status_name' => 'التعليم',
    'p_id_sub' => 185,
]);

\App\Models\Status::firstOrCreate([
    'id' => 120,
], [
    'status_name' => 'Survey Section 120',
    'p_id_sub' => 185,
]);

echo "Seeding user...\n";
\App\Models\User::factory()->create(['id' => 1]);

echo "Seeding survey_table...\n";
\DB::table('survey_table')->insert([
    'id' => 3,
    'survey_for_section' => 120,
]);

echo "Verifying survey_table contents:\n";
print_r(\DB::table('survey_table')->get()->toArray());

echo "Verifying statuses contents:\n";
print_r(\DB::table('statuses')->get()->toArray());

try {
    echo "Inserting survey_questions...\n";
    \App\Models\SurveyQuestion::forceCreate([
        'id' => 6,
        'question_ar_text' => 'Question 6',
        'survey_table_id' => 3,
        'survey_for_section' => 120,
        'answer_input_type' => 1,
        'batch_no' => 1,
    ]);
    echo "Successfully inserted!\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
    
    // Now let's disable foreign keys and insert, then re-enable and check pragma foreign_key_check
    echo "\nDisabling foreign keys to insert and debug...\n";
    DB::statement('PRAGMA foreign_keys = OFF');
    
    // Clear and insert
    DB::table('survey_questions')->delete();
    \App\Models\SurveyQuestion::forceCreate([
        'id' => 6,
        'question_ar_text' => 'Question 6',
        'survey_table_id' => 3,
        'survey_for_section' => 120,
        'answer_input_type' => 1,
        'batch_no' => 1,
    ]);
    
    $violations = DB::select('PRAGMA foreign_key_check(survey_questions)');
    echo "Foreign key check violations on survey_questions:\n";
    print_r($violations);
}
