<?php

use App\Livewire\OrgApp\Reports\EducationDirectorDashboard;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$dashboard = new EducationDirectorDashboard();
$dashboard->dateFrom = '2026-06-01';
$dashboard->dateTo = '2026-06-11';
$dashboard->selectedGroupId = '';

$metrics = $dashboard->getMetricsProperty();

print_r($metrics);
