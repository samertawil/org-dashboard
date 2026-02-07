<?php

use App\Livewire\OrgApp\Activity\Create;
use App\Livewire\OrgApp\Activity\Edit;
use App\Models\Activity;
use App\Models\CurrancyValue;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Seed necessary statuses
    // Seed necessary statuses
    $this->sector = Status::create(['status_name' => 'Health', 'p_id_sub' => 29]); // Normal sector
    
    // Force ID 55 for Education sector as it is hardcoded in application logic
    DB::table('statuses')->insert([
        'id' => 55,
        'status_name' => 'Education',
        'p_id_sub' => 55,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $this->educationSector = Status::find(55);

    // Seed Currency for cost calculation
    CurrancyValue::create(['exchange_date' => now(), 'currency_value' => 3.5]);

});

it('renders the create activity page', function () {
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('validates core fields', function () {
    Livewire::test(Create::class)
        ->set('start_date', '') // Validation requires start_date to be empty to fail
        ->call('save')
        ->assertHasErrors([
            'start_date' => 'required',
            'sector_id' => 'required',
        ]);
});

it('validates date logic', function () {
    Livewire::test(Create::class)
        ->set('start_date', '2024-01-10')
        ->set('end_date', '2024-01-01') // Before start date
        ->call('save')
        ->assertHasErrors(['end_date']);
});

it('generates activity name automatically', function () {
    // If name is left as default "ACTIVITY #", it should append count + 1
    Livewire::test(Create::class)
        ->set('sector_id', $this->sector->id)
        ->set('start_date', now()->toDateString())
        ->set('name', 'ACTIVITY #')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('activities', [
        'name' => 'ACTIVITY #1',
    ]);
});

it('creates activity with parcels and beneficiaries', function () {
    $foodBoxStatus = Status::create(['status_name' => 'Food Box', 'p_id_sub' => 100]); // Mock parcel type
    $parcelData = [
        [
            'parcel_type' => $foodBoxStatus->id,
            'distributed_parcels_count' => 100,
            'cost_for_each_parcel' => 10,
            'status_id' => 1,
            'notes' => 'Test Parcel'
        ]
    ];

    Livewire::test(Create::class)
        ->set('name', 'Health Project')
        ->set('sector_id', $this->sector->id)
        ->set('start_date', now()->toDateString())
        ->set('parcels', $parcelData)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('activities', ['name' => 'Health Project']);
    $this->assertDatabaseHas('activity_parcels', ['parcel_type' => $foodBoxStatus->id]);
});

it('creates activity with teaching groups for education sector', function () {
    $teachingGroupData = [
        [
            'name' => 'Group A',
            'activation' => 1,
            'cost_usd' => 100,
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
        ]
    ];

    $component = Livewire::test(Create::class)
        ->set('name', 'Education Project')
        ->set('sector_id', $this->educationSector->id) // Education Sector logic (should be 55)
        ->set('start_date', now()->toDateString())
        ->set('end_date', now()->addDays(5)->toDateString())
        ->set('teaching_groups', $teachingGroupData)
        ->call('save');

    if ($component->errors()->isNotEmpty()) {
        dump($component->errors());
    }

    $component->assertHasNoErrors();

    $this->assertDatabaseHas('activities', ['name' => 'Education Project']);
    $this->assertDatabaseHas('teaching_groups', ['name' => 'Group A']);
});

it('updates activity and syncs relationships', function () {
    $activity = Activity::create([
        'name' => 'Old Activity',
        'start_date' => now(),
        'sector_id' => $this->sector->id,
        'status' => $this->sector->id, // Use sector ID as status ID for simplicity in test
        'activation' => 1,
        'cost' => 0,
        'cost_nis' => 0,
        'created_by' => $this->user->id
    ]);

    Livewire::test(Edit::class, ['activity' => $activity])
        ->set('name', 'Updated Activity')
        ->call('update')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('activities', ['name' => 'Updated Activity']);
});
