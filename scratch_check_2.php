<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Ability;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('id', 2)->with('rolesRelation')->first();
echo "User: " . $user->name . "\n";
foreach ($user->rolesRelation as $role) {
    echo "Role Name: " . $role->name . "\n";
    echo "Type of \$role->abilities: " . gettype($role->abilities) . "\n";
    if (is_object($role->abilities)) {
        echo "Class of \$role->abilities: " . get_class($role->abilities) . "\n";
    } else {
        echo "Value: " . print_r($role->abilities, true) . "\n";
    }
}
