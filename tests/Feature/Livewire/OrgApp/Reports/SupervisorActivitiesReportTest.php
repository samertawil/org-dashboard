<?php

use App\Models\User;
use App\Models\StudentGroup;
use App\Models\TeacherStudentGroup;
use App\Models\ActivitySchedule;
use App\Models\EducationalActivityDetail;
use App\Models\EducationalActivityName;
use Livewire\Livewire;
use App\Livewire\OrgApp\Reports\SupervisorActivitiesReport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Carbon;

beforeEach(function () {
    // Pin time to prevent day/night variance
    Carbon::setTestNow('2026-06-08 10:00:00');

    // Define standard gates
    Gate::define('reports.all', fn() => false);
    Gate::define('reports.groups.attendance', fn() => false);

    // Seed supervisor and teacher statuses to satisfy foreign keys
    \App\Models\Status::forceCreate([
        'id' => 166,
        'status_name' => 'Teacher',
        'p_id_sub' => 165,
    ]);

    \App\Models\Status::forceCreate([
        'id' => 167,
        'status_name' => 'Supervisor',
        'p_id_sub' => 165,
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

it('aborts with 403 for unauthorized users', function () {
    $regularUser = User::factory()->create(['id' => 999]);
    $this->actingAs($regularUser);

    Livewire::test(SupervisorActivitiesReport::class)
        ->assertStatus(403);
});

it('allows supervisor to view reports for their groups', function () {
    $supervisorUser = User::factory()->create(['id' => 777]);
    $group = StudentGroup::create([
        'name' => 'Supervisor Group',
        'batch_no' => 'B1',
        'activation' => 1,
    ]);

    // Assign user to group as Supervisor (167)
    TeacherStudentGroup::create([
        'teacher_id' => $supervisorUser->id,
        'student_group_id' => $group->id,
        'job_title' => 167,
    ]);

    $this->actingAs($supervisorUser);

    Livewire::test(SupervisorActivitiesReport::class)
        ->assertStatus(200);
});

it('allows super admin to view reports and lists supervisors', function () {
    // Super admin user has ID = 1
    $adminUser = User::factory()->create(['id' => 1]);
    $this->actingAs($adminUser);

    Livewire::test(SupervisorActivitiesReport::class)
        ->assertStatus(200)
        ->assertViewHas('supervisors');
});

it('filters reports based on batch, group, activity name, and dates', function () {
    $adminUser = User::factory()->create(['id' => 1]);
    $this->actingAs($adminUser);

    // Create groups
    $groupA = StudentGroup::create([
        'name' => 'Group Alpha',
        'batch_no' => 'B-Alpha',
        'activation' => 1,
    ]);

    $groupB = StudentGroup::create([
        'name' => 'Group Beta',
        'batch_no' => 'B-Beta',
        'activation' => 1,
    ]);

    // Create educational activity names
    $activityNameA = EducationalActivityName::create([
        'activity_name' => 'First Aid',
        'activation' => 1,
    ]);

    $activityNameB = EducationalActivityName::create([
        'activity_name' => 'Reading Skill',
        'activation' => 1,
    ]);

    // Create schedules
    $scheduleA = ActivitySchedule::create([
        'group_id' => $groupA->id,
        'activity_name' => (string) $activityNameA->id,
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'activation' => 1,
    ]);

    EducationalActivityDetail::create([
        'educational_activity_id' => $scheduleA->id,
        'consistent' => 4,
        'what_learned' => 'CPR Basics',
        'teacher_report_detail' => 'Good session',
    ]);

    $scheduleB = ActivitySchedule::create([
        'group_id' => $groupB->id,
        'activity_name' => (string) $activityNameB->id,
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'activation' => 1,
    ]);

    EducationalActivityDetail::create([
        'educational_activity_id' => $scheduleB->id,
        'consistent' => 5,
        'what_learned' => 'Silent reading practice',
        'teacher_report_detail' => 'Quiet class',
    ]);

    // Test default lists
    $component = Livewire::test(SupervisorActivitiesReport::class);
    $component->assertViewHas('batches', function ($batches) {
        return $batches->contains('B-Alpha') && $batches->contains('B-Beta');
    });

    // Filter by Batch
    $component->set('selectedBatch', 'B-Alpha')
        ->assertViewHas('activities', function ($activities) {
            return count($activities) === 1 && $activities[0]['activity_name'] === 'First Aid';
        });

    // Reset batch and filter by Activity Name
    $component->set('selectedBatch', '')
        ->set('selectedActivityName', $activityNameB->id)
        ->assertViewHas('activities', function ($activities) {
            return count($activities) === 1 && $activities[0]['activity_name'] === 'Reading Skill';
        });
});

it('openCreateReport stores draft in session and redirects to reports.create', function () {
    $adminUser = User::factory()->create(['id' => 1]);
    $this->actingAs($adminUser);

    $group = StudentGroup::create([
        'name'       => 'Test Group Draft',
        'batch_no'   => 'B-Draft',
        'activation' => 1,
    ]);

    $activityName = EducationalActivityName::create([
        'activity_name' => 'Draft Activity',
        'activation'    => 1,
    ]);

    $schedule = ActivitySchedule::create([
        'group_id'      => $group->id,
        'activity_name' => (string) $activityName->id,
        'period_start'  => now(),
        'period_end'    => now()->addHour(),
        'activation'    => 1,
    ]);

    EducationalActivityDetail::create([
        'educational_activity_id' => $schedule->id,
        'consistent'              => 3,
        'what_learned'            => 'Draft what learned',
        'teacher_report_detail'   => 'Draft teacher notes',
    ]);

    $compoundKey = $group->id . '_' . $activityName->id;

    Livewire::test(SupervisorActivitiesReport::class)
        ->set('selectedActivities', [$compoundKey])
        ->call('openCreateReport')
        ->assertRedirect(route('reports.create'));

    // Verify session contains the draft
    expect(session()->has('report_draft'))->toBeTrue();
    $draft = session('report_draft');
    expect($draft['source'])->toBe('supervisor_activities');
    expect($draft['items'])->toHaveCount(1);
    expect($draft['items'][0]['title'])->toContain('Draft Activity');
    expect($draft['items'][0]['content'])->toContain('عدد الحضور للنشاط هو 0');
    expect($draft['items'][0]['content'])->toContain('عدد الاطفال المنسجمين بالنشاط هو 3');
});

it('marks activities as reported when a report has been submitted previously', function () {
    $adminUser = User::factory()->create(['id' => 1]);
    $this->actingAs($adminUser);

    $group = StudentGroup::create([
        'name'       => 'Test Group Reported',
        'batch_no'   => 'B-Reported',
        'activation' => 1,
    ]);

    $activityName = EducationalActivityName::create([
        'activity_name' => 'Reported Activity',
        'activation'    => 1,
    ]);

    $schedule = ActivitySchedule::create([
        'group_id'      => $group->id,
        'activity_name' => (string) $activityName->id,
        'period_start'  => now(),
        'period_end'    => now()->addHour(),
        'activation'    => 1,
    ]);

    EducationalActivityDetail::create([
        'educational_activity_id' => $schedule->id,
        'consistent'              => 3,
        'what_learned'            => 'Reported what learned',
        'teacher_report_detail'   => 'Reported teacher notes',
    ]);

    // Initially it should not be reported
    $component = Livewire::test(SupervisorActivitiesReport::class);
    $component->assertViewHas('activities', function ($activities) {
        return count($activities) === 1 && $activities[0]['is_reported'] === false;
    });

    // Create a valid employee first to satisfy foreign keys
    $employee = \App\Models\Employee::create([
        'user_id'         => $adminUser->id,
        'full_name'       => 'Admin Employee',
        'employee_number' => 'ADM101',
        'date_of_birth'   => '1990-01-01',
        'phone'           => '0599000109',
        'email'           => $adminUser->email,
        'activation'      => 1,
        'gender'          => 2,
    ]);

    // Create a report containing the schedule ID directly in DB
    DB::table('reports')->insert([
        'report_name' => 'Test Report',
        'report_period_type' => 166,
        'report_main_type' => 166,
        'report_date' => now(),
        'date_from' => now()->subDays(5),
        'date_to' => now(),
        'employee_id' => $employee->id,
        'addressed_to_dept_types' => json_encode([]),
        'addressed_to_employees' => $employee->id,
        'covered_educational_activity_schedules_ids' => json_encode([$schedule->id]),
    ]);

    // Now it should show as reported
    $component = Livewire::test(SupervisorActivitiesReport::class);
    $component->assertViewHas('activities', function ($activities) {
        return count($activities) === 1 && $activities[0]['is_reported'] === true;
    });
});

it('filters activities based on report status (reported / unreported)', function () {
    $adminUser = User::factory()->create(['id' => 1]);
    $this->actingAs($adminUser);

    $group = StudentGroup::create([
        'name'       => 'Test Group Reported Filter',
        'batch_no'   => 'B-Filter',
        'activation' => 1,
    ]);

    $activityName1 = EducationalActivityName::create([
        'activity_name' => 'First Activity Filter',
        'activation'    => 1,
    ]);

    $activityName2 = EducationalActivityName::create([
        'activity_name' => 'Second Activity Filter',
        'activation'    => 1,
    ]);

    $schedule1 = ActivitySchedule::create([
        'group_id'      => $group->id,
        'activity_name' => (string) $activityName1->id,
        'period_start'  => now(),
        'period_end'    => now()->addHour(),
        'activation'    => 1,
    ]);

    $schedule2 = ActivitySchedule::create([
        'group_id'      => $group->id,
        'activity_name' => (string) $activityName2->id,
        'period_start'  => now(),
        'period_end'    => now()->addHour(),
        'activation'    => 1,
    ]);

    EducationalActivityDetail::create([
        'educational_activity_id' => $schedule1->id,
        'consistent'              => 3,
        'what_learned'            => 'First what learned',
        'teacher_report_detail'   => 'First notes',
    ]);

    EducationalActivityDetail::create([
        'educational_activity_id' => $schedule2->id,
        'consistent'              => 3,
        'what_learned'            => 'Second what learned',
        'teacher_report_detail'   => 'Second notes',
    ]);

    $employee = \App\Models\Employee::create([
        'user_id'         => $adminUser->id,
        'full_name'       => 'Admin Employee Filter',
        'employee_number' => 'ADM102',
        'date_of_birth'   => '1990-01-01',
        'phone'           => '0599000110',
        'email'           => $adminUser->email,
        'activation'      => 1,
        'gender'          => 2,
    ]);

    // Create a report containing only schedule1
    DB::table('reports')->insert([
        'report_name' => 'Test Filter Report',
        'report_period_type' => 166,
        'report_main_type' => 166,
        'report_date' => now(),
        'date_from' => now()->subDays(5),
        'date_to' => now(),
        'employee_id' => $employee->id,
        'addressed_to_dept_types' => json_encode([]),
        'addressed_to_employees' => $employee->id,
        'covered_educational_activity_schedules_ids' => json_encode([$schedule1->id]),
    ]);

    $component = Livewire::test(SupervisorActivitiesReport::class);

    // With status = empty, we see both activities
    $component->assertViewHas('activities', function ($activities) {
        return count($activities) === 2;
    });

    // Filter by reported
    $component->set('selectedReportStatus', 'reported')
        ->assertViewHas('activities', function ($activities) {
            return count($activities) === 1 && $activities[0]['activity_name'] === 'First Activity Filter';
        });

    // Filter by unreported
    $component->set('selectedReportStatus', 'unreported')
        ->assertViewHas('activities', function ($activities) {
            return count($activities) === 1 && $activities[0]['activity_name'] === 'Second Activity Filter';
        });
});

it('can clear all filters', function () {
    $adminUser = User::factory()->create(['id' => 1]);
    $this->actingAs($adminUser);

    Livewire::test(SupervisorActivitiesReport::class)
        ->set('selectedBatch', 'B-1')
        ->set('selectedGroup', '12')
        ->set('selectedActivityName', 'Reading')
        ->set('selectedReportStatus', 'reported')
        ->set('selectedSupervisorId', '15')
        ->set('selectedActivities', ['1_2'])
        ->call('clearFilters')
        ->assertSet('selectedBatch', '')
        ->assertSet('selectedGroup', '')
        ->assertSet('selectedActivityName', '')
        ->assertSet('selectedReportStatus', '')
        ->assertSet('selectedSupervisorId', '')
        ->assertSet('selectedActivities', []);
});




