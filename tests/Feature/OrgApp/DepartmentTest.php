<?php

use App\Livewire\OrgApp\Department\Create;
use App\Models\Department;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders the create department page', function () {
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('validates required fields', function () {
    Livewire::test(Create::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

it('validates unique name', function () {
    Department::create(['name' => 'IT Department']);

    Livewire::test(Create::class)
        ->set('name', 'IT Department')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

it('creates a department', function () {
    Livewire::test(Create::class)
        ->set('name', 'HR Department')
        ->set('location', 'Building A')
        ->set('description', 'Human Resources')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('departments', [
        'name' => 'HR Department',
        'location' => 'Building A',
    ]);
});

it('formats location and name', function () {
    Livewire::test(Create::class)
        ->set('name', 'accounting')
        ->assertSet('name', 'Accounting') // Expects capitalization
        ->set('location', 'office b')
        ->assertSet('location', 'Office b'); // Expects capitalization
});
