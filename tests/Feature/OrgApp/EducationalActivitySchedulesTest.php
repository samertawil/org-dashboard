<?php

use App\Livewire\OrgApp\EducationalActivitySchedules\Index;
use App\Models\User;
use App\Models\StudentGroup;
use App\Models\Employee;
use Livewire\Livewire;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Gate::define('educational-activity-schedules.index', fn() => true);
    
    // Create standard active student groups
    $this->group1 = StudentGroup::create([
        'name' => 'Group A',
        'batch_no' => '3',
        'activation' => 1,
        'start_date' => now()->subDays(5),
        'end_date' => now()->addDays(5),
    ]);

    $this->group2 = StudentGroup::create([
        'name' => 'Group B',
        'batch_no' => '4',
        'activation' => 1,
        'start_date' => now()->subDays(5),
        'end_date' => now()->addDays(5),
    ]);

    $this->adminUser = User::factory()->create(['id' => 1, 'activation' => 1]);
    $this->teacherUser = User::factory()->create(['id' => 200, 'activation' => 1]);

    $this->employee = Employee::create([
        'user_id' => $this->teacherUser->id,
        'full_name' => 'Teacher User',
        'employee_number' => 'EMP200',
        'date_of_birth' => '1990-01-01',
        'phone' => '0599000200',
        'email' => $this->teacherUser->email,
        'activation' => 1,
        'gender' => 2,
    ]);

    // Assign teacher user to Group A
    $this->teacherUser->teacher()->create([
        'student_group_id' => $this->group1->id,
    ]);
});

it('renders the educational activity schedules index page for admin', function () {
    actingAs($this->adminUser);

    $component = Livewire::test(Index::class)
        ->assertStatus(200);

    $groups = $component->instance()->availableGroups;
    expect($groups->contains($this->group1))->toBeTrue();
    expect($groups->contains($this->group2))->toBeTrue();
});

it('renders the educational activity schedules index page for teacher and restricts available groups', function () {
    actingAs($this->teacherUser);

    $component = Livewire::test(Index::class)
        ->assertStatus(200);

    $groups = $component->instance()->availableGroups;
    expect($groups->contains($this->group1))->toBeTrue();
    expect($groups->contains($this->group2))->toBeFalse();
});

it('filters schedules by employee_id for regular teacher (job_title != 167)', function () {
    // Create status to avoid FK constraint violation
    $status150 = new \App\Models\Status();
    $status150->id = 150;
    $status150->status_name = 'Teacher';
    $status150->save();

    // Set teacher's job title in pivot to something other than 167
    $this->teacherUser->teacher()->where('student_group_id', $this->group1->id)->update(['job_title' => 150]);

    // Create another employee
    $otherUser = User::factory()->create(['activation' => 1]);
    $otherEmployee = Employee::create([
        'user_id' => $otherUser->id,
        'full_name' => 'Other Teacher',
        'employee_number' => 'EMP201',
        'date_of_birth' => '1990-01-01',
        'phone' => '0599000201',
        'email' => $otherUser->email,
        'activation' => 1,
        'gender' => 2,
    ]);

    // Create a schedule assigned to this teacher
    $schedule1 = \App\Models\ActivitySchedule::create([
        'activity_name' => 'Teacher Activity',
        'group_id' => $this->group1->id,
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'activation' => 1,
        'created_by' => $this->teacherUser->id,
    ]);

    // Create a schedule assigned to another employee
    $schedule2 = \App\Models\ActivitySchedule::create([
        'activity_name' => 'Other Teacher Activity',
        'group_id' => $this->group1->id,
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $otherEmployee->id, // Valid other employee
        'activation' => 1,
        'created_by' => $this->teacherUser->id,
    ]);

    actingAs($this->teacherUser);

    $component = Livewire::test(Index::class)
        ->set('filterBatch', '3'); // Set batch to match group1

    $schedules = $component->instance()->schedules;
    
    // Regular teacher should only see their own schedule
    expect($schedules->items())->toHaveCount(1);
    expect($schedules->items()[0]->id)->toBe($schedule1->id);
});

it('does not filter schedules by employee_id for teacher with job_title 167', function () {
    // Create status to avoid FK constraint violation
    $status167 = new \App\Models\Status();
    $status167->id = 167;
    $status167->status_name = 'Coordinator';
    $status167->save();

    // Set teacher's job title in pivot to 167
    $this->teacherUser->teacher()->where('student_group_id', $this->group1->id)->update(['job_title' => 167]);

    // Create another employee
    $otherUser = User::factory()->create(['activation' => 1]);
    $otherEmployee = Employee::create([
        'user_id' => $otherUser->id,
        'full_name' => 'Other Teacher',
        'employee_number' => 'EMP201',
        'date_of_birth' => '1990-01-01',
        'phone' => '0599000201',
        'email' => $otherUser->email,
        'activation' => 1,
        'gender' => 2,
    ]);

    // Create a schedule assigned to this teacher
    $schedule1 = \App\Models\ActivitySchedule::create([
        'activity_name' => 'Teacher Activity',
        'group_id' => $this->group1->id,
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'activation' => 1,
        'created_by' => $this->teacherUser->id,
    ]);

    // Create a schedule assigned to another employee
    $schedule2 = \App\Models\ActivitySchedule::create([
        'activity_name' => 'Other Teacher Activity',
        'group_id' => $this->group1->id,
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $otherEmployee->id, // Valid other employee
        'activation' => 1,
        'created_by' => $this->teacherUser->id,
    ]);

    actingAs($this->teacherUser);

    $component = Livewire::test(Index::class)
        ->set('filterBatch', '3'); // Set batch to match group1

    $schedules = $component->instance()->schedules;
    
    // Teacher with job_title 167 should see all schedules of their group
    expect($schedules->items())->toHaveCount(2);
    $ids = collect($schedules->items())->pluck('id')->toArray();
    expect($ids)->toContain($schedule1->id);
    expect($ids)->toContain($schedule2->id);
});

it('renders the educational activity schedule show page and displays the report details when it exists', function () {
    // Create status to avoid FK constraint violation
    $status193 = new \App\Models\Status();
    $status193->id = 193;
    $status193->status_name = 'Done';
    $status193->save();

    // Create a schedule
    $schedule = \App\Models\ActivitySchedule::create([
        'activity_name' => 'Activity to Show',
        'group_id' => $this->group1->id,
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'activation' => 1,
        'created_by' => $this->adminUser->id,
    ]);

    // Create educational activity detail (report)
    $detail = \App\Models\EducationalActivityDetail::create([
        'educational_activity_id' => $schedule->id,
        'consistent' => 'Yes',
        'status_id' => 193,
        'what_learned' => 'They learned spelling',
        'teacher_report_detail' => 'Great details here',
    ]);

    actingAs($this->adminUser);

    Livewire::test(App\Livewire\OrgApp\EducationalActivitySchedules\Show::class, ['schedule' => $schedule])
        ->assertStatus(200)
        ->assertSee('Activity to Show')
        ->assertSee('Yes')
        ->assertSee('They learned spelling')
        ->assertSee('Great details here');
});


