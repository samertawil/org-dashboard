<?php

use App\Models\User;
use App\Models\Employee;
use App\Models\Report;
use App\Models\ReportBody;
use App\Models\Status;
use App\Models\StudentGroup;
use App\Models\EducationalActivityName;
use Livewire\Livewire;
use App\Livewire\OrgApp\Reports\ShowReport;
use Illuminate\Support\Carbon;

beforeEach(function () {
    Carbon::setTestNow('2026-06-08 10:00:00');

    // Create statuses to satisfy report constraints
    $this->periodType = Status::create(['status_name' => 'Monthly', 'p_id_sub' => 192]);
    $this->mainType = Status::create(['status_name' => 'Educational', 'p_id_sub' => 197]);

    // Create employees to satisfy foreign keys
    $this->empA = Employee::create([
        'employee_number' => 'EMP001',
        'full_name' => 'First Employee',
        'gender' => 2,
        'activation' => 1,
    ]);

    $this->empB = Employee::create([
        'employee_number' => 'EMP002',
        'full_name' => 'Second Employee',
        'gender' => 3,
        'activation' => 1,
    ]);

    // Create extra metadata models
    $this->group = StudentGroup::create([
        'name' => 'Grade 4 Group',
        'activation' => 1,
        'batch_no' => 4,
    ]);

    $this->activity = EducationalActivityName::create([
        'activity_name' => 'General Math Activity',
        'activation' => 1,
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

it('renders the report details page successfully', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $report = Report::create([
        'report_name' => 'Test Show Report Details',
        'report_date' => now(),
        'date_from' => now()->subDays(5),
        'date_to' => now(),
        'employee_id' => $this->empA->id,
        'addressed_to_employees' => $this->empB->id,
        'addressed_to_dept_types' => ['management'],
        'report_period_type' => $this->periodType->id,
        'report_main_type' => $this->mainType->id,
        'student_group_ids' => [$this->group->id],
        'covered_educational_activities_ids' => [$this->activity->id],
        'follow_up_by' => [$this->empA->id, $this->empB->id],
        'note' => 'Some general note for this report.',
    ]);

    $body = ReportBody::create([
        'report_id' => $report->id,
        'item_order' => 1,
        'content' => 'Item one content text',
        'observation' => 'Item observation note',
    ]);

    Livewire::test(ShowReport::class, ['report' => $report])
        ->assertStatus(200)
        ->assertSet('report.id', $report->id)
        ->assertSet('coveredGroups', ['Grade 4 Group'])
        ->assertSet('coveredActivities', ['General Math Activity'])
        ->assertSet('ccEmployees', ['First Employee', 'Second Employee'])
        ->assertSee('Test Show Report Details')
        ->assertSee('Item one content text')
        ->assertSee('Item observation note')
        ->assertSee('Some general note for this report.');
});

it('restricts show report page to creator, recipient, or admin', function () {
    // ID 1 is automatically Super Admin, so let's create a dummy user first to occupy ID 1
    $adminUser = User::factory()->create(['id' => 1]);

    // Create a regular user who is the creator
    $creatorUser = User::factory()->create(['id' => 20]);
    $creatorEmployee = Employee::create([
        'user_id' => $creatorUser->id,
        'employee_number' => 'EMP220',
        'full_name' => 'Creator Employee Show',
        'gender' => 2,
        'activation' => 1,
    ]);

    // Create a regular user who is the recipient
    $recipientUser = User::factory()->create(['id' => 21]);
    $recipientEmployee = Employee::create([
        'user_id' => $recipientUser->id,
        'employee_number' => 'EMP221',
        'full_name' => 'Recipient Employee Show',
        'gender' => 2,
        'activation' => 1,
    ]);

    // Create a regular user who is unrelated
    $unrelatedUser = User::factory()->create(['id' => 22]);
    $unrelatedEmployee = Employee::create([
        'user_id' => $unrelatedUser->id,
        'employee_number' => 'EMP222',
        'full_name' => 'Unrelated Employee Show',
        'gender' => 2,
        'activation' => 1,
    ]);

    // Create a report
    $report = Report::create([
        'report_name' => 'Private Report Show',
        'report_date' => now(),
        'date_from' => now()->subDays(5),
        'date_to' => now(),
        'employee_id' => $creatorEmployee->id,
        'addressed_to_employees' => $recipientEmployee->id,
        'addressed_to_dept_types' => ['management'],
        'report_period_type' => $this->periodType->id,
        'report_main_type' => $this->mainType->id,
    ]);

    // 1. Creator can view
    $this->actingAs($creatorUser);
    Livewire::test(ShowReport::class, ['report' => $report])
        ->assertStatus(200);

    // 2. Recipient can view
    $this->actingAs($recipientUser);
    Livewire::test(ShowReport::class, ['report' => $report])
        ->assertStatus(200);

    // 3. Unrelated user gets 403
    $this->actingAs($unrelatedUser);
    Livewire::test(ShowReport::class, ['report' => $report])
        ->assertStatus(403);

    // 4. Super Admin can view
    $this->actingAs($adminUser);
    Livewire::test(ShowReport::class, ['report' => $report])
        ->assertStatus(200);
});

it('only marks the report as read when viewed by the addressed employee', function () {
    $creatorUser = User::factory()->create(['id' => 30]);
    $creatorEmployee = Employee::create([
        'user_id' => $creatorUser->id,
        'employee_number' => 'EMP330',
        'full_name' => 'Creator Employee Show',
        'gender' => 2,
        'activation' => 1,
    ]);

    $recipientUser = User::factory()->create(['id' => 31]);
    $recipientEmployee = Employee::create([
        'user_id' => $recipientUser->id,
        'employee_number' => 'EMP331',
        'full_name' => 'Recipient Employee Show',
        'gender' => 2,
        'activation' => 1,
    ]);

    $adminUser = User::factory()->create(['id' => 1]); // Super admin

    $report = Report::create([
        'report_name' => 'Read Status Report',
        'report_date' => now(),
        'date_from' => now()->subDays(5),
        'date_to' => now(),
        'employee_id' => $creatorEmployee->id,
        'addressed_to_employees' => $recipientEmployee->id,
        'addressed_to_dept_types' => ['management'],
        'report_period_type' => $this->periodType->id,
        'report_main_type' => $this->mainType->id,
        'is_read' => false,
    ]);

    // 1. Unrelated super admin views - should NOT mark as read
    $this->actingAs($adminUser);
    Livewire::test(ShowReport::class, ['report' => $report]);
    expect($report->fresh()->is_read)->toBeFalse();

    // 2. Creator views - should NOT mark as read
    $this->actingAs($creatorUser);
    Livewire::test(ShowReport::class, ['report' => $report]);
    expect($report->fresh()->is_read)->toBeFalse();

    // 3. Recipient views - SHOULD mark as read
    $this->actingAs($recipientUser);
    Livewire::test(ShowReport::class, ['report' => $report]);
    expect($report->fresh()->is_read)->toBeTrue();
});

