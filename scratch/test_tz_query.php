<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActivitySchedule;
use Illuminate\Support\Carbon;

// Date range in July 2026
$dateFrom = '2026-07-01';
$dateTo = '2026-07-01';

echo "=== Query Method A: whereDate (Current Code) ===\n";
$q1 = ActivitySchedule::query()
    ->whereDate('period_start', '>=', $dateFrom)
    ->whereDate('period_start', '<=', $dateTo);

echo "Total Schedules: " . $q1->count() . "\n";
$schedulesA = $q1->get(['id', 'group_id', 'activity_name', 'period_start']);

echo "\n=== Query Method B: Carbon UTC bounds ===\n";
$start = Carbon::parse($dateFrom)->startOfDay()->setTimezone('UTC');
$end = Carbon::parse($dateTo)->endOfDay()->setTimezone('UTC');
echo "UTC Range: [$start, $end]\n";

$q2 = ActivitySchedule::query()
    ->where('period_start', '>=', $start)
    ->where('period_start', '<=', $end);

echo "Total Schedules: " . $q2->count() . "\n";
$schedulesB = $q2->get(['id', 'group_id', 'activity_name', 'period_start']);

echo "\n=== Differences ===\n";
$idsA = $schedulesA->pluck('id')->toArray();
$idsB = $schedulesB->pluck('id')->toArray();

$onlyInA = array_diff($idsA, $idsB);
$onlyInB = array_diff($idsB, $idsA);

echo "Only in whereDate (A): " . count($onlyInA) . "\n";
foreach ($onlyInA as $id) {
    $s = $schedulesA->firstWhere('id', $id);
    echo "  ID: {$s->id}, Start: {$s->period_start} (local is " . $s->period_start->setTimezone('Asia/Gaza') . ")\n";
}

echo "Only in Carbon UTC (B): " . count($onlyInB) . "\n";
foreach ($onlyInB as $id) {
    $s = $schedulesB->firstWhere('id', $id);
    echo "  ID: {$s->id}, Start: {$s->period_start} (local is " . $s->period_start->setTimezone('Asia/Gaza') . ")\n";
}
