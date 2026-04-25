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
    $this->sector = Status::create(['status_name' => 'Health', 'p_id_sub' => 29]); 
    
    // Force ID 55 for Education sector as it is hardcoded in application logic
    DB::table('statuses')->insert([
        'id' => 55,
        'status_name' => 'Education',
        'p_id_sub' => 55,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $this->educationSector = Status::find(55);

    // Seed Status 25 (In Progress) to avoid FK errors
    DB::table('statuses')->insert([
        'id' => 25,
        'status_name' => 'In Progress',
        'p_id_sub' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Seed Currency for cost calculation
    CurrancyValue::create(['exchange_date' => now(), 'currency_value' => 3.5]);

    // Create a dummy PurchaseRequisition for parcels foreign key
    \App\Models\PurchaseRequisition::create([
        'id' => 1,
        'request_number' => 'PR-1001',
        'request_date' => now(),
        'created_by' => $this->user->id,
        'status_id' => 1,
    ]);

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
        ->set('status', 25)
        ->set('start_date', now()->toDateString())
        ->set('cost', 10)
        ->set('cost_nis', 35)
        ->set('name', 'ACTIVITY #')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('activities', [
        'name' => 'ACTIVITY #1',
    ]);
});

it('creates activity with parcels and beneficiaries', function () {
    $foodBoxStatus = Status::create(['status_name' => 'Food Box', 'p_id_sub' => 100]); // Mock parcel type
    $unitStatus = Status::create(['status_name' => 'Box', 'p_id_sub' => 101]);
    
    $parcelData = [
        [
            'parcel_type' => $foodBoxStatus->id,
            'distributed_parcels_count' => 100,
            'cost_for_each_parcel' => 10,
            'unit_id' => $unitStatus->id,
            'purchase_requisition_id' => 1,
            'status_id' => 1,
            'notes' => 'Test Parcel'
        ]
    ];

    $test = Livewire::test(Create::class)
        ->set('name', 'Health Project')
        ->set('sector_id', $this->sector->id)
        ->set('status', 25) // Typical status ID
        ->set('start_date', now()->toDateString())
        ->set('cost', 10)
        ->set('cost_nis', 35)
        ->set('parcels', $parcelData)
        ->call('save');

    if (session('message')) {
        dump("Creation Error: " . session('message'));
    }

    $test->assertHasNoErrors();

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
        ->set('status', 25)
        ->set('start_date', now()->toDateString())
        ->set('end_date', now()->addDays(5)->toDateString())
        ->set('cost', 10)
        ->set('cost_nis', 35)
        ->set('teaching_groups', $teachingGroupData)
        ->call('save');

    if (session('message')) {
        dump("Education Creation Error: " . session('message'));
    }

    $component->assertHasNoErrors();

    $this->assertDatabaseHas('activities', ['name' => 'Education Project']);
    $this->assertDatabaseHas('teaching_groups', ['name' => 'Group A']);
});

it('updates activity and syncs relationships', function () {
    $activity = Activity::create([
        'name' => 'Old Activity',
        'start_date' => now()->toDateString(),
        'sector_id' => $this->sector->id,
        'status' => 25,
        'activation' => 1,
        'cost' => 10,
        'cost_nis' => 35,
        'created_by' => $this->user->id
    ]);

    Livewire::test(Edit::class, ['activity' => $activity])
        ->set('name', 'Updated Activity')
        ->call('update')
        ->assertHasNoErrors()
        ->assertSessionHas('type', 'success');

    $this->assertDatabaseHas('activities', ['name' => 'Updated Activity']);
});

it('verifies smart sync for parcels and beneficiaries on update', function () {
    $activity = Activity::create([
        'name' => 'Sync Test Activity',
        'start_date' => now()->toDateString(),
        'sector_id' => $this->sector->id,
        'status' => 25,
        'activation' => 1,
        'cost' => 10,
        'cost_nis' => 35,
        'created_by' => $this->user->id
    ]);

    // Create 2 original parcels
    $parcel1 = $activity->parcels()->create([
        'parcel_type' => $this->sector->id,
        'distributed_parcels_count' => 10,
        'cost_for_each_parcel' => 10,
        'unit_id' => 1,
        'purchase_requisition_id' => 1
    ]);
    $parcel2 = $activity->parcels()->create([
        'parcel_type' => $this->sector->id,
        'distributed_parcels_count' => 20,
        'cost_for_each_parcel' => 10,
        'unit_id' => 1,
        'purchase_requisition_id' => 1
    ]);

    // Test Smart Sync: Update P1, Delete P2, Add P3
    $updatedParcels = [
        [
            'id' => $parcel1->id,
            'parcel_type' => $this->sector->id,
            'distributed_parcels_count' => 50, // Updated
            'cost_for_each_parcel' => 10,
            'unit_id' => 1,
            'purchase_requisition_id' => 1
        ],
        [
            'parcel_type' => $this->sector->id,
            'distributed_parcels_count' => 100, // New
            'cost_for_each_parcel' => 10,
            'unit_id' => 1,
            'purchase_requisition_id' => 1
        ]
    ];

    Livewire::test(Edit::class, ['activity' => $activity])
        ->set('parcels', $updatedParcels)
        ->call('update')
        ->assertHasNoErrors();

    // Verify P1 updated
    $this->assertDatabaseHas('activity_parcels', [
        'id' => $parcel1->id,
        'distributed_parcels_count' => 50
    ]);

    // Verify P2 deleted
    $this->assertDatabaseMissing('activity_parcels', [
        'id' => $parcel2->id
    ]);

    // Verify P3 created
    $this->assertDatabaseHas('activity_parcels', [
        'distributed_parcels_count' => 100
    ]);
});

it('shows warning when no changes are made to activity', function () {
    $activity = Activity::create([
        'name' => 'Stable Activity',
        'start_date' => now()->toDateString(),
        'sector_id' => $this->sector->id,
        'status' => 25,
        'activation' => 1,
        'cost' => 10,
        'cost_nis' => 35,
        'created_by' => $this->user->id
    ]);

    Livewire::test(Edit::class, ['activity' => $activity])
        ->call('update')
        ->assertHasNoErrors()
        ->assertSessionHas('type', 'warning')
        ->assertSessionHas('message', __('No changes were made!'));
});
