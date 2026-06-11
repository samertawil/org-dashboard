<?php

use App\Models\ActivitySchedule;
use Illuminate\Support\Carbon;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$dateFrom = '2026-06-01';
$dateTo = '2026-06-11';

$schedules = ActivitySchedule::query()
    ->whereDate('period_start', '>=', $dateFrom)
    ->whereDate('period_start', '<=', $dateTo)
    ->whereIn('educational_activity_domain', [187, 188, 190])
    ->completed()
    ->active()
    ->with('activityDetail')
    ->get();

echo "Total Schedules: " . $schedules->count() . "\n";

$totalConsistent = 0;
$nullDetailCount = 0;
$nullConsistentCount = 0;

foreach ($schedules as $s) {
    if (!$s->activityDetail) {
        $nullDetailCount++;
        continue;
    }
    if ($s->activityDetail->consistent === null) {
        $nullConsistentCount++;
    } else {
        $totalConsistent += $s->activityDetail->consistent;
    }
}

echo "Null Details: $nullDetailCount\n";
echo "Null Consistent field: $nullConsistentCount\n";
echo "Total Consistent sum: $totalConsistent\n";
