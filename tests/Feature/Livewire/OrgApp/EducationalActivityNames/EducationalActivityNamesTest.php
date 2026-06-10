<?php

use App\Models\User;
use App\Models\Employee;
use App\Models\Status;
use App\Models\EducationalActivityName;
use App\Models\ActivitySchedule;
use Livewire\Livewire;
use App\Livewire\OrgApp\EducationalActivityNames\Index;
use App\Livewire\OrgApp\EducationalActivityNames\Create;
use App\Livewire\OrgApp\EducationalActivityNames\Edit;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    // Setup super admin user (ID = 1)
    $this->adminUser = User::factory()->create(['id' => 1]);
    $this->actingAs($this->adminUser);

    // Seed domain status
    $this->domainStatus = Status::forceCreate([
        'id' => 186,
        'status_name' => 'Cognitive Domain',
        'p_id_sub' => 185,
    ]);

    // Seed teacher employee
    $this->teacher = Employee::forceCreate([
        'user_id' => User::factory()->create()->id,
        'employee_number' => 'T1001',
        'full_name' => 'Teacher One',
        'gender' => 1,
        'activation' => 1,
    ]);
});

it('renders the educational activity names index page', function () {
    EducationalActivityName::create([
        'activity_name' => 'Painting Class',
        'activity_domain' => $this->domainStatus->id,
        'available_in_active_groups' => true,
        'activation' => 1,
    ]);

    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertSee('Painting Class');
});

it('can create a new educational activity name', function () {
    Livewire::test(Create::class)
        ->set('activity_name', 'Chess Class')
        ->set('activity_domain', $this->domainStatus->id)
        ->set('available_in_active_groups', true)
        ->set('teachers', [(string) $this->teacher->id])
        ->set('description', 'Learn chess strategies')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('educational-activity-names.index'));

    $this->assertDatabaseHas('educational_activity_names', [
        'activity_name' => 'Chess Class',
        'activity_domain' => $this->domainStatus->id,
    ]);

    $created = EducationalActivityName::where('activity_name', 'Chess Class')->first();
    expect($created->teachers)->toContain((string) $this->teacher->id);
});

it('validates unique activity name', function () {
    EducationalActivityName::create([
        'activity_name' => 'Unique Activity',
        'activation' => 1,
    ]);

    Livewire::test(Create::class)
        ->set('activity_name', 'Unique Activity')
        ->call('save')
        ->assertHasErrors(['activity_name']);
});

it('can edit an educational activity name', function () {
    $activity = EducationalActivityName::create([
        'activity_name' => 'Old Activity',
        'activation' => 1,
    ]);

    Livewire::test(Edit::class, ['educationalActivityName' => $activity])
        ->set('activity_name', 'Updated Activity')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('educational-activity-names.index'));

    $this->assertDatabaseHas('educational_activity_names', [
        'id' => $activity->id,
        'activity_name' => 'Updated Activity',
    ]);
});

it('can delete an educational activity name when not linked to schedules', function () {
    $activity = EducationalActivityName::create([
        'activity_name' => 'Temporary Activity',
        'activation' => 1,
    ]);

    Livewire::test(Index::class)
        ->call('delete', $activity->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('educational_activity_names', [
        'id' => $activity->id,
    ]);
});

it('cannot delete an educational activity name when linked to schedules', function () {
    $activity = EducationalActivityName::create([
        'activity_name' => 'In-Use Activity',
        'activation' => 1,
    ]);

    // Link it to an activity schedule
    ActivitySchedule::forceCreate([
        'activity_name' => $activity->id,
        'period_start' => now(),
        'period_end' => now()->addHour(),
        'target_category' => 'children',
        'activation' => 1,
    ]);

    Livewire::test(Index::class)
        ->call('delete', $activity->id)
        ->assertSee(__('Cannot delete this activity name because it is linked to scheduled activities.'));

    $this->assertDatabaseHas('educational_activity_names', [
        'id' => $activity->id,
    ]);
});

it('rejects core-duplicate activity names on create', function () {
    EducationalActivityName::create([
        'activity_name' => 'المهارة الثالثة التعرف على الحروف',
        'activation' => 1,
    ]);

    // Try to create a semantically identical name with parenthetical prefix and separators
    Livewire::test(Create::class)
        ->set('activity_name', '(Hello abc) - المهارة الثالثة - التعرف على الحروف')
        ->call('save')
        ->assertHasErrors(['activity_name']);
});

it('rejects core-duplicate activity names with different session or skill prefixes', function () {
    EducationalActivityName::create([
        'activity_name' => 'المهارة الثالثة - استكشاف الهوية الحقيقة من انا',
        'activation' => 1,
    ]);

    // Try to create with "الجلسة الثالثة" instead of "المهارة الثالثة"
    Livewire::test(Create::class)
        ->set('activity_name', 'الجلسة الثالثة - استكشاف الهوية الحقيقة من انا')
        ->call('save')
        ->assertHasErrors(['activity_name']);
});

it('normalizes separators in activity names on save', function () {
    Livewire::test(Create::class)
        ->set('activity_name', '  المهارة الأولى  -  صيد_الحروف  ')
        ->set('activation', 1)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('educational-activity-names.index'));

    $this->assertDatabaseHas('educational_activity_names', [
        'activity_name' => 'المهارة الأولى صيد الحروف',
    ]);
});

it('allows editing an activity name without triggering core duplicate error', function () {
    $activity = EducationalActivityName::create([
        'activity_name' => 'My Unique Activity',
        'activation' => 1,
    ]);

    // Editing without changing the name should succeed
    Livewire::test(Edit::class, ['educationalActivityName' => $activity])
        ->set('activity_name', 'My Unique Activity')
        ->call('save')
        ->assertHasNoErrors();
});
