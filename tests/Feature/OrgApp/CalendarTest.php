<?php

use App\Livewire\OrgApp\Calendar\Index;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\Event;
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
        'gender' => 2,
    ]);
    $this->status = \App\Models\Status::create([
        'status_name' => 'Planned',
        'p_id' => 1,
    ]);
    actingAs($this->user);
});

it('renders the calendar component', function () {
    Livewire::test(Index::class)
        ->assertStatus(200);
});

it('loads events and activities into the calendar', function () {
    Event::create([
        'title' => 'Test Event',
        'start' => now(),
        'end' => now()->addHour(),
        'created_by' => $this->user->id,
    ]);

    Activity::create([
        'name' => 'Test Activity',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'activation' => 1,
        'created_by' => $this->user->id,
        'status' => $this->status->id,
    ]);

    Livewire::test(Index::class)
        ->assertSet('events', function ($events) {
            $hasEvent = collect($events)->contains(fn($e) => str_contains($e['title'], 'Test Event'));
            $hasActivity = collect($events)->contains(fn($e) => str_contains($e['title'], 'Test Activity'));
            return $hasEvent && $hasActivity;
        });
});

it('creates a new event with assignees', function () {
    Livewire::test(Index::class)
        ->set('title', 'New Meeting')
        ->set('start', now()->format('Y-m-d\TH:i'))
        ->set('assignees', [
            ['employee_id' => $this->employee->id, 'notes' => 'Be there']
        ])
        ->call('saveEvent')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('events', ['title' => 'New Meeting']);
    $this->assertDatabaseHas('event_assignees', [
        'employee_id' => $this->employee->id,
        'notes' => 'Be there'
    ]);
});

it('updates an event via drag and drop', function () {
    $event = Event::create([
        'title' => 'Old Event',
        'start' => now(),
        'end' => now()->addHour(),
        'created_by' => $this->user->id,
    ]);

    $newStart = now()->addDay();
    $newEnd = now()->addDay()->addHour();

    Livewire::test(Index::class)
        ->call('updateEventDrop', $event->id, $newStart->toIso8601String(), $newEnd->toIso8601String(), false);

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'start' => $newStart->format('Y-m-d H:i:s'),
    ]);
});

it('deletes an event', function () {
    $event = Event::create([
        'title' => 'To be deleted',
        'start' => now(),
        'created_by' => $this->user->id,
    ]);

    Livewire::test(Index::class)
        ->set('event_id', $event->id)
        ->call('deleteEvent');

    $this->assertDatabaseMissing('events', ['id' => $event->id]);
});
