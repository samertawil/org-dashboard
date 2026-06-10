<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\EducationalActivityName;
use App\Models\ActivitySchedule;

echo "--- CHECKING MERGED IDs ---" . PHP_EOL;
$ids = [222, 223, 227, 228];
foreach ($ids as $id) {
    $r = EducationalActivityName::find($id);
    if ($r) {
        $count = ActivitySchedule::where('activity_name', $id)->count();
        echo "ID: {$id} | Name: [{$r->activity_name}] | Schedules: {$count}" . PHP_EOL;
    } else {
        echo "ID: {$id} — DELETED (Merged successfully)" . PHP_EOL;
    }
}

echo PHP_EOL . "--- CHECKING FOR ANY OTHER SEMANTIC DUPLICATES ---" . PHP_EOL;
$activities = EducationalActivityName::all();
$groups = [];
foreach ($activities as $r) {
    $core = EducationalActivityName::extractCoreName($r->activity_name);
    $groups[$core][] = $r;
}

$duplicatesFound = false;
foreach ($groups as $core => $items) {
    if (count($items) > 1) {
        $duplicatesFound = true;
        echo "Core Group: [{$core}]" . PHP_EOL;
        foreach ($items as $item) {
            echo "  - ID: {$item->id} | Domain: {$item->activity_domain} | Name: [{$item->activity_name}]" . PHP_EOL;
        }
        echo PHP_EOL;
    }
}

if (!$duplicatesFound) {
    echo "Excellent! No duplicates found under the new core matching algorithm." . PHP_EOL;
}
