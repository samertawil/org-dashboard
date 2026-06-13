<?php

use App\Models\User;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\ActivitySchedule;
use App\Models\EducationalActivityDetail;
use App\Models\StudentDailyAttendance;
use App\Models\Status;
use App\Models\EducationalActivityName;
use App\Models\TeacherStudentGroup;
use App\Models\Employee;
use Livewire\Livewire;
use App\Livewire\OrgApp\Reports\EducationDirectorDashboard;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Carbon;

beforeEach(function () {
    Carbon::setTestNow('2026-06-08 10:00:00');
    \Illuminate\Support\Facades\Cache::flush();

    // Define standard gates
    Gate::define('manager.reports.all', fn() => false);
    Gate::define('reports.all', fn() => false);

    // Seed educational activity domain statuses
    Status::forceCreate([
        'id' => 187,
        'status_name' => 'التعليم',
        'p_id_sub' => 185,
    ]);
    Status::forceCreate([
        'id' => 188,
        'status_name' => 'الدعم النفسي',
        'p_id_sub' => 185,
    ]);
    Status::forceCreate([
        'id' => 190,
        'status_name' => 'مهارات وقيم تربوية',
        'p_id_sub' => 185,
    ]);

    // Seed period groups status
    Status::forceCreate([
        'id' => 200,
        'status_name' => 'الفترة الأولى',
        'p_id_sub' => 124,
    ]);

    // Seed supervisor and teacher statuses
    Status::forceCreate([
        'id' => 167,
        'status_name' => 'Supervisor',
        'p_id_sub' => 165,
    ]);
    Status::forceCreate([
        'id' => 166,
        'status_name' => 'Teacher',
        'p_id_sub' => 165,
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

it('aborts with 403 for unauthorized users who are not supervisors or admins', function () {
    $regularUser = User::factory()->create(['id' => 999]);
    $this->actingAs($regularUser);

    Livewire::test(EducationDirectorDashboard::class)
        ->assertStatus(403);
});

it('allows supervisors to access the dashboard', function () {
    $supervisorUser = User::factory()->create(['id' => 2]);
    
    // Assign user as supervisor (167) to a group
    $group = StudentGroup::create([
        'name' => 'Group A',
        'batch_no' => 'B1',
        'activation' => 1,
    ]);

    TeacherStudentGroup::create([
        'teacher_id' => $supervisorUser->id,
        'student_group_id' => $group->id,
        'job_title' => 167,
    ]);

    $this->actingAs($supervisorUser);

    Livewire::test(EducationDirectorDashboard::class)
        ->assertStatus(200)
        ->assertViewHas('metrics');
});

it('calculates metrics only for groups supervised by the supervisor', function () {
    // 1. Create Supervisor User & Employee
    $supervisorUser = User::factory()->create(['id' => 2]);
    $employee = Employee::create([
        'user_id' => $supervisorUser->id,
        'employee_number' => 'EMP167',
        'full_name' => 'Test Supervisor',
        'gender' => 2,
        'activation' => 1,
    ]);

    // 2. Create two groups: Group A (supervised) and Group B (not supervised)
    $groupA = StudentGroup::create([
        'name' => 'Group A',
        'batch_no' => 'B1',
        'activation' => 1,
    ]);

    $groupB = StudentGroup::create([
        'name' => 'Group B',
        'batch_no' => 'B1',
        'activation' => 1,
    ]);

    // Supervisor links to Group A
    TeacherStudentGroup::create([
        'teacher_id' => $supervisorUser->id,
        'student_group_id' => $groupA->id,
        'job_title' => 167,
    ]);

    // 3. Create students
    $studentA = Student::create([
        'identity_number' => 111111111,
        'full_name' => 'Student A',
        'birth_date' => '2018-01-01',
        'student_groups_id' => $groupA->id,
        'activation' => 1,
        'gender' => 1,
        'status_id' => 200,
    ]);

    $studentB = Student::create([
        'identity_number' => 222222222,
        'full_name' => 'Student B',
        'birth_date' => '2018-01-01',
        'student_groups_id' => $groupB->id,
        'activation' => 1,
        'gender' => 1,
        'status_id' => 200,
    ]);

    // 4. Create executed schedules
    $actName = EducationalActivityName::create([
        'activity_name' => 'Test Activity Name',
        'activation' => 1,
    ]);

    // Schedule for A (supervised)
    $scheduleA = ActivitySchedule::create([
        'group_id' => $groupA->id,
        'activity_name' => $actName->id,
        'educational_activity_domain' => 187,
        'target_category' => 'children',
        'period_start' => '2026-06-08 09:00:00',
        'period_end' => '2026-06-08 10:00:00',
        'educational_period_groups' => 200,
        'activation' => 1,
    ]);
    EducationalActivityDetail::create([
        'educational_activity_id' => $scheduleA->id,
        'consistent' => 1,
        'what_learned' => 'Nothing',
        'teacher_report_detail' => 'Done',
    ]);
    StudentDailyAttendance::create([
        'student_id' => $studentA->id,
        'student_group_id' => $groupA->id,
        'attendance_date' => '2026-06-08',
        'status' => 'present',
    ]);

    // Schedule for B (unsupervised)
    $scheduleB = ActivitySchedule::create([
        'group_id' => $groupB->id,
        'activity_name' => $actName->id,
        'educational_activity_domain' => 187,
        'target_category' => 'children',
        'period_start' => '2026-06-08 09:00:00',
        'period_end' => '2026-06-08 10:00:00',
        'educational_period_groups' => 200,
        'activation' => 1,
    ]);
    EducationalActivityDetail::create([
        'educational_activity_id' => $scheduleB->id,
        'consistent' => 1,
        'what_learned' => 'Nothing',
        'teacher_report_detail' => 'Done',
    ]);
    StudentDailyAttendance::create([
        'student_id' => $studentB->id,
        'student_group_id' => $groupB->id,
        'attendance_date' => '2026-06-08',
        'status' => 'present',
    ]);

    $this->actingAs($supervisorUser);

    // Test that EducationDirectorDashboard only calculates metrics for supervised Group A
    Livewire::test(EducationDirectorDashboard::class)
        ->set('dateFrom', '2026-06-01')
        ->set('dateTo', '2026-06-15')
        ->assertViewHas('metrics', function ($metrics) {
            return $metrics['total_executed'] === 1 &&
                   $metrics['total_attendance'] === 1;
        });
});
