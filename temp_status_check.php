<?php
// temp_status_check.php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$statuses = \App\Reposotries\StatusRepo::statuses()->where('p_id_sub', 47);
foreach($statuses as $s) {
    echo $s->id . ':' . $s->status_name . PHP_EOL;
}
