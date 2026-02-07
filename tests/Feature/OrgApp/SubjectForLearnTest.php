<?php

use App\Livewire\OrgApp\SubjectForLearn\Create;
use App\Models\Status;
use App\Models\StudentSubjectForLearn;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    // Subject type seed (p_id_sub = 63 from SubjectForLearnTrait)
    $this->subjectType = Status::create(['status_name' => 'Core', 'p_id_sub' => 63]);
});

it('renders the create subject page', function () {
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

it('validates age range logic', function () {
    Livewire::test(Create::class)
        ->set('from_age', 5) // Too low (min 6)
        ->set('to_age', 13) // Too high (max 12)
        ->call('save')
        ->assertHasErrors(['from_age', 'to_age']);

    Livewire::test(Create::class)
        ->set('from_age', 10)
        ->set('to_age', 8) // to_age < from_age
        ->call('save')
        ->assertHasErrors(['to_age']);
});

it('creates a subject for learn', function () {
    Livewire::test(Create::class)
        ->set('name', 'mathematics')
        ->set('type_id', $this->subjectType->id)
        ->set('description', 'Basic math')
        ->set('from_age', 6)
        ->set('to_age', 12)
        ->set('activation', 1)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('student_subject_for_learns', [
        'name' => 'Mathematics', // Expecting ucfirst
        'from_age' => 6,
        'to_age' => 12,
    ]);
});
