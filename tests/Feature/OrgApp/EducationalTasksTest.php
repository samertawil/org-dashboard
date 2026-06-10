<?php

use App\Livewire\OrgApp\EducationalActivitySchedules\EducationalTasks;
use App\Models\Employee;
use App\Models\ActivitySchedule;
use App\Models\EducationalActivityDetail;
use App\Models\StudentGroup;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Setup required gates/permissions
    Gate::define('educational-activity-detail.index', fn() => true);
    Gate::define('educational-activity-detail.create', fn() => true);

    // Set fixed test now to avoid day/night time dependency in status checks
    \Illuminate\Support\Carbon::setTestNow('2026-06-08 10:00:00');

    // Seed statuses
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

    // Create a regular user and employee
    $this->user = User::factory()->create([
        'id' => 999,
        'activation' => 1,
    ]);
    $this->employee = Employee::create([
        'user_id' => $this->user->id,
        'full_name' => 'Regular Employee',
        'employee_number' => 'EMP101',
        'date_of_birth' => '1990-01-01',
        'phone' => '0599000101',
        'email' => $this->user->email,
        'activation' => 1,
        'gender' => 2,
    ]);

    // Create another user and employee for comparison
    $this->otherUser = User::factory()->create([
        'id' => 888,
        'activation' => 1,
    ]);
    $this->otherEmployee = Employee::create([
        'user_id' => $this->otherUser->id,
        'full_name' => 'Other Employee',
        'employee_number' => 'EMP102',
        'date_of_birth' => '1990-01-01',
        'phone' => '0599000102',
        'email' => $this->otherUser->email,
        'activation' => 1,
        'gender' => 2,
    ]);

    // Create an administrator user (ID = 1)
    $this->adminUser = User::factory()->create([
        'id' => 1,
        'activation' => 1,
    ]);
});

afterEach(function () {
    \Illuminate\Support\Carbon::setTestNow();
});

it('classifies task status correctly', function () {
    actingAs($this->user);

    // 1. Completed Task
    $completedSchedule = ActivitySchedule::create([
        'activity_name' => 'Completed Task',
        'target_category' => 'children',
        'period_start' => now()->subDay(),
        'period_end' => now()->subDay()->addHour(),
        'employee_id' => $this->employee->id,
        'activation' => 1,
    ]);
    EducationalActivityDetail::create([
        'educational_activity_id' => $completedSchedule->id,
        'consistent' => 10,
        'what_learned' => 'Learned mathematics',
    ]);

    // 2. Delayed Task (Scheduled for yesterday and not completed)
    $delayedSchedule = ActivitySchedule::create([
        'activity_name' => 'Delayed Task',
        'target_category' => 'children',
        'period_start' => now()->subDay(),
        'period_end' => now()->subDay()->addHour(),
        'employee_id' => $this->employee->id,
        'activation' => 1,
    ]);

    // 3. Require Today Task (Scheduled for today, but in the future)
    $requireTodaySchedule = ActivitySchedule::create([
        'activity_name' => 'Require Today Task',
        'target_category' => 'children',
        'period_start' => now()->addHour(),
        'period_end' => now()->addHours(2),
        'employee_id' => $this->employee->id,
        'activation' => 1,
    ]);

    // 4. Upcoming Task (Scheduled for a future day)
    $upcomingSchedule = ActivitySchedule::create([
        'activity_name' => 'Upcoming Task',
        'target_category' => 'children',
        'period_start' => now()->addDays(2),
        'period_end' => now()->addDays(2)->addHour(),
        'employee_id' => $this->employee->id,
        'activation' => 1,
    ]);

    // 5. Happen Now Task (Current time is within the scheduled period)
    $happenNowSchedule = ActivitySchedule::create([
        'activity_name' => 'Happen Now Task',
        'target_category' => 'children',
        'period_start' => now()->subMinutes(15),
        'period_end' => now()->addMinutes(15),
        'employee_id' => $this->employee->id,
        'activation' => 1,
    ]);

    expect($completedSchedule->fresh()->task_status)->toBe('completed');
    expect($delayedSchedule->fresh()->task_status)->toBe('delayed');
    expect($requireTodaySchedule->fresh()->task_status)->toBe('require_today');
    expect($upcomingSchedule->fresh()->task_status)->toBe('upcoming');
    expect($happenNowSchedule->fresh()->task_status)->toBe('happen_now');
});

it('scopes tasks for regular employee to only their own in assigned groups (Job Title 166)', function () {
    // Create group
    $group = StudentGroup::create([
        'name' => 'Test Group',
        'max_students' => 10,
        'batch_no' => 'B01',
        'activation' => 1,
    ]);

    // Assign employee to group as teacher (Job Title 166)
    \App\Models\TeacherStudentGroup::create([
        'teacher_id' => $this->user->id,
        'student_group_id' => $group->id,
        'job_title' => 166,
    ]);

    // Task for current employee in this group
    ActivitySchedule::create([
        'activity_name' => 'My Task 1',
        'target_category' => 'children',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $group->id,
        'activation' => 1,
    ]);

    // Task for other employee in this same group
    ActivitySchedule::create([
        'activity_name' => 'Other Task 1',
        'target_category' => 'children',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $group->id,
        'activation' => 1,
    ]);

    actingAs($this->user);

    Livewire::test(EducationalTasks::class)
        ->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 1 && ($tasks->first()->activityNameStatus?->activity_name ?? $tasks->first()->activity_name) === 'My Task 1';
        });
});

