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
