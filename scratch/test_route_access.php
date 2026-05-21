<?php

use App\Models\User;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::find(19);

if (!$user) {
    echo "User 19 not found!\n";
    exit;
}

echo "=== Simulating Request to /dashboard/student as User 19 ===\n";
$request = \Illuminate\Http\Request::create('/dashboard/student', 'GET');

// Boot session
$session = $app['session']->driver();
$request->setLaravelSession($session);

// Authenticate user
auth()->login($user);

$response = $app->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";

if (isset($response->exception)) {
    $e = $response->exception;
    echo "Exception Class: " . get_class($e) . "\n";
    echo "Exception Message: " . $e->getMessage() . "\n";
    echo "Exception Stack Trace (Top 30 lines):\n";
    $lines = explode("\n", $e->getTraceAsString());
    for ($i = 0; $i < min(30, count($lines)); $i++) {
        echo $lines[$i] . "\n";
    }
} else {
    echo "No exception attached to response.\n";
}
