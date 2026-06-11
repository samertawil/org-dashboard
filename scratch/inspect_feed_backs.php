<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$columns = Schema::getColumnListing('feed_backs');
echo "Columns of feed_backs table:\n";
print_r($columns);

$count = DB::table('feed_backs')->count();
echo "\nTotal rows in feed_backs table: $count\n";

if ($count > 0) {
    $samples = DB::table('feed_backs')->take(5)->get();
    print_r($samples);
}
