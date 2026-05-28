<?php

use App\Livewire\OrgApp\StudentGroups\Create;
use App\Livewire\OrgApp\StudentGroups\Edit;
use App\Livewire\OrgApp\StudentGroups\Index;
use App\Models\City;
use App\Models\Location;
use App\Models\Neighbourhood;
use App\Models\Region;
use App\Models\Status;
use App\Models\StudentGroup;
use App\Models\StudentGroupSchedule;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);

    $this->region = Region::create(['region_name' => 'Gaza']);
    $this->city = City::create(['city_name' => 'Gaza City', 'region_id' => $this->region->id]);
    $this->neighbourhood = Neighbourhood::create(['neighbourhood_name' => 'Rimal', 'city_id' => $this->city->id]);
    $this->location = Location::create(['location_name' => 'Main Office', 'neighbourhood_id' => $this->neighbourhood->id]);
    $this->status = Status::create(['status_name' => 'Active', 'p_id' => 1]);

    // Create required sub-group statuses for daily attendance tests
    Illuminate\Support\Facades\DB::table('statuses')->insertOrIgnore([
        ['id' => 124, 'status_name' => 'Student Groups', 'p_id' => null, 'p_id_sub' => null],
        ['id' => 125, 'status_name' => 'Level 1', 'p_id' => null, 'p_id_sub' => 124],
        ['id' => 126, 'status_name' => 'Level 2', 'p_id' => null, 'p_id_sub' => 124],
    ]);
});

