<?php

use App\Models\Student;
use App\Models\Status;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$uniqueStatusIds = Student::distinct()->pluck('status_id')->toArray();
echo "Unique student status IDs in database: " . implode(', ', $uniqueStatusIds) . "\n\n";

foreach ($uniqueStatusIds as $id) {
    if (!$id) {
        echo "ID: NULL (No status)\n";
        continue;
    }
    $status = Status::find($id);
    $name = $status ? $status->status_name : 'Unknown';
    echo "ID: $id | Name: $name | Parent ID: " . ($status ? $status->p_id_sub : 'N/A') . "\n";
}
