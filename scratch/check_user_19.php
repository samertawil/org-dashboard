<?php

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::find(19);

if (!$user) {
    echo "User 19 not found!\n";
    exit;
}

echo "=== Student Group Assignments for User 19 ===\n";
$assignedGroups = DB::table('teacher_student_group')
    ->where('teacher_id', 19)
    ->get();

echo "Assigned Group Count: " . $assignedGroups->count() . "\n";
foreach ($assignedGroups as $assignment) {
    echo "- Teacher ID: {$assignment->teacher_id}, Student Group ID: {$assignment->student_group_id}\n";
}

echo "\n=== Visible Students Count ===\n";
$visibleStudentsCount = Student::query()->visibleToTeacher($user)->count();
$totalStudentsCount = Student::query()->count();

echo "Total Students in system: " . $totalStudentsCount . "\n";
echo "Students visible to User 19: " . $visibleStudentsCount . "\n";
