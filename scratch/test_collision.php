<?php

use App\Models\User;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::find(19);

echo "=== Testing Without Eager Loading Relationship ===\n";
foreach ($user->rolesRelation as $role) {
    echo "Role ID: {$role->id}\n";
    echo "gettype(\$role->abilities): " . gettype($role->abilities) . "\n";
    echo "is_array(\$role->abilities): " . (is_array($role->abilities) ? 'YES' : 'NO') . "\n";
    echo "getAttribute('abilities') type: " . gettype($role->getAttribute('abilities')) . "\n";
}

echo "\n=== Testing With Eager Loading 'rolesRelation.abilities' Relationship ===\n";
$userWithRelations = User::with('rolesRelation.abilities')->find(19);
foreach ($userWithRelations->rolesRelation as $role) {
    echo "Role ID: {$role->id}\n";
    echo "gettype(\$role->abilities): " . gettype($role->abilities) . "\n";
    echo "is_array(\$role->abilities): " . (is_array($role->abilities) ? 'YES' : 'NO') . "\n";
    echo "Class of Collection: " . (is_object($role->abilities) ? get_class($role->abilities) : 'N/A') . "\n";
    echo "getAttribute('abilities') type: " . gettype($role->getAttribute('abilities')) . "\n";
}
