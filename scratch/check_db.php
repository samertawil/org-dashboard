<?php

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== All Roles in System ===\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "ID: {$role->id} | Name: {$role->name} | Abilities: " . json_encode($role->abilities) . "\n";
}

echo "\n=== role_user Table Contents ===\n";
$roleUsers = DB::table('role_user')->get();
foreach ($roleUsers as $ru) {
    echo "User ID: {$ru->user_id} | Role ID: {$ru->role_id}\n";
}

echo "\n=== User ID 19 Details ===\n";
$user19 = User::find(19);
if ($user19) {
    echo "Name: {$user19->name} | Email: {$user19->email} | Activation: {$user19->activation}\n";
} else {
    echo "User ID 19 not found!\n";
}
