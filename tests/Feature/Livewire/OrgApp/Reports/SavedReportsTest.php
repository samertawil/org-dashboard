<?php

use App\Models\User;
use App\Models\Employee;
use App\Models\Report;
use App\Models\ReportBody;
use App\Models\Status;
use Livewire\Livewire;
use App\Livewire\OrgApp\Reports\SavedReports;
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
});

afterEach(function () {
    Carbon::setTestNow();
});

it('allows authenticated users to view saved reports list', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(SavedReports::class)
        ->assertStatus(200)
        ->assertViewHas('reports');
});

it('allows searching and filtering reports', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $reportA = Report::create([
        'report_name' => 'First Special Report',
        'report_date' => now(),
        'date_from' => now()->subDays(5),
        'date_to' => now(),
        'employee_id' => $this->empA->id,
        'addressed_to_employees' => $this->empB->id,
        'addressed_to_dept_types' => ['management'],
        'report_period_type' => $this->periodType->id,
        'report_main_type' => $this->mainType->id,
    ]);

    $reportB = Report::create([
        'report_name' => 'Second Test Report',
        'report_date' => now(),
        'date_from' => now()->subDays(5),
        'date_to' => now(),
        'employee_id' => $this->empA->id,
        'addressed_to_employees' => $this->empB->id,
        'addressed_to_dept_types' => ['management'],
        'report_period_type' => $this->periodType->id,
        'report_main_type' => $this->mainType->id,
    ]);

    Livewire::test(SavedReports::class)
        ->set('search', 'Special')
        ->assertViewHas('reports', function ($reports) use ($reportA) {
            return $reports->count() === 1 && $reports->first()->id === $reportA->id;
        });
});

it('can view report details', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $report = Report::create([
        'report_name' => 'Detailed Report test',
        'report_date' => now(),
        'date_from' => now()->subDays(5),
        'date_to' => now(),
        'employee_id' => $this->empA->id,
        'addressed_to_employees' => $this->empB->id,
        'addressed_to_dept_types' => ['management'],
        'report_period_type' => $this->periodType->id,
        'report_main_type' => $this->mainType->id,
    ]);

    $body = ReportBody::create([
        'report_id' => $report->id,
        'item_order' => 1,
        'content' => 'Sample item content',
    ]);

    Livewire::test(SavedReports::class)
        ->assertSee(route('reports.show', $report));
});

it('allows super admin or creator to delete report', function () {
    // 1. Creator user
    $creatorUser = User::factory()->create();
    $creatorEmployee = Employee::create([
        'user_id' => $creatorUser->id,
        'employee_number' => 'EMP123',
        'full_name' => 'Creator Employee',
        'gender' => 2,
        'activation' => 1,
    ]);

    $report = Report::create([
        'report_name' => 'Deletable Report',
        'report_date' => now(),
        'date_from' => now()->subDays(5),
        'date_to' => now(),
        'employee_id' => $creatorEmployee->id,
        'addressed_to_employees' => $this->empB->id,
        'addressed_to_dept_types' => ['management'],
        'report_period_type' => $this->periodType->id,
        'report_main_type' => $this->mainType->id,
    ]);

    // Non-creator user cannot delete
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);

    Livewire::test(SavedReports::class)
        ->call('deleteReport', $report->id);

    expect(Report::find($report->id))->not->toBeNull();

    // Creator can delete
    $this->actingAs($creatorUser);

    Livewire::test(SavedReports::class)
        ->call('deleteReport', $report->id);

    expect(Report::find($report->id))->toBeNull();
});

it('restricts saved reports list to creator, recipient, or admin', function () {
    // ID 1 is automatically Super Admin, so let's create a dummy user first to occupy ID 1
    $adminUser = User::factory()->create(['id' => 1]);

    // Create a regular user who is the creator
    $creatorUser = User::factory()->create(['id' => 10]);
    $creatorEmployee = Employee::create([
        'user_id' => $creatorUser->id,
        'employee_number' => 'EMP110',
        'full_name' => 'Creator Employee',
        'gender' => 2,
        'activation' => 1,
    ]);

    // Create a regular user who is the recipient
    $recipientUser = User::factory()->create(['id' => 11]);
    $recipientEmployee = Employee::create([
        'user_id' => $recipientUser->id,
        'employee_number' => 'EMP111',
        'full_name' => 'Recipient Employee',
        'gender' => 2,
        'activation' => 1,
    ]);

    // Create a regular user who is unrelated
    $unrelatedUser = User::factory()->create(['id' => 12]);
    $unrelatedEmployee = Employee::create([
        'user_id' => $unrelatedUser->id,
        'employee_number' => 'EMP112',
        'full_name' => 'Unrelated Employee',
        'gender' => 2,
        'activation' => 1,
    ]);

    // Create a report
    $report = Report::create([
        'report_name' => 'Private Report',
        'report_date' => now(),
        'date_from' => now()->subDays(5),
        'date_to' => now(),
        'employee_id' => $creatorEmployee->id,
        'addressed_to_employees' => $recipientEmployee->id,
        'addressed_to_dept_types' => ['management'],
        'report_period_type' => $this->periodType->id,
        'report_main_type' => $this->mainType->id,
    ]);

    // 1. Creator can see the report
    $this->actingAs($creatorUser);
    Livewire::test(SavedReports::class)
        ->assertViewHas('reports', function ($reports) use ($report) {
            return $reports->pluck('id')->contains($report->id);
        });

    // 2. Recipient can see the report
    $this->actingAs($recipientUser);
    Livewire::test(SavedReports::class)
        ->assertViewHas('reports', function ($reports) use ($report) {
            return $reports->pluck('id')->contains($report->id);
        });

    // 3. Unrelated user CANNOT see the report
    $this->actingAs($unrelatedUser);
    Livewire::test(SavedReports::class)
        ->assertViewHas('reports', function ($reports) use ($report) {
            return !$reports->pluck('id')->contains($report->id);
        });

    // 4. Super Admin (ID 1) CAN see the report
    $this->actingAs($adminUser);
    Livewire::test(SavedReports::class)
        ->assertViewHas('reports', function ($reports) use ($report) {
            return $reports->pluck('id')->contains($report->id);
        });
});
