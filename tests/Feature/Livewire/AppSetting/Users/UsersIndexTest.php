<?php

use App\Livewire\AppSetting\Users\Index;
use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use Livewire\Livewire;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->admin = User::factory()->create(['id' => 1]); // Superadmin
    $this->actingAs($this->admin);

    // Bypass gate checks or define permission
    Gate::define('user.index', fn () => true);
});

it('renders the users index page successfully', function () {
    Livewire::test(Index::class)
        ->assertStatus(200);
});

it('sets selected employee and opens modal when user has employee details', function () {
    $user = User::factory()->create();
    $employee = Employee::create([
        'employee_number' => 'EMP007',
        'full_name' => 'James Bond',
        'email' => 'bond@example.com',
        'gender' => 2,
        'activation' => 1,
        'user_id' => $user->id,
    ]);

    Livewire::test(Index::class)
        ->assertSet('showEmployeeModal', false)
        ->assertSet('selectedEmployeeId', null)
        ->call('showEmployee', $user->id)
        ->assertSet('showEmployeeModal', true)
        ->assertSet('selectedEmployeeId', $employee->id)
        ->assertSee('James Bond')
        ->assertSee('#EMP007');
});

it('does not open modal and shows session message when user has no employee details', function () {
    $userWithoutEmployee = User::factory()->create();

    Livewire::test(Index::class)
        ->call('showEmployee', $userWithoutEmployee->id)
        ->assertSet('showEmployeeModal', false)
        ->assertSet('selectedEmployeeId', null)
        ->assertSee(__('This user does not have employee details.'));
});

it('clears selected employee and closes modal when calling closeEmployeeModal', function () {
    $user = User::factory()->create();
    $employee = Employee::create([
        'employee_number' => 'EMP008',
        'full_name' => 'Sherlock Holmes',
        'email' => 'holmes@example.com',
        'gender' => 2,
        'activation' => 1,
        'user_id' => $user->id,
    ]);

    Livewire::test(Index::class)
        ->call('showEmployee', $user->id)
        ->assertSet('showEmployeeModal', true)
        ->assertSet('selectedEmployeeId', $employee->id)
        ->call('closeEmployeeModal')
        ->assertSet('showEmployeeModal', false)
        ->assertSet('selectedEmployeeId', null);
});

it('sets selected user roles and opens roles modal when view roles button is clicked', function () {
    $user = User::factory()->create(['name' => 'John Watson']);
    $role = Role::create([
        'name' => 'Medical Officer',
        'abilities' => ['view-medical'],
        'abilities_description' => ['View medical reports']
    ]);
    
    // Attach role
    $user->rolesRelation()->attach($role->id, ['granted_by' => 1]);

    Livewire::test(Index::class)
        ->assertSet('showRolesModal', false)
        ->assertSet('selectedUserIdForRoles', null)
        ->call('showUserRoles', $user->id)
        ->assertSet('showRolesModal', true)
        ->assertSet('selectedUserIdForRoles', $user->id)
        ->assertSee('John Watson')
        ->assertSee('Medical Officer')
        ->assertSee('View medical reports');
});

it('clears selected user roles and closes modal when calling closeRolesModal', function () {
    $user = User::factory()->create(['name' => 'John Watson']);
    $role = Role::create([
        'name' => 'Medical Officer',
        'abilities' => ['view-medical'],
        'abilities_description' => ['View medical reports']
    ]);
    $user->rolesRelation()->attach($role->id, ['granted_by' => 1]);

    Livewire::test(Index::class)
        ->call('showUserRoles', $user->id)
        ->assertSet('showRolesModal', true)
        ->assertSet('selectedUserIdForRoles', $user->id)
        ->call('closeRolesModal')
        ->assertSet('showRolesModal', false)
        ->assertSet('selectedUserIdForRoles', null);
});

it('filters users list by assigned role', function () {
    $roleA = Role::create([
        'name' => 'Role A',
        'abilities' => ['ability-a'],
        'abilities_description' => ['Ability A']
    ]);
    $roleB = Role::create([
        'name' => 'Role B',
        'abilities' => ['ability-b'],
        'abilities_description' => ['Ability B']
    ]);

    $userA = User::factory()->create(['name' => 'User with Role A']);
    $userB = User::factory()->create(['name' => 'User with Role B']);

    $userA->rolesRelation()->attach($roleA->id, ['granted_by' => 1]);
    $userB->rolesRelation()->attach($roleB->id, ['granted_by' => 1]);

    Livewire::test(Index::class)
        ->assertSee('User with Role A')
        ->assertSee('User with Role B')
        ->set('searchRole', $roleA->id)
        ->assertSee('User with Role A')
        ->assertDontSee('User with Role B')
        ->set('searchRole', $roleB->id)
        ->assertSee('User with Role B')
        ->assertDontSee('User with Role A');
});
