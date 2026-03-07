<?php

use App\Livewire\OrgApp\CampsResidents\Create;
use App\Livewire\OrgApp\CampsResidents\Edit;
use App\Livewire\OrgApp\CampsResidents\Index;
use App\Models\displacementCamp;
use App\Models\displacementCampResident;
use App\Models\Status;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    
    // Mock the Gate to allow these permissions instead of depending on a trait that might not exist on the User model
    Illuminate\Support\Facades\Gate::define('displacement.camps.index', fn($user) => true);
    Illuminate\Support\Facades\Gate::define('displacement.camps.create', fn($user) => true);
});

it('renders the camps residents index page successfully', function () {
    $this->actingAs($this->user);
    $response = $this->get(route('camps.residents.index'));
    $response->assertStatus(200);
});



it('renders the camps residents create component successfully', function () {
    $this->actingAs($this->user);
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('requires identity number, full name, camp, and status fields to create', function () {
    $this->actingAs($this->user);

    Livewire::test(Create::class)
        ->set('full_name', '')
        ->set('identity_number', '')
        ->set('displacement_camp_id', '')
        ->set('resident_type', '')
        ->call('save')
        ->assertHasErrors(['full_name' => 'required', 'identity_number' => 'required', 'displacement_camp_id' => 'required', 'resident_type' => 'required']);
});

it('can successfully initialize the create component with an empty state', function () {
    $this->actingAs($this->user);

    Livewire::test(Create::class)
        ->assertSet('full_name', '')
        ->assertSet('identity_number', '')
        ->assertSet('displacement_camp_id', '')
        ->assertSet('resident_type', '')
        ->assertSet('gender', '')
        ->assertSet('activation', 1); // 1 is default
});
