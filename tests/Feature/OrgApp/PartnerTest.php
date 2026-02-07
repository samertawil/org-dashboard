<?php

use App\Livewire\OrgApp\Partner\Create;
use App\Models\PartnerInstitution;
use App\Models\Status;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    // Partner type seed (p_id_sub = 52 from appConstant)
    $this->partnerType = Status::create(['status_name' => 'NGO', 'p_id_sub' => 52]);
});

it('renders the create partner page', function () {
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('validates required fields', function () {
    Livewire::test(Create::class)
        ->set('activation', '') // Override default value to test validation
        ->call('save')
        ->assertHasErrors([
            'name' => 'required',
            'activation' => 'required',
        ]);
});

it('validates unique name', function () {
    PartnerInstitution::create([
        'name' => 'Existing Partner',
        'activation' => 1,
    ]);

    Livewire::test(Create::class)
        ->set('name', 'Existing Partner')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

it('validates email format', function () {
    Livewire::test(Create::class)
        ->set('email', 'invalid-email')
        ->call('save')
        ->assertHasErrors(['email' => 'email']);
});

it('creates a partner institution and capitalizes names', function () {
    Livewire::test(Create::class)
        ->set('name', 'new partner')
        ->set('manager_name', 'john doe')
        ->set('type_id', $this->partnerType->id)
        ->set('phone', '123456')
        ->set('email', 'partner@example.com')
        ->set('website', 'www.example.com')
        ->set('activation', 1)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('partner_institutions', [
        'name' => 'New partner', // Expecting ucfirst
        'manager_name' => 'John doe', // Expecting ucfirst
        'email' => 'partner@example.com',
    ]);
});
