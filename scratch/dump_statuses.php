<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Status::where('p_id_sub', 192)->get() as $s) {
    echo 'Child of 192: ' . $s->id . ': ' . $s->status_name . PHP_EOL;
}
foreach (\App\Models\Status::where('status_name', 'like', '%تقرير%')->get() as $s) {
    echo 'Contains تقرير: ' . $s->id . ': ' . $s->status_name . ' (p_id_sub: ' . $s->p_id_sub . ')' . PHP_EOL;
}
foreach (\App\Models\Status::where('status_name', 'like', '%report%')->get() as $s) {
    echo 'Contains report: ' . $s->id . ': ' . $s->status_name . ' (p_id_sub: ' . $s->p_id_sub . ')' . PHP_EOL;
}