it('allows coordinators (Job Title 167) to see all tasks in their group', function () {
    // Create group
    $group = StudentGroup::create([
        'name' => 'Test Group 2',
        'max_students' => 10,
        'batch_no' => 'B02',
        'activation' => 1,
    ]);

    // Assign employee to group as coordinator (Job Title 167)
    \App\Models\TeacherStudentGroup::create([
        'teacher_id' => $this->user->id,
        'student_group_id' => $group->id,
        'job_title' => 167,
    ]);

    // Task for current employee in this group
    ActivitySchedule::create([
        'activity_name' => 'My Task 1',
        'target_category' => 'children',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $group->id,
        'activation' => 1,
    ]);

    // Task for other employee in this group
    ActivitySchedule::create([
        'activity_name' => 'Other Task 1',
        'target_category' => 'children',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $group->id,
        'activation' => 1,
    ]);

    actingAs($this->user);

    // Should see both tasks because of 167 permission on group
    Livewire::test(EducationalTasks::class)
        ->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 2;
        });
});

it('allows manager/admin to see all tasks and filter by employee', function () {
    // Task for employee 1
    ActivitySchedule::create([
        'activity_name' => 'Employee 1 Task',
        'target_category' => 'children',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'activation' => 1,
    ]);

    // Task for employee 2
    ActivitySchedule::create([
        'activity_name' => 'Employee 2 Task',
        'target_category' => 'children',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'activation' => 1,
    ]);

    actingAs($this->adminUser);

    // Should see both tasks initially
    Livewire::test(EducationalTasks::class)
        ->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 2;
        })
        // Set filter to employee 1 only
        ->set('filterEmployee', (string) $this->employee->id)
        ->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 1 && ($tasks->first()->activityNameStatus?->activity_name ?? $tasks->first()->activity_name) === 'Employee 1 Task';
        });
});

it('can filter tasks by search query, date, and status', function () {
    actingAs($this->adminUser);

    // 1. Completed Task
    $completed = ActivitySchedule::create([
        'activity_name' => 'Math Class',
        'target_category' => 'children',
        'period_start' => now()->subDay(),
        'period_end' => now()->subDay()->addHour(),
        'employee_id' => $this->employee->id,
        'activation' => 1,
    ]);
    EducationalActivityDetail::create([
        'educational_activity_id' => $completed->id,
        'consistent' => 5,
        'what_learned' => 'Learned algebra',
    ]);

    // 2. Delayed Task
    $delayed = ActivitySchedule::create([
        'activity_name' => 'Science Lab',
        'target_category' => 'children',
        'period_start' => now()->subHour(),
        'period_end' => now()->subHour()->addMinutes(30),
        'employee_id' => $this->employee->id,
        'activation' => 1,
    ]);

    // 3. Task on a specific date (future)
    $specificDate = now()->addDays(5);
    ActivitySchedule::create([
        'activity_name' => 'History Lecture',
        'target_category' => 'children',
        'period_start' => $specificDate,
        'period_end' => $specificDate->copy()->addHour(),
        'employee_id' => $this->employee->id,
        'activation' => 1,
    ]);

    // Test Search Filter
    Livewire::test(EducationalTasks::class)
        ->set('search', (string) $delayed->activity_name)
        ->assertViewHas('tasks', function ($tasks) use ($delayed) {
            return $tasks->count() === 1 && $tasks->first()->id === $delayed->id;
        });

    // Test Status Filter (Completed)
    Livewire::test(EducationalTasks::class)
        ->set('filterStatus', 'completed')
        ->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 1 && ($tasks->first()->activityNameStatus?->activity_name ?? $tasks->first()->activity_name) === 'Math Class';
        });

    // Test Date Filter
    Livewire::test(EducationalTasks::class)
        ->set('filterDate', $specificDate->toDateString())
        ->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 1 && ($tasks->first()->activityNameStatus?->activity_name ?? $tasks->first()->activity_name) === 'History Lecture';
        });
});

it('can filter tasks by student group', function () {
    actingAs($this->adminUser);

    // Create student groups
    $groupA = StudentGroup::create([
        'name' => 'Group Alpha',
        'max_students' => 15,
        'batch_no' => 'B01',
        'activation' => 1,
    ]);

    $groupB = StudentGroup::create([
        'name' => 'Group Beta',
        'max_students' => 15,
        'batch_no' => 'B02',
        'activation' => 1,
    ]);

    // Create schedule for Group A
    ActivitySchedule::create([
        'activity_name' => 'Alpha Schedule',
        'target_category' => 'children',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $groupA->id,
        'activation' => 1,
    ]);

    // Create schedule for Group B
    ActivitySchedule::create([
        'activity_name' => 'Beta Schedule',
        'target_category' => 'children',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $groupB->id,
        'activation' => 1,
    ]);

    // Test Group Filter
    Livewire::test(EducationalTasks::class)
        ->set('filterGroup', (string) $groupA->id)
        ->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 1 && ($tasks->first()->activityNameStatus?->activity_name ?? $tasks->first()->activity_name) === 'Alpha Schedule';
        });
});
