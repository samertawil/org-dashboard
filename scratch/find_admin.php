<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$users = \App\Models\User::all();
foreach ($users as $user) {
    if ($user->isSuperAdmin()) {
        echo "Super Admin: ID={$user->id}, Email={$user->email}, Name={$user->name}\n";
    }
}
