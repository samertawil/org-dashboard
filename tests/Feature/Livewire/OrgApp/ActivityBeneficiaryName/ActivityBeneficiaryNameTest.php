<?php

use App\Livewire\OrgApp\ActivityBeneficiaryName\Create;
use App\Livewire\OrgApp\ActivityBeneficiaryName\Edit;
use App\Livewire\OrgApp\ActivityBeneficiaryName\Index;
use App\Models\Activity;
use App\Models\activityBeneficiaryName;
use App\Models\displacementCamp;
use App\Models\Status;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    
    // Mock the Gate to allow these permissions to bypass authorization layers
    Illuminate\Support\Facades\Gate::define('activity.beneficiaries.index', fn($user) => true);
    Illuminate\Support\Facades\Gate::define('activity.beneficiaries.create', fn($user) => true);
});

it('renders the activity beneficiaries index page successfully', function () {
    $this->actingAs($this->user);
    $response = $this->get(route('activity.beneficiaries.index'));
    $response->assertStatus(200);
});

it('renders the activity beneficiaries create component successfully', function () {
    $this->actingAs($this->user);
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('requires activity_id, identity number, full name, and receipt date to create', function () {
    $this->actingAs($this->user);

    Livewire::test(Create::class)
        ->set('activity_id', '')
        ->set('full_name', '')
        ->set('identity_number', '')
        ->set('receipt_date', '')
        ->call('save')
        ->assertHasErrors(['activity_id' => 'required', 'full_name' => 'required', 'identity_number' => 'required', 'receipt_date' => 'required']);
});

it('can successfully initialize the create component with an empty state', function () {
    $this->actingAs($this->user);

    Livewire::test(Create::class)
        ->assertSet('activity_id', '')
        ->assertSet('full_name', '')
        ->assertSet('identity_number', '')
        ->assertSet('displacement_camps_id', '')
        ->assertSet('receipt_date', '')
        ->assertSet('receive_method', '')
        ->assertSet('receive_by_name', '')
        ->assertSet('phone', '');
});
