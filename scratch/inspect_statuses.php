<?php

use App\Models\Status;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$statuses = Status::where('status_name', 'like', '%تحويل%')
    ->orWhere('status_name', 'like', '%حول%')
    ->orWhere('status_name', 'like', '%جهة%')
    ->get();

echo "Matching Statuses:\n";
foreach ($statuses as $s) {
    echo "ID: {$s->id} | Name: {$s->status_name} | Parent: {$s->p_id_sub}\n";
}
