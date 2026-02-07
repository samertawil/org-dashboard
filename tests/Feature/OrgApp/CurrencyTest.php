<?php

use App\Livewire\OrgApp\Currency\Create;
use App\Models\CurrancyValue;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders the create currency page', function () {
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('validates required fields', function () {
    Livewire::test(Create::class)
        ->call('save')
        ->assertHasErrors([
            'exchange_date' => 'required',
            'currency_value' => 'required',
        ]);
});

it('validates unique exchange date', function () {
    CurrancyValue::create([
        'exchange_date' => '2023-01-01',
        'currency_value' => 3.5,
    ]);

    Livewire::test(Create::class)
        ->set('exchange_date', '2023-01-01')
        ->call('save')
        ->assertHasErrors(['exchange_date' => 'unique']);
});

it('validates currency value is numeric', function () {
    Livewire::test(Create::class)
        ->set('currency_value', 'abc')
        ->call('save')
        ->assertHasErrors(['currency_value' => 'numeric']);
});

it('creates a currency value', function () {
    Livewire::test(Create::class)
        ->set('exchange_date', '2023-01-02')
        ->set('currency_value', 3.6)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('currancy_values', [
        'exchange_date' => '2023-01-02',
        'currency_value' => 3.6,
    ]);
});
