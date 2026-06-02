<?php

use App\Livewire\OrgApp\EducationalActivityDetail\Index;
use App\Livewire\OrgApp\EducationalActivityDetail\Create;
use App\Livewire\OrgApp\EducationalActivityDetail\Edit;
use App\Livewire\OrgApp\EducationalActivityDetail\Show;
use App\Livewire\OrgApp\EducationalActivityDetail\Gallery;
use App\Models\Employee;
use App\Models\ActivitySchedule;
use App\Models\EducationalActivityDetail;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    // Setup permissions
    Gate::define('educational-activity-detail.index', fn() => true);
    Gate::define('educational-activity-detail.create', fn() => true);

    // Seed statuses
    \App\Models\Status::forceCreate([
        'id' => 193,
        'status_name' => 'Done',
        'p_id_sub' => 192,
    ]);

    \App\Models\Status::forceCreate([
        'id' => 194,
        'status_name' => 'Postponed',
        'p_id_sub' => 192,
    ]);

    \App\Models\Status::forceCreate([
        'id' => 195,
        'status_name' => 'Cancelled',
        'p_id_sub' => 192,
    ]);

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

    // Create user with explicit non-admin ID and activation = 1 to pass gate checks
    $this->user = User::factory()->create([
        'id' => 999,
        'activation' => 1,
    ]);
    $this->employee = Employee::create([
        'user_id' => $this->user->id,
        'full_name' => 'John Employee',
        'employee_number' => 'EMP101',
        'date_of_birth' => '1990-01-01',
        'phone' => '0599000101',
        'email' => $this->user->email,
        'activation' => 1,
        'gender' => 2,
    ]);

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

    // Create Student Group
    $this->studentGroup = \App\Models\StudentGroup::create([
        'id' => 1,
        'name' => 'Group A',
        'batch_no' => 1,
        'max_students' => 20,
        'activation' => 1,
    ]);

    // Create Teacher Student Group assignment for job_title 166 (Teacher)
    \App\Models\TeacherStudentGroup::create([
        'teacher_id' => $this->user->id,
        'student_group_id' => $this->studentGroup->id,
        'job_title' => 166,
    ]);
});

it('renders the index component showing only details assigned to the employee', function () {
    // Create schedule for logged-in employee
    $schedule1 = ActivitySchedule::create([
        'activity_name' => 'My Activity',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    $detail1 = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule1->id,
        'what_learned' => 'Learned A',
    ]);

    // Create schedule for another employee
    $schedule2 = ActivitySchedule::create([
        'activity_name' => 'Other Activity',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    $detail2 = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule2->id,
        'what_learned' => 'Learned B',
    ]);

    actingAs($this->user);

    Livewire::test(Index::class)
        ->assertViewHas('details', function ($details) use ($detail1, $detail2) {
            return $details->contains($detail1) && !$details->contains($detail2);
        });
});

it('allows super admin to see all details', function () {
    // Create admin user with ID 1 and activation = 1
    $adminUser = User::factory()->create([
        'id' => 1,
        'activation' => 1,
    ]);

    $schedule1 = ActivitySchedule::create([
        'activity_name' => 'My Activity',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    $detail1 = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule1->id,
        'what_learned' => 'Learned A',
    ]);

    $schedule2 = ActivitySchedule::create([
        'activity_name' => 'Other Activity',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    $detail2 = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule2->id,
        'what_learned' => 'Learned B',
    ]);

    actingAs($adminUser);

    Livewire::test(Index::class)
        ->assertViewHas('details', function ($details) use ($detail1, $detail2) {
            return $details->contains($detail1) && $details->contains($detail2);
        });
});

