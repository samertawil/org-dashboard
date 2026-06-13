<?php

use App\Livewire\OrgApp\Reports\ActivityOverview;
use App\Livewire\OrgApp\Reports\GroupsAttendance;
use App\Models\Activity;
use App\Models\Region;
use App\Models\Status;
use App\Models\StudentGroup;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);

    $this->status = Status::create(['status_name' => 'Completed', 'p_id' => 1]);
    $this->region = Region::create(['region_name' => 'Gaza']);
});

it('renders activity overview report', function () {
    Livewire::test(ActivityOverview::class)
        ->assertStatus(200);
});

it('filters activity overview by region', function () {
    Activity::create([
        'name' => 'Activity in Gaza',
        'start_date' => now()->toDateString(),
        'region' => $this->region->id,
        'created_by' => $this->user->id,
        'status' => $this->status->id,
        'activation' => 1,
    ]);

    $otherRegion = Region::create(['region_name' => 'Other']);
    Activity::create([
        'name' => 'Activity in Other',
        'start_date' => now()->toDateString(),
        'region' => $otherRegion->id,
        'created_by' => $this->user->id,
        'status' => $this->status->id,
        'activation' => 1,
    ]);

    Livewire::test(ActivityOverview::class)
        ->set('selectedRegion', $this->region->id)
        ->assertViewHas('activities', function ($activities) {
            return $activities->count() === 1 && $activities->first()->name === 'Activity in Gaza';
        });
});

it('calculates KPIs correctly in activity overview', function () {
    Activity::create([
        'name' => 'Expensive Activity',
        'start_date' => now()->toDateString(),
        'cost' => 1000,
        'created_by' => $this->user->id,
        'status' => $this->status->id, // Complete
        'activation' => 1,
    ]);

    Livewire::test(ActivityOverview::class)
        ->assertViewHas('kpis', function ($kpis) {
            return $kpis['totalActivities'] === 1 && $kpis['totalBudget'] == 1000;
        });
});

it('renders groups attendance report', function () {
    StudentGroup::create([
        'name' => 'Reporting Group',
        'max_students' => 20,
        'activation' => 1,
        'batch_no' => '1',
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ]);

    Livewire::test(GroupsAttendance::class)
        ->assertStatus(200)
        ->assertSee('Reporting Group');
});

it('supports lazy loading of groups attendance', function () {
    StudentGroup::create([
        'name' => 'Reporting Group Lazy',
        'max_students' => 20,
        'activation' => 1,
        'batch_no' => '1',
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ]);

    // When isLazy is true and loadData is false, it should show the trigger button but not the group names
    Livewire::test(GroupsAttendance::class, ['isLazy' => true, 'loadData' => false])
        ->assertStatus(200)
        ->assertSee('عرض حضور المجموعات')
        ->assertDontSee('Reporting Group Lazy')
        ->assertViewHas('groups', function ($groups) {
            return $groups->isEmpty();
        })
        // Set loadData to true
        ->set('loadData', true)
        ->assertSee('Reporting Group Lazy')
        ->assertViewHas('groups', function ($groups) {
            return $groups->isNotEmpty();
        });
});
