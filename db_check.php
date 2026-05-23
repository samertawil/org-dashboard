<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$statuses = App\Models\Status::whereBetween('id', [120, 150])->get();
foreach ($statuses as $status) {
    echo "Status ID: " . $status->id . " | Name: " . $status->status_name . " | Description: " . $status->description . " | p_id_sub: " . $status->p_id_sub . PHP_EOL;
}
