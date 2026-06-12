<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Carbon;

echo "Default PHP Timezone: " . date_default_timezone_set('Asia/Gaza') . " -> " . date_default_timezone_get() . "\n";
echo "Carbon Timezone: " . Carbon::now()->timezoneName . "\n";

$dateFrom = '2026-06-01';
$dateTo = '2026-06-12';

$localStart = Carbon::parse($dateFrom)->startOfDay();
$utcStart = $localStart->copy()->setTimezone('UTC');

$localEnd = Carbon::parse($dateTo)->endOfDay();
$utcEnd = $localEnd->copy()->setTimezone('UTC');

echo "Local Start: $localStart (" . $localStart->timezoneName . ")\n";
echo "UTC Start: $utcStart (" . $utcStart->timezoneName . ")\n";
echo "Local End: $localEnd (" . $localEnd->timezoneName . ")\n";
echo "UTC End: $utcEnd (" . $utcEnd->timezoneName . ")\n";
