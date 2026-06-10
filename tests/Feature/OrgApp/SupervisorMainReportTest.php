<?php

use App\Livewire\OrgApp\Reports\SupervisorActivitiesReport;
use App\Livewire\OrgApp\Reports\CreateReport;
use App\Models\ActivitySchedule;
use App\Models\EducationalActivityDetail;
use App\Models\EducationalActivityName;
use App\Models\Employee;
use App\Models\StudentGroup;
use App\Models\User;
use App\Models\Status;
use App\Models\Report;
use App\Models\ReportBody;
use App\Mail\SendReportMail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Gate::define('reports.all', fn() => true);

    Status::forceCreate(['id' => 166, 'status_name' => 'Teacher',    'p_id_sub' => 165]);
    Status::forceCreate(['id' => 167, 'status_name' => 'Supervisor', 'p_id_sub' => 165]);

    $this->group = StudentGroup::create([
        'name'       => 'Group X',
        'batch_no'   => '5',
        'activation' => 1,
        'start_date' => now()->subDays(5),
        'end_date'   => now()->addDays(5),
    ]);

    $this->supervisorUser     = User::factory()->create(['id' => 10, 'activation' => 1]);
    $this->supervisorEmployee = Employee::create([
        'user_id'         => $this->supervisorUser->id,
        'full_name'       => 'Supervisor User',
        'employee_number' => 'SUP101',
        'date_of_birth'   => '1990-01-01',
        'phone'           => '0599000101',
        'email'           => $this->supervisorUser->email,
        'activation'      => 1,
        'gender'          => 2,
    ]);

    $this->supervisorUser->teacher()->create([
        'student_group_id' => $this->group->id,
        'job_title'        => 167,
    ]);

    $this->teacherUser     = User::factory()->create(['id' => 20, 'activation' => 1]);
    $this->teacherEmployee = Employee::create([
        'user_id'         => $this->teacherUser->id,
        'full_name'       => 'Teacher User',
        'employee_number' => 'TEA101',
        'date_of_birth'   => '1990-01-01',
        'phone'           => '0599000102',
        'email'           => $this->teacherUser->email,
        'activation'      => 1,
        'gender'          => 2,
    ]);

    $this->addressedUser     = User::factory()->create(['id' => 30, 'activation' => 1]);
    $this->addressedEmployee = Employee::create([
        'user_id'         => $this->addressedUser->id,
        'full_name'       => 'Addressed Employee',
        'employee_number' => 'ADD101',
        'date_of_birth'   => '1990-01-01',
        'phone'           => '0599000103',
        'email'           => 'addressed@example.com',
        'activation'      => 1,
        'gender'          => 2,
    ]);

    $this->ccUser     = User::factory()->create(['id' => 40, 'activation' => 1]);
    $this->ccEmployee = Employee::create([
        'user_id'         => $this->ccUser->id,
        'full_name'       => 'CC Employee',
        'employee_number' => 'CC101',
        'date_of_birth'   => '1990-01-01',
        'phone'           => '0599000104',
        'email'           => 'cc@example.com',
        'activation'      => 1,
        'gender'          => 2,
    ]);

    $periodParent         = Status::create(['status_name' => 'Report Period Type Root', 'route_system_name' => 'report_period_type', 'p_id_sub' => null]);
    $this->periodTypeStatus = Status::create(['status_name' => 'شهري',           'p_id_sub' => $periodParent->id]);

    $mainParent           = Status::create(['status_name' => 'Report Main Type Root',   'route_system_name' => 'report_main_type', 'p_id_sub' => null]);
    $this->mainTypeStatus = Status::create(['status_name' => 'تقرير الأنشطة',   'p_id_sub' => $mainParent->id]);
});

// ──────────────────────────────────────────────────────────────────────────
// Test 1: openCreateReport stores draft in session and redirects
// ──────────────────────────────────────────────────────────────────────────

