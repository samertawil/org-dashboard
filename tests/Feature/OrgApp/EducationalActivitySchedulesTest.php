<?php

use App\Livewire\OrgApp\EducationalActivitySchedules\Index;
use App\Models\User;
use App\Models\StudentGroup;
use App\Models\Employee;
use Livewire\Livewire;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Gate::define('educational-activity-schedules.index', fn() => true);
    
    // Create standard active student groups
    $this->group1 = StudentGroup::create([
        'name' => 'Group A',
        'batch_no' => '3',
        'activation' => 1,
        'start_date' => now()->subDays(5),
        'end_date' => now()->addDays(5),
    ]);

    $this->group2 = StudentGroup::create([
        'name' => 'Group B',
        'batch_no' => '4',
        'activation' => 1,
        'start_date' => now()->subDays(5),
        'end_date' => now()->addDays(5),
    ]);

    $this->adminUser = User::factory()->create(['id' => 1, 'activation' => 1]);
    $this->teacherUser = User::factory()->create(['id' => 200, 'activation' => 1]);

    $this->employee = Employee::create([
        'user_id' => $this->teacherUser->id,
        'full_name' => 'Teacher User',
        'employee_number' => 'EMP200',
        'date_of_birth' => '1990-01-01',
        'phone' => '0599000200',
        'email' => $this->teacherUser->email,
        'activation' => 1,
        'gender' => 2,
    ]);

    // Assign teacher user to Group A
    $this->teacherUser->teacher()->create([
        'student_group_id' => $this->group1->id,
    ]);
});

it('renders the educational activity schedules index page for admin', function () {
    actingAs($this->adminUser);

    $component = Livewire::test(Index::class)
        ->assertStatus(200);

    $groups = $component->instance()->availableGroups;
    expect($groups->contains($this->group1))->toBeTrue();
    expect($groups->contains($this->group2))->toBeTrue();
});

it('renders the educational activity schedules index page for teacher and restricts available groups', function () {
    actingAs($this->teacherUser);

    $component = Livewire::test(Index::class)
        ->assertStatus(200);

    $groups = $component->instance()->availableGroups;
    expect($groups->contains($this->group1))->toBeTrue();
    expect($groups->contains($this->group2))->toBeFalse();
});