it('filters available schedules in create component dropdown', function () {
    $schedule1 = ActivitySchedule::create([
        'activity_name' => 'My Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    $schedule2 = ActivitySchedule::create([
        'activity_name' => 'Other Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    actingAs($this->user);

    $component = Livewire::test(Create::class);
    $schedules = $component->instance()->activitySchedules;

    expect($schedules->contains($schedule1))->toBeTrue();
    expect($schedules->contains($schedule2))->toBeFalse();
});

it('validates employee ownership when saving new details', function () {
    $scheduleOther = ActivitySchedule::create([
        'activity_name' => 'Other Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    actingAs($this->user);

    $component = Livewire::test(Create::class)
        ->set('educational_activity_id', $scheduleOther->id)
        ->set('status_id', 193)
        ->set('what_learned', 'Test text');

    expect(fn() => $component->instance()->save())
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class, 'You do not have permission to use this educational activity.');
});

it('validates employee ownership in edit route access', function () {
    $scheduleOther = ActivitySchedule::create([
        'activity_name' => 'Other Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    $detailOther = EducationalActivityDetail::create([
        'educational_activity_id' => $scheduleOther->id,
        'what_learned' => 'Learned Other',
    ]);

    actingAs($this->user);

    // This route should return 403
    get(route('educational-activity-detail.edit', $detailOther->id))
        ->assertStatus(403);
});

it('validates employee ownership in show route access', function () {
    $scheduleOther = ActivitySchedule::create([
        'activity_name' => 'Other Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    $detailOther = EducationalActivityDetail::create([
        'educational_activity_id' => $scheduleOther->id,
        'what_learned' => 'Learned Other',
    ]);

    actingAs($this->user);

    // This route should return 403
    get(route('educational-activity-detail.show', $detailOther->id))
        ->assertStatus(403);
});

it('validates employee ownership in gallery route access', function () {
    $scheduleOther = ActivitySchedule::create([
        'activity_name' => 'Other Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    $detailOther = EducationalActivityDetail::create([
        'educational_activity_id' => $scheduleOther->id,
        'what_learned' => 'Learned Other',
    ]);

    actingAs($this->user);

    // This route should return 403
    get(route('educational-activity-detail.gallery', $detailOther->id))
        ->assertStatus(403);
});

it('authorizes deletion only for owner', function () {
    $schedule1 = ActivitySchedule::create([
        'activity_name' => 'My Activity',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    $detail1 = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule1->id,
        'what_learned' => 'Learned A',
    ]);

    $schedule2 = ActivitySchedule::create([
        'activity_name' => 'Other Activity',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    $detail2 = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule2->id,
        'what_learned' => 'Learned B',
    ]);

    actingAs($this->user);

    // Delete own detail should succeed
    Livewire::test(Index::class)
        ->call('delete', $detail1->id);

    $this->assertDatabaseMissing('educational_activity_details', ['id' => $detail1->id]);

    // Delete other detail should fail
    $component = Livewire::test(Index::class);
    expect(fn() => $component->instance()->delete($detail2->id))
        ->toThrow(\Illuminate\Auth\Access\AuthorizationException::class, 'You do not have permission to for this report.');

    $this->assertDatabaseHas('educational_activity_details', ['id' => $detail2->id]);
});

it('can search and filter by activity date', function () {
    $targetDate = '2026-05-23';
    $otherDate = '2026-05-24';

    $schedule1 = ActivitySchedule::create([
        'activity_name' => 'Target Date Activity',
        'period_start' => \Carbon\Carbon::parse($targetDate . ' 10:00:00'),
        'period_end' => \Carbon\Carbon::parse($targetDate . ' 11:00:00'),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    $detail1 = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule1->id,
        'what_learned' => 'Learned A',
    ]);

    $schedule2 = ActivitySchedule::create([
        'activity_name' => 'Other Date Activity',
        'period_start' => \Carbon\Carbon::parse($otherDate . ' 10:00:00'),
        'period_end' => \Carbon\Carbon::parse($otherDate . ' 11:00:00'),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    $detail2 = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule2->id,
        'what_learned' => 'Learned B',
    ]);

    actingAs($this->user);

    // Test 1: Date Filter
    Livewire::test(Index::class)
        ->set('filterDate', $targetDate)
        ->assertViewHas('details', function ($details) use ($detail1, $detail2) {
            return $details->contains($detail1) && !$details->contains($detail2);
        });

    // Test 2: Search by date string
    Livewire::test(Index::class)
        ->set('search', $targetDate)
        ->assertViewHas('details', function ($details) use ($detail1, $detail2) {
            return $details->contains($detail1) && !$details->contains($detail2);
        });
});

it('colors gallery button blue when attachments exist', function () {
    $schedule = ActivitySchedule::create([
        'activity_name' => 'Attachment Test Activity',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    // Detail with attachments
    $detailWithAttachments = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule->id,
        'what_learned' => 'Learned A',
        'attchments' => [
            ['path' => 'file.jpg', 'name' => 'file']
        ]
    ]);

    // Detail without attachments
    $detailWithoutAttachments = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule->id,
        'what_learned' => 'Learned B',
        'attchments' => null
    ]);

    actingAs($this->user);

    Livewire::test(Index::class)
        ->assertSee('text-blue-500 hover:text-blue-700')
        ->assertSee('color: #3b82f6 !important;')
        ->assertDontSee('text-green-500');
});

it('allows user with select.any.educational-activity-detail permission to see all details', function () {
    Gate::define('select.any.educational-activity-detail', fn() => true);

    $schedule1 = ActivitySchedule::create([
        'activity_name' => 'My Activity',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    $detail1 = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule1->id,
        'what_learned' => 'Learned A',
    ]);

    $schedule2 = ActivitySchedule::create([
        'activity_name' => 'Other Activity',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    $detail2 = EducationalActivityDetail::create([
        'educational_activity_id' => $schedule2->id,
        'what_learned' => 'Learned B',
    ]);

    actingAs($this->user);

    Livewire::test(Index::class)
        ->assertViewHas('details', function ($details) use ($detail1, $detail2) {
            return $details->contains($detail1) && $details->contains($detail2);
        });
});

it('allows user with select.any.educational-activity-detail permission to save new detail for any employee schedule', function () {
    Gate::define('select.any.educational-activity-detail', fn() => true);

    $scheduleOther = ActivitySchedule::create([
        'activity_name' => 'Other Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    actingAs($this->user);

    Livewire::test(Create::class)
        ->set('educational_activity_id', $scheduleOther->id)
        ->set('status_id', 193)
        ->set('what_learned', 'Test text')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('educational_activity_details', [
        'educational_activity_id' => $scheduleOther->id,
        'what_learned' => 'Test text',
    ]);
});

it('allows user with select.any.educational-activity-detail permission to edit details of any employee', function () {
    Gate::define('select.any.educational-activity-detail', fn() => true);

    $scheduleOther = ActivitySchedule::create([
        'activity_name' => 'Other Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    $detailOther = EducationalActivityDetail::create([
        'educational_activity_id' => $scheduleOther->id,
        'what_learned' => 'Learned Other',
    ]);

    actingAs($this->user);

    // Should load the edit page successfully without 403
    get(route('educational-activity-detail.edit', $detailOther->id))
        ->assertStatus(200);

    // Save update with new schedule
    $scheduleOther2 = ActivitySchedule::create([
        'activity_name' => 'Other Schedule 2',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->otherEmployee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->otherUser->id,
    ]);

    Livewire::test(Edit::class, ['detail' => $detailOther])
        ->set('educational_activity_id', $scheduleOther2->id)
        ->set('status_id', 193)
        ->set('what_learned', 'Updated text')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('educational_activity_details', [
        'id' => $detailOther->id,
        'educational_activity_id' => $scheduleOther2->id,
        'what_learned' => 'Updated text',
    ]);
});

it('requires status_id', function () {
    $schedule = ActivitySchedule::create([
        'activity_name' => 'My Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    actingAs($this->user);

    Livewire::test(Create::class)
        ->set('educational_activity_id', $schedule->id)
        ->set('status_id', '')
        ->call('save')
        ->assertHasErrors(['status_id' => 'required']);
});

it('does not require replaced_activity and replaced_reason if status_id is 193', function () {
    $schedule = ActivitySchedule::create([
        'activity_name' => 'My Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    actingAs($this->user);

    Livewire::test(Create::class)
        ->set('educational_activity_id', $schedule->id)
        ->set('status_id', 193)
        ->set('replaced_activity', '')
        ->set('replaced_reason', '')
        ->call('save')
        ->assertHasNoErrors();
});

it('requires replaced_activity and replaced_reason if status_id is not 193', function () {
    $schedule = ActivitySchedule::create([
        'activity_name' => 'My Schedule',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    actingAs($this->user);

    Livewire::test(Create::class)
        ->set('educational_activity_id', $schedule->id)
        ->set('status_id', 194)
        ->set('replaced_activity', '')
        ->set('replaced_reason', '')
        ->call('save')
        ->assertHasErrors(['replaced_activity' => 'required', 'replaced_reason' => 'required']);
});

it('displays dynamic labels in the form based on target_category', function () {
    $scheduleChildren = ActivitySchedule::create([
        'activity_name' => 'Children Schedule',
        'target_category' => 'children',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    $scheduleParents = ActivitySchedule::create([
        'activity_name' => 'Parents Schedule',
        'target_category' => 'parents',
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'employee_id' => $this->employee->id,
        'group_id' => $this->studentGroup->id,
        'activation' => 1,
        'created_by' => $this->user->id,
    ]);

    actingAs($this->user);

    // 1. Initially empty/not selected: should show defaults
    Livewire::test(Create::class)
        ->assertSee('Consistent')
        ->assertSee('What Learned')
        ->assertSee('Teacher Report Detail');

    // 2. Select schedule with target_category = children: should show defaults
    Livewire::test(Create::class)
        ->set('educational_activity_id', $scheduleChildren->id)
        ->assertSee('Consistent')
        ->assertSee('What Learned')
        ->assertSee('Teacher Report Detail')
        ->assertDontSee('Turnout')
        ->assertDontSee('Goals')
        ->assertDontSee('Recommendations');

    // 3. Select schedule with target_category = parents (not children): should show custom labels
    Livewire::test(Create::class)
        ->set('educational_activity_id', $scheduleParents->id)
        ->assertSee('Turnout')
        ->assertSee('Goals')
        ->assertSee('Recommendations')
        ->assertDontSee('Consistent')
        ->assertDontSee('What Learned')
        ->assertDontSee('Teacher Report Detail');
});

