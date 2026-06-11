<?php

use App\Models\ActivitySchedule;
use Illuminate\Support\Carbon;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$dateFrom = '2026-06-01';
$dateTo = '2026-06-11';

$domains = ActivitySchedule::query()
    ->whereDate('period_start', '>=', $dateFrom)
    ->whereDate('period_start', '<=', $dateTo)
    ->completed()
    ->active()
    ->select('educational_activity_domain', \DB::raw('count(*) as count'))
    ->groupBy('educational_activity_domain')
    ->get();

foreach ($domains as $d) {
    $statusName = \App\Models\Status::find($d->educational_activity_domain)?->status_name ?? 'Unknown';
    echo "Domain ID: {$d->educational_activity_domain} ({$statusName}) - Count: {$d->count}\n";
}
