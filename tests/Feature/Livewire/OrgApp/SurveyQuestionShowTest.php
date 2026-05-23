<?php

use App\Models\User;
use App\Models\SurveyTable;
use App\Models\SurveyQuestion;
use App\Models\Status;
use Livewire\Livewire;
use App\Livewire\OrgApp\SurveyQuestions\Show;

beforeEach(function () {
    // Create a user with ID 1 to ensure isSuperAdmin() returns true and passes permission gates
    $this->user = User::factory()->create(['id' => 1]);
    $this->actingAs($this->user);
});

it('renders the survey tree show page', function () {
    Livewire::test(Show::class)
        ->assertStatus(200)
        ->assertSee(__('Integrated Survey Structure'));
});

it('can filter surveys by section and target', function () {
    // Create status lookup values
    $section = Status::forceCreate([
        'id' => 301,
        'status_name' => 'Section Test X',
    ]);
    
    $target = Status::forceCreate([
        'id' => 302,
        'status_name' => 'Target Test Y',
    ]);
    
    // Create a survey table record
    $survey = SurveyTable::forceCreate([
        'survey_name' => 'Survey Spec ABC',
        'survey_for_section' => $section->id,
        'survey_target' => $target->id,
        'is_active' => 1,
        'from_age' => 5,
        'to_age' => 10,
    ]);

    Livewire::test(Show::class)
        ->set('selectedSection', $section->id)
        ->set('selectedTarget', $target->id)
        ->assertSee('Survey Spec ABC')
        ->assertSee('Section Test X')
        ->assertSee('Target Test Y');
});
