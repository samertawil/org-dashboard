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
    StudentGroup::create(['name' => 'Existing Group']);

    Livewire::test(Create::class)
        ->set('name', 'Existing Group')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

it('renders edit page', function () {
    $group = StudentGroup::create(['name' => 'Edit Group']);

    Livewire::test(Edit::class, ['group' => $group])
        ->assertStatus(200);
});

it('updates a student group', function () {
    $group = StudentGroup::create([
        'name' => 'Old Name',
        'min_students' => 5,
        'max_students' => 10,
        'activation' => 1,
    ]);

    Livewire::test(Edit::class, ['group' => $group])
        ->set('name', 'New Name')
        ->set('max_students', 15)
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
    StudentGroup::create(['name' => 'Group Index']);

    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertSee('Group Index');
});
