<?php

use App\Livewire\OrgApp\Dashboard\Index;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/dashboard')->assertOk();
});

test('dashboard detects activity.index permission and displays activity cards', function () {
    $user = User::factory()->create(['id' => 999, 'activation' => 1]);
    $this->actingAs($user);

    Gate::define('activity.index', fn() => true);
    Gate::define('displacement.camps.index', fn() => true);

    Livewire::test(Index::class)
        ->assertSee(__('Total Activities'));
});

test('dashboard detects lack of activity.index permission and hides activity cards', function () {
    $user = User::factory()->create(['id' => 999, 'activation' => 1]);
    $this->actingAs($user);

    Gate::define('activity.index', fn() => false);
    Gate::define('displacement.camps.index', fn() => false);

    Livewire::test(Index::class)
        ->assertDontSee(__('Total Activities'));
});