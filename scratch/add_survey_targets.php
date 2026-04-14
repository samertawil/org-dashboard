<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$target = \App\Models\Status::create(['status_name' => 'Survey Target', 'p_id' => 0]); 
\App\Models\Status::create(['status_name' => 'Student', 'p_id' => $target->id, 'p_id_sub' => $target->id]);
\App\Models\Status::create(['status_name' => 'Parent', 'p_id' => $target->id, 'p_id_sub' => $target->id]);
\App\Models\Status::create(['status_name' => 'General Public', 'p_id' => $target->id, 'p_id_sub' => $target->id]);

echo "Target ID Created: " . $target->id . "\n";