it('openCreateReport stores draft in session and redirects to create page', function () {
    $activityName = EducationalActivityName::create(['activity_name' => 'Creative Writing', 'activation' => 1]);

    $schedule = ActivitySchedule::create([
        'activity_name' => (string) $activityName->id,
        'group_id'      => $this->group->id,
        'period_start'  => now(),
        'period_end'    => now()->addHour(),
        'employee_id'   => $this->teacherEmployee->id,
        'activation'    => 1,
    ]);

    EducationalActivityDetail::create([
        'educational_activity_id' => $schedule->id,
        'consistent'              => 8,
        'what_learned'            => 'Learned spelling',
        'teacher_report_detail'   => 'Great spelling class.',
        'attchments'              => [['name' => 'img1.png', 'url' => 'uploads/img1.png']],
    ]);

    actingAs($this->supervisorUser);

    $compoundKey = $this->group->id . '_' . $activityName->id;

    Livewire::test(SupervisorActivitiesReport::class)
        ->set('selectedActivities', [$compoundKey])
        ->call('openCreateReport')
        ->assertRedirect(route('reports.create'));

    // Verify session draft structure
    expect(session()->has('report_draft'))->toBeTrue();

    $draft = session('report_draft');
    expect($draft['source'])->toBe('supervisor_activities');
    expect($draft['items'])->toHaveCount(1);
    expect($draft['items'][0]['title'])->toContain('Creative Writing');
    expect($draft['items'][0]['content'])->toContain('Learned spelling');
    expect($draft['covered_educational_activity_schedules_ids'])->toContain($schedule->id);
});

// ──────────────────────────────────────────────────────────────────────────
// Test 2: CreateReport saves report + bodies + sends email
// ──────────────────────────────────────────────────────────────────────────

it('CreateReport saves report, report bodies, and sends email', function () {
    actingAs($this->supervisorUser);
    Mail::fake();

    $scheduleId = 55;
    $detailId   = 66;

    // Put a draft in session as if it came from SupervisorActivitiesReport
    session()->put('report_draft', [
        'source'    => 'supervisor_activities',
        'date_from' => now()->startOfMonth()->format('Y-m-d'),
        'date_to'   => now()->format('Y-m-d'),
        'batch_no'  => '5',
        'student_group_ids'                            => [$this->group->id],
        'covered_educational_activities_ids'           => [99],
        'covered_educational_activity_schedules_ids'   => [$scheduleId],
        'covered_educational_activity_details_ids'     => [$detailId],
        'items' => [[
            'title'                => 'Creative Writing — Group X',
            'content'              => "ما تعلمه الطلاب:\n- Learned spelling\n\nملاحظات المعلمين:\n- Great spelling class.",
            'observation'          => '',
            'attachments_pool'     => [['name' => 'img1.png', 'url' => 'uploads/img1.png']],
            'selected_attachments' => [],
        ]],
    ]);

    Livewire::test(CreateReport::class)
        ->set('report_name', 'Consolidated Activity Report')
        ->set('report_period_type', $this->periodTypeStatus->id)
        ->set('report_main_type', $this->mainTypeStatus->id)
        ->set('addressed_to_employees', $this->addressedEmployee->id)
        ->set('addressed_to_dept_types', ['center_director'])
        ->set('follow_up_by', [(string) $this->ccEmployee->id])
        ->set('note', 'General notes for management')
        ->set('reportItems.0.selected_attachments', [['name' => 'img1.png', 'url' => 'uploads/img1.png']])
        ->call('saveReport')
        ->assertHasNoErrors()
        ->assertRedirect(route('reports.saved-reports'));

    // Verify Report DB record
    $this->assertDatabaseHas('reports', [
        'report_name'            => 'Consolidated Activity Report',
        'addressed_to_employees' => $this->addressedEmployee->id,
        'employee_id'            => $this->supervisorEmployee->id,
    ]);

    $report = Report::where('report_name', 'Consolidated Activity Report')->first();
    expect($report)->not->toBeNull();
    expect($report->student_group_ids)->toContain($this->group->id);
    expect($report->covered_educational_activity_schedules_ids)->toContain($scheduleId);
    expect($report->covered_educational_activity_details_ids)->toContain($detailId);

    // Verify ReportBody
    $this->assertDatabaseHas('report_body', ['report_id' => $report->id]);
    $body = ReportBody::where('report_id', $report->id)->first();
    expect($body->attachments)->toHaveCount(1);
    expect($body->attachments[0]['name'])->toBe('img1.png');

    // Verify Email
    Mail::assertSent(SendReportMail::class, function ($mail) {
        return $mail->hasTo($this->addressedEmployee->email)
            && $mail->hasCc($this->ccEmployee->email);
    });
});

// ──────────────────────────────────────────────────────────────────────────
// Test 3: CreateReport CC autocomplete helper methods
// ──────────────────────────────────────────────────────────────────────────

