<?php

use App\Livewire\OrgApp\Dashboard\MyTasks;
use App\Models\Employee;
use App\Models\Event;
use App\Models\EventAssignee;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->employee = Employee::create([
        'user_id' => $this->user->id,
        'full_name' => 'John Doe',
        'employee_number' => 'EMP001',
        'date_of_birth' => '1990-01-01',
        'phone' => '0599000000',
        'email' => $this->user->email,
        'activation' => 1,
        'gender' => 2, // Male
    ]);
    actingAs($this->user);
});

it('renders the my tasks component', function () {
    Livewire::test(MyTasks::class)
        ->assertStatus(200);
});

it('loads only tasks assigned to the current employee', function () {
    $event = Event::create([
        'title' => 'Test Event',
        'start' => now(),
        'end' => now()->addHour(),
        'created_by' => $this->user->id,
    ]);

    $assignedTask = EventAssignee::create([
        'event_id' => $event->id,
        'employee_id' => $this->employee->id,
        'status' => 'pending',
        'assigned_by' => $this->user->id,
    ]);

    $otherUser = User::factory()->create();
    $otherEmployee = Employee::create([
        'user_id' => $otherUser->id,
        'full_name' => 'Other Employee',
        'employee_number' => 'EMP002',
        'date_of_birth' => '1990-01-01',
        'phone' => '0599000001',
        'email' => $otherUser->email,
        'activation' => 1,
        'gender' => 3, // Female
    ]);

    $otherTask = EventAssignee::create([
        'event_id' => $event->id,
        'employee_id' => $otherEmployee->id,
        'status' => 'pending',
        'assigned_by' => $this->user->id,
    ]);

    Livewire::test(MyTasks::class)
        ->assertViewHas('tasks', function ($tasks) use ($assignedTask, $otherTask) {
            return $tasks->contains($assignedTask) && !$tasks->contains($otherTask);
        });
});

it('updates task status', function () {
    $event = Event::create([
        'title' => 'Test Event',
        'start' => now(),
        'end' => now()->addHour(),
        'created_by' => $this->user->id,
    ]);

    $task = EventAssignee::create([
        'event_id' => $event->id,
        'employee_id' => $this->employee->id,
        'status' => 'pending',
        'assigned_by' => $this->user->id,
    ]);

    Livewire::test(MyTasks::class)
        ->set("responses.{$task->id}", 'Status updated to completed')
        ->call('updateStatus', $task->id, 'completed')
        ->assertDispatched('task-updated');

    $this->assertDatabaseHas('event_assignees', [
        'id' => $task->id,
        'status' => 'completed',
        'response' => 'Status updated to completed',
    ]);
});