it('renders the create student group page', function () {
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('validates required fields', function () {
    Livewire::test(Create::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors([
            'name' => 'required',
        ]);
});

it('validates min and max students logic', function () {
    Livewire::test(Create::class)
        ->set('max_students', 5)
        ->set('min_students', 10)
        ->call('save')
        ->assertHasErrors(['max_students', 'min_students']);
});

it('creates a student group with schedules', function () {
    $startDate = now()->toDateString();
    $endDate = now()->addDays(5)->toDateString();
    $startTime = '08:00';
    $endTime = '10:00';

    Livewire::test(Create::class)
        ->set('name', 'Group Alpha')
        ->set('min_students', 5)
        ->set('max_students', 20)
        ->set('batch_no', 1)
        ->set('region_id', $this->region->id)
        ->set('city_id', $this->city->id)
        ->set('neighbourhood_id', $this->neighbourhood->id)
        ->set('location_id', $this->location->id)
        ->set('start_date', $startDate)
        ->set('end_date', $endDate)
        ->set('start_time', $startTime)
        ->set('end_time', $endTime)
        ->set('activation', 1)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('student.group.index'));

    $this->assertDatabaseHas('student_groups', [
        'name' => 'Group Alpha',
        'min_students' => 5,
        'max_students' => 20,
    ]);

    // Verify schedules are created (6 days total, start to end inclusive)
    $this->assertDatabaseCount('student_group_schedules', 6);
});

it('validates unique name', function () {
    StudentGroup::create(['name' => 'Existing Group', 'batch_no' => 1]);

    Livewire::test(Create::class)
        ->set('name', 'Existing Group')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

it('renders edit page', function () {
    $group = StudentGroup::create(['name' => 'Edit Group', 'batch_no' => 1]);

    Livewire::test(Edit::class, ['group' => $group])
        ->assertStatus(200);
});

it('updates a student group', function () {
    $group = StudentGroup::create([
        'name' => 'Old Name',
        'min_students' => 5,
        'max_students' => 10,
        'activation' => 1,
        'batch_no' => 1,
    ]);

    Livewire::test(Edit::class, ['group' => $group])
        ->set('name', 'New Name')
        ->set('max_students', 15)
        ->set('start_date', '2026-05-01')
        ->set('end_date', '2026-05-30')
        ->set('start_time', '08:00')
        ->set('end_time', '10:00')
        ->set('batch_no', 1)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('student.group.index'));

    $this->assertDatabaseHas('student_groups', [
        'id' => $group->id,
        'name' => 'New Name',
        'max_students' => 15,
    ]);
});

it('renders index page', function () {
    StudentGroup::create(['name' => 'Group Index', 'batch_no' => 1]);

    // $this->user has ID 1 by default since it is the first created user, so it is superadmin.
    actingAs($this->user);

    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertSee('Group Index');
});

it('restricts groups list for a teacher to only their assigned groups', function () {
    Gate::define('student.group.index', fn() => true);

    // Create two groups
    $group1 = StudentGroup::create(['name' => 'Group One', 'batch_no' => 1]);
    $group2 = StudentGroup::create(['name' => 'Group Two', 'batch_no' => 2]);

    // Create a teacher user and profile
    $teacherUser = User::factory()->create(['activation' => 1]);
    $employee = Employee::create([
        'user_id' => $teacherUser->id,
        'full_name' => 'Teacher One',
        'employee_number' => 'EMP501',
        'date_of_birth' => '1990-01-01',
        'phone' => '0599000501',
        'email' => $teacherUser->email,
        'activation' => 1,
        'gender' => 2,
    ]);

    // Assign teacher user to Group One
    $teacherUser->teacher()->create([
        'student_group_id' => $group1->id,
    ]);

    // Act as the teacher
    actingAs($teacherUser);

    $component = Livewire::test(Index::class)
        ->assertStatus(200);

    $groups = $component->instance()->groups;
    expect($groups->items())->toHaveCount(1);
    expect($groups->items()[0]->id)->toBe($group1->id);
});

it('can view group details in modal', function () {
    $group = StudentGroup::create([
        'name' => 'Details Group',
        'min_students' => 5,
        'max_students' => 10,
        'activation' => 1,
        'batch_no' => 1,
    ]);

    Livewire::test(Index::class)
        ->assertSet('showDetailsModal', false)
        ->assertSet('selectedGroup', null)
        ->call('viewGroupDetails', $group->id)
        ->assertSet('showDetailsModal', true)
        ->assertSee('Details Group')
        ->call('closeDetailsModal')
        ->assertSet('showDetailsModal', false)
        ->assertSet('selectedGroup', null);
});

it('saves daily attendance only for students in status groups that have at least one present student', function () {
    $group = StudentGroup::create([
        'name' => 'Attendance Group 1',
        'batch_no' => 1,
        'min_students' => 5,
        'max_students' => 20,
        'activation' => 1,
    ]);

    // Student 1 and 2 are in status 125
    $student1 = \App\Models\Student::create([
        'identity_number' => '123456789',
        'full_name' => 'Student One',
        'birth_date' => '2015-05-01',
        'gender' => 2,
        'student_groups_id' => $group->id,
        'activation' => 1,
        'status_id' => 125,
        'enrollment_type' => 'full_week',
    ]);

    $student2 = \App\Models\Student::create([
        'identity_number' => '123456788',
        'full_name' => 'Student Two',
        'birth_date' => '2015-05-01',
        'gender' => 2,
        'student_groups_id' => $group->id,
        'activation' => 1,
        'status_id' => 125,
        'enrollment_type' => 'full_week',
    ]);

    // Student 3 is in status 126
    $student3 = \App\Models\Student::create([
        'identity_number' => '987654321',
        'full_name' => 'Student Three',
        'birth_date' => '2015-05-01',
        'gender' => 2,
        'student_groups_id' => $group->id,
        'activation' => 1,
        'status_id' => 126,
        'enrollment_type' => 'full_week',
    ]);

    $date = now()->format('Y-m-d');

    // Only Student 1 (status 125) is marked present. Student 2 (status 125) is unchecked.
    // Student 3 (status 126) is unchecked.
    Livewire::test(\App\Livewire\OrgApp\StudentGroups\DailyStudents::class, [
        'group' => $group,
        'date' => $date
    ])
    ->set('attendance', [
        $student1->id => true,
        $student2->id => false,
        $student3->id => false,
    ])
    ->call('saveAttendance')
    ->assertHasNoErrors()
    ->assertDispatched('attendance-saved');

    // Student 1 (status 125) was checked present, so status group 125 is active.
    // Student 1 should be saved as present.
    $this->assertDatabaseHas('student_daily_attendances', [
        'student_id' => $student1->id,
        'status' => 'present',
    ]);

    // Student 2 (status 125) is in the same status group and was unchecked, so saved as absent.
    $this->assertDatabaseHas('student_daily_attendances', [
        'student_id' => $student2->id,
        'status' => 'absent',
    ]);

    // Student 3 (status 126) was unchecked and no student in status 126 was checked present, so skipped.
    $this->assertDatabaseMissing('student_daily_attendances', [
        'student_id' => $student3->id,
    ]);
});

it('saves daily attendance for multiple active status groups', function () {
    $group = StudentGroup::create([
        'name' => 'Attendance Group 2',
        'batch_no' => 1,
        'min_students' => 5,
        'max_students' => 20,
        'activation' => 1,
    ]);

    // Student 1 (status 125)
    $student1 = \App\Models\Student::create([
        'identity_number' => '123456780',
        'full_name' => 'Student One',
        'birth_date' => '2015-05-01',
        'gender' => 2,
        'student_groups_id' => $group->id,
        'activation' => 1,
        'status_id' => 125,
        'enrollment_type' => 'full_week',
    ]);

    // Student 2 (status 126)
    $student2 = \App\Models\Student::create([
        'identity_number' => '987654320',
        'full_name' => 'Student Two',
        'birth_date' => '2015-05-01',
        'gender' => 2,
        'student_groups_id' => $group->id,
        'activation' => 1,
        'status_id' => 126,
        'enrollment_type' => 'full_week',
    ]);

    // Student 3 (status 125) - unchecked
    $student3 = \App\Models\Student::create([
        'identity_number' => '123456781',
        'full_name' => 'Student Three',
        'birth_date' => '2015-05-01',
        'gender' => 2,
        'student_groups_id' => $group->id,
        'activation' => 1,
        'status_id' => 125,
        'enrollment_type' => 'full_week',
    ]);

    $date = now()->format('Y-m-d');

    // Both student 1 (status 125) and student 2 (status 126) are checked present.
    // Both status groups 125 and 126 are active.
    Livewire::test(\App\Livewire\OrgApp\StudentGroups\DailyStudents::class, [
        'group' => $group,
        'date' => $date
    ])
    ->set('attendance', [
        $student1->id => true,
        $student2->id => true,
        $student3->id => false,
    ])
    ->call('saveAttendance')
    ->assertHasNoErrors()
    ->assertDispatched('attendance-saved');

    $this->assertDatabaseHas('student_daily_attendances', [
        'student_id' => $student1->id,
        'status' => 'present',
    ]);

    $this->assertDatabaseHas('student_daily_attendances', [
        'student_id' => $student2->id,
        'status' => 'present',
    ]);

    $this->assertDatabaseHas('student_daily_attendances', [
        'student_id' => $student3->id,
        'status' => 'absent',
    ]);
});
