<?php

use App\Models\User;
use App\Models\SurveyGradingScaleTable;
use App\Models\Status;
use Livewire\Livewire;
use App\Livewire\OrgApp\SurveyQuestions\GradingScale\Index as GradingScaleIndex;

beforeEach(function () {
    // Create a user with ID 1 to ensure isSuperAdmin() returns true and passes permission gates
    $this->user = User::factory()->create(['id' => 1]);
    $this->actingAs($this->user);

    // Create status lookup values
    $this->section = Status::forceCreate([
        'id' => 401,
        'status_name' => 'Section Test Alpha',
    ]);

    // Create a grading scale record
    $this->gradingScale = SurveyGradingScaleTable::forceCreate([
        'evaluation' => 'Evaluation Scale Spec Beta',
        'description' => 'Test description beta',
        'from_percentage' => 80,
        'to_percentage' => 90,
        'batch_no' => '5',
        'survey_for_section' => $this->section->id,
    ]);
});

it('renders the survey grading scale index page', function () {
    Livewire::test(GradingScaleIndex::class)
        ->assertStatus(200)
        ->assertSee(__('Survey Grading Scales'))
        ->assertSee(__('Table'))
        ->assertSee(__('Tree View'));
});

it('can switch between table and tree views', function () {
    Livewire::test(GradingScaleIndex::class)
        ->assertSet('viewType', 'table')
        ->set('viewType', 'tree')
        ->assertSet('viewType', 'tree')
        ->assertSee(__('Expand All'))
        ->assertSee(__('Collapse All'));
});

it('can filter survey grading scale tree by batch and section', function () {
    Livewire::test(GradingScaleIndex::class)
        ->set('viewType', 'tree')
        ->set('searchSection', $this->section->id)
        ->set('searchBatch', '5')
        ->assertSee('Section Test Alpha')
        ->assertSee('Evaluation Scale Spec Beta');
});
