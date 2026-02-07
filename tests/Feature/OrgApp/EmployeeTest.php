<?php

use App\Livewire\OrgApp\Employee\Create;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Enums\GlobalSystemConstant;
use Livewire\Livewire;

use App\Models\Status;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Seed Statuses for dropdowns
    // Config values from appConstant:
    // 'maritalStatuses'=>1
    // 'regions'=>6
    // 'positions_in_organization'=>12
    // 'hire_types'=>19

    $this->maritalStatus = Status::create(['status_name' => 'Single', 'p_id_sub' => 1]);
    $this->region = Status::create(['status_name' => 'Gaza', 'p_id_sub' => 6]);
    $this->position = Status::create(['status_name' => 'Developer', 'p_id_sub' => 12]);
    $this->hireType = Status::create(['status_name' => 'Contract', 'p_id_sub' => 19]);
});

it('renders the create employee page', function () {
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('validates required fields', function () {
    Livewire::test(Create::class)
        ->call('save')
        ->assertHasErrors([
            'employee_number' => 'required',
            'full_name' => 'required',
            'gender' => 'required',
        ]);
});

it('validates unique fields', function () {
    Employee::create([
        'employee_number' => '12345',
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'gender' => 2,
        'activation' => 1,
    ]);

    Livewire::test(Create::class)
        ->set('employee_number', '12345')
        ->set('full_name', 'John Doe')
        ->set('email', 'john@example.com')
        ->call('save')
        ->assertHasErrors([
            'employee_number' => 'unique',
            'full_name' => 'unique',
            'email' => 'unique',
        ]);
});

it('creates an employee', function () {
    $department = Department::create(['name' => 'IT']);
    $user = User::factory()->create();

    // Gender enum in migration is [2, 3] (Male, Female)
    $gender = 2; 

    Livewire::test(Create::class)
        ->set('employee_number', 'EMP001')
        ->set('full_name', 'alice smith')
        ->set('email', 'alice@example.com')
        ->set('gender', $gender)
        ->set('marital_status', $this->maritalStatus->id)
        ->set('regions', $this->region->id)
        ->set('position', $this->position->id)
        ->set('type_of_employee_hire', $this->hireType->id)
        ->set('department_id', $department->id)
        ->set('user_id', $user->id)
        ->set('phone', '1234567890')
        ->set('activation', 1)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('employees', [
        'employee_number' => 'EMP001',
        'full_name' => 'Alice smith', // Expecting ucfirst on full_name if implemented, checking logic...
        'email' => 'alice@example.com',
        'department_id' => $department->id,
    ]);
});
