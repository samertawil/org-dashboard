<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$results = \App\Models\Status::select('id', 'status_name', 'p_id_sub')
    ->where('status_name', 'like', '%طالب%')
    ->orWhere('status_name', 'like', '%ولي%')
    ->orWhere('status_name', 'like', '%عامة%')
    ->get();

foreach ($results as $res) {
    echo "ID: {$res->id}, Name: {$res->status_name}, p_id_sub: {$res->p_id_sub}\n";
}
