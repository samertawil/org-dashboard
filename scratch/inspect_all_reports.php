<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Report;
use App\Models\ReportBody;

echo "Starting inspect script...\n";
try {
    $reports = Report::orderByDesc('id')->limit(5)->get();
    echo "Reports found: " . $reports->count() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
foreach ($reports as $r) {
    echo "=========================================\n";
    echo "Report ID: {$r->id}\n";
    echo "Name: {$r->report_name}\n";
    echo "Date From: {$r->date_from}\n";
    echo "Date To: {$r->date_to}\n";
    echo "Student Groups: " . json_encode($r->student_group_ids) . "\n";
    echo "Schedules count: " . count($r->covered_educational_activity_schedules_ids ?? []) . "\n";
    echo "Schedules: " . json_encode($r->covered_educational_activity_schedules_ids) . "\n";
    
    $bodies = ReportBody::where('report_id', $r->id)->get();
    foreach ($bodies as $b) {
        echo "  - Body Item #{$b->item_order} (ID: {$b->id})\n";
        echo "    Content:\n{$b->content}\n";
    }
}
