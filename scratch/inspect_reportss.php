<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Check attachments structure
echo "=== ATTACHMENTS STRUCTURE ===\n";
$bodies = DB::table('report_body')->whereNotNull('attachments')->limit(3)->get();
foreach ($bodies as $b) {
    echo "\n-- report_body id: {$b->id} --\n";
    $atts = json_decode($b->attachments, true);
    if (is_array($atts)) {
        foreach ($atts as $i => $att) {
            if (is_string($att)) {
                // nested JSON
                $inner = json_decode($att, true);
                echo "  [$i] (nested JSON): " . json_encode($inner, JSON_UNESCAPED_UNICODE) . "\n";
            } else {
                echo "  [$i]: " . json_encode($att, JSON_UNESCAPED_UNICODE) . "\n";
            }
        }
    } else {
        echo "  RAW: " . mb_substr($b->attachments, 0, 200) . "\n";
    }
}

// Check report with activities info
echo "\n\n=== FULL REPORT JOIN SAMPLE ===\n";
$reports = DB::table('reports')
    ->select('id', 'report_name', 'date_from', 'date_to', 'covered_educational_activity_schedules_ids', 'student_group_ids')
    ->limit(3)
    ->get();

foreach ($reports as $r) {
    $schedIds = json_decode($r->covered_educational_activity_schedules_ids, true);
    $groupIds = json_decode($r->student_group_ids, true);
    $firstSchedId = !empty($schedIds) ? $schedIds[0] : null;
    $firstGroupId = !empty($groupIds) ? $groupIds[0] : null;
    
    echo "\nReport #{$r->id}: {$r->report_name}\n";
    echo "  Groups: " . implode(',', $groupIds ?? []) . "\n";
    echo "  Schedules: " . implode(',', array_slice($schedIds ?? [], 0, 5)) . "\n";
    
    if ($firstGroupId) {
        $group = DB::table('student_groups')->where('id', $firstGroupId)->first();
        echo "  Group Name: " . ($group->name ?? 'N/A') . "\n";
    }
    
    if ($firstSchedId) {
        $sched = DB::table('educational_activity_schedules')
            ->leftJoin('educational_activity_names', 'educational_activity_schedules.activity_name', '=', 'educational_activity_names.id')
            ->leftJoin('statuses', 'educational_activity_schedules.educational_activity_domain', '=', 'statuses.id')
            ->select('educational_activity_names.activity_name', 'statuses.status_name as domain_name')
            ->where('educational_activity_schedules.id', $firstSchedId)
            ->first();
        echo "  Activity: " . ($sched->activity_name ?? 'N/A') . " | Domain: " . ($sched->domain_name ?? 'N/A') . "\n";
    }
}