it('CreateReport supports follow up by autocomplete helper methods', function () {
    actingAs($this->supervisorUser);

    Livewire::test(CreateReport::class)
        ->assertSet('follow_up_by', [])
        ->set('ccSearch', 'CC')
        ->call('addCcEmployee', $this->ccEmployee->id)
        ->assertSet('follow_up_by', [(string) $this->ccEmployee->id])
        ->assertSet('ccSearch', '')
        ->call('removeCcEmployee', $this->ccEmployee->id)
        ->assertSet('follow_up_by', []);
});

// ──────────────────────────────────────────────────────────────────────────
// Test 4: CreateReport can add / remove report items
// ──────────────────────────────────────────────────────────────────────────

it('CreateReport can add and remove report items', function () {
    actingAs($this->supervisorUser);

    Livewire::test(CreateReport::class)
        ->assertCount('reportItems', 1)
        ->call('addItem')
        ->assertCount('reportItems', 2)
        ->call('removeItem', 1)
        ->assertCount('reportItems', 1);
});

// ──────────────────────────────────────────────────────────────────────────
// Test 5: Policy locking after report is generated
// ──────────────────────────────────────────────────────────────────────────

it('locks detail and schedule edits after report is generated', function () {
    actingAs($this->supervisorUser);
    Mail::fake();

    $activityName = EducationalActivityName::create(['activity_name' => 'Locking Test Activity', 'activation' => 1]);

    $schedule = ActivitySchedule::create([
        'activity_name' => (string) $activityName->id,
        'group_id'      => $this->group->id,
        'period_start'  => now(),
        'period_end'    => now()->addHour(),
        'employee_id'   => $this->teacherEmployee->id,
        'activation'    => 1,
    ]);

    $detail = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule->id,
        'consistent'              => 4,
        'what_learned'            => 'Locking test',
        'teacher_report_detail'   => 'Locking test notes',
    ]);

    // Create the report manually to simulate what CreateReport::saveReport() does
    $report = Report::create([
        'report_name'                                => 'Lock Test Report',
        'report_period_type'                         => $this->periodTypeStatus->id,
        'report_main_type'                           => $this->mainTypeStatus->id,
        'report_date'                                => now()->format('Y-m-d'),
        'date_from'                                  => now()->startOfMonth()->format('Y-m-d'),
        'date_to'                                    => now()->format('Y-m-d'),
        'student_group_ids'                          => [$this->group->id],
        'employee_id'                                => $this->supervisorEmployee->id,
        'addressed_to_dept_types'                    => ['center_director'],
        'addressed_to_employees'                     => $this->addressedEmployee->id,
        'follow_up_by'                               => [],
        'covered_educational_activities_ids'         => [(int) $activityName->id],
        'covered_educational_activity_schedules_ids' => [$schedule->id],
        'covered_educational_activity_details_ids'   => [$detail->id],
        'note'                                       => null,
    ]);

    // Policy should now deny updates/deletes
    expect(Gate::forUser($this->supervisorUser)->allows('update', $detail))->toBeFalse();
    expect(Gate::forUser($this->supervisorUser)->allows('delete', $detail))->toBeFalse();
    expect(Gate::forUser($this->supervisorUser)->allows('update', $schedule))->toBeFalse();
    expect(Gate::forUser($this->supervisorUser)->allows('delete', $schedule))->toBeFalse();
});

it('CreateReport supports AI summarization for report items', function () {
    actingAs($this->supervisorUser);

    // Mock AIService
    $mockAIService = Mockery::mock(\App\Services\AIService::class);
    $mockAIService->shouldReceive('generateContent')
        ->once()
        ->with(Mockery::on(function ($prompt) {
            return str_contains($prompt, 'Learned spelling');
        }))
        ->andReturn('AI-generated summary paragraph of spelling.');

    $this->app->instance(\App\Services\AIService::class, $mockAIService);

    // Put a draft in session as if it came from SupervisorActivitiesReport
    session()->put('report_draft', [
        'source'    => 'supervisor_activities',
        'date_from' => now()->startOfMonth()->format('Y-m-d'),
        'date_to'   => now()->format('Y-m-d'),
        'batch_no'  => '5',
        'student_group_ids'                  => [$this->group->id],
        'items' => [[
            'title'   => 'Creative Writing — Group X',
            'content' => "ما تعلمه الطلاب:\n- Learned spelling",
        ]],
    ]);

    Livewire::test(CreateReport::class)
        ->assertSet('reportItems.0.content', "ما تعلمه الطلاب:\n- Learned spelling")
        ->call('summarizeItemWithAI', 0)
        ->assertSet('reportItems.0.content', 'AI-generated summary paragraph of spelling.');
});

