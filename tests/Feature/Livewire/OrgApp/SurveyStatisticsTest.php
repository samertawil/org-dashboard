<?php

use App\Models\User;
use App\Models\SurveyTable;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\Status;
use App\Models\StudentGroup;
use App\Models\Student;
use Livewire\Livewire;
use App\Livewire\OrgApp\SurveyQuestions\Statistics;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Create super admin user with ID 1 to pass gate checks
    $this->user = User::factory()->create(['id' => 1]);
    $this->actingAs($this->user);
});

it('correctly filters students and respondents by age limit of the selected survey', function () {
    // 1. Create a survey status (ID 144)
    $surveyStatus = Status::forceCreate([
        'id' => 144,
        'status_name' => 'Survey Age Test 6-12',
    ]);

    // 2. Create the survey table configuration for age 6 to 12
    $surveyTable = SurveyTable::forceCreate([
        'survey_for_section' => 144,
        'from_age' => 6,
        'to_age' => 12,
        'survey_name' => 'Survey Age Test 6-12',
        'is_active' => 1,
    ]);

    // 2.5 Create a survey question (so we can refer to it)
    $question = SurveyQuestion::forceCreate([
        'id' => 1,
        'question_ar_text' => 'Test Question',
        'answer_input_type' => 1,
        'survey_for_section' => 144,
        'batch_no' => 5,
    ]);

    // 3. Create a student group starting on 2026-01-01
    $group = StudentGroup::create([
        'name' => 'Test Group 1',
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'activation' => 1,
        'batch_no' => '5',
    ]);

    // 4. Create students with different age at join (as of 2026-01-01)
    // Student A: age 6 (born 2020-01-01) - ELIGIBLE
    $studentA = Student::create([
        'identity_number' => '111111111',
        'full_name' => 'Eligible Student 6yo',
        'birth_date' => '2020-01-01',
        'student_groups_id' => $group->id,
        'activation' => 1,
        'gender' => 'male',
    ]);

    // Student B: age 12 (born 2014-01-01) - ELIGIBLE
    $studentB = Student::create([
        'identity_number' => '222222222',
        'full_name' => 'Eligible Student 12yo',
        'birth_date' => '2014-01-01',
        'student_groups_id' => $group->id,
        'activation' => 1,
        'gender' => 'male',
    ]);

    // Student C: age 16 (born 2010-01-01) - INELIGIBLE (too old)
    $studentC = Student::create([
        'identity_number' => '333333333',
        'full_name' => 'Ineligible Student 16yo',
        'birth_date' => '2010-01-01',
        'student_groups_id' => $group->id,
        'activation' => 1,
        'gender' => 'male',
    ]);

    // 5. Add survey answers for all students
    SurveyAnswer::create([
        'survey_table_id' => $surveyTable->id,
        'account_id' => $studentA->identity_number,
        'survey_no' => 144,
        'question_id' => $question->id,
        'answer_ar_text' => '5',
    ]);
    SurveyAnswer::create([
        'survey_table_id' => $surveyTable->id,
        'account_id' => $studentB->identity_number,
        'survey_no' => 144,
        'question_id' => $question->id,
        'answer_ar_text' => '5',
    ]);
    SurveyAnswer::create([
        'survey_table_id' => $surveyTable->id,
        'account_id' => $studentC->identity_number,
        'survey_no' => 144,
        'question_id' => $question->id,
        'answer_ar_text' => '5',
    ]);

    // 6. Test the Livewire component statistics calculation
    $component = Livewire::test(Statistics::class)
        ->set('surveyNo', '144')
        ->set('batchNo', '5');

    $stats = $component->get('statsPerGroup');

    // Assert that we have statistics calculated for the group
    expect($stats)->not->toBeEmpty();
    
    // Find the group stats in the array
    $groupStats = collect($stats)->firstWhere('id', $group->id);
    expect($groupStats)->not->toBeNull();

    // Verify stats: 
    // - Total eligible students should be 2 (Student A and B, not C)
    // - Respondents should be 2 (Student A and B, not C)
    // - Rate should be 100% (2 / 2 * 100)
    expect($groupStats['total'])->toBe(2);
    expect($groupStats['respondents'])->toBe(2);
    expect($groupStats['rate'])->toBe(100.0);
});
