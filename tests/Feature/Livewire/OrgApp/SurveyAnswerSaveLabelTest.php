<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Status;
use App\Models\SurveyQuestion;
use App\Models\SurveyAnswer;
use Livewire\Livewire;
use App\Livewire\OrgApp\SurveyAnswers\Create;
use App\Livewire\OrgApp\SurveyAnswers\Edit;

beforeEach(function () {
    $this->user = User::factory()->create(['id' => 1]);
    $this->actingAs($this->user);
});

it('resolves and saves answer_label when saving survey answers', function () {
    // 1. Create a section status
    $section = Status::forceCreate([
        'id' => 450,
        'status_name' => 'Section Test A',
    ]);

    // Create student group
    $group = \App\Models\StudentGroup::create([
        'name' => 'Test Group',
        'batch_no' => 1,
    ]);

    // 2. Create student
    $student = Student::create([
        'identity_number' => 123456789,
        'full_name' => 'John Doe',
        'birth_date' => now()->subYears(8)->format('Y-m-d'),
        'gender' => 1,
        'enrollment_type' => 'full_week',
        'student_groups_id' => $group->id,
    ]);

    // 3. Create survey questions
    $question1 = SurveyQuestion::forceCreate([
        'question_ar_text' => 'Question 1 with options',
        'survey_for_section' => $section->id,
        'answer_input_type' => 2, // Multiple choice
        'answer_options' => [
            ['value' => '1', 'label' => 'Option One'],
            ['value' => '2', 'label' => 'Option Two'],
        ],
        'batch_no' => 1,
    ]);

    $question2 = SurveyQuestion::forceCreate([
        'question_ar_text' => 'Question 2 without options',
        'survey_for_section' => $section->id,
        'answer_input_type' => 1, // Short text
        'answer_options' => null,
        'batch_no' => 1,
    ]);

    // 4. Test Livewire component
    Livewire::test(Create::class)
        ->set('account_id', $student->identity_number)
        ->set('surveyForSection', $section->id)
        ->set('answers', [
            $question1->id => '2', // Option Two
            $question2->id => 'Free Text Response',
        ])
        ->call('save')
        ->assertHasNoErrors();

    // 5. Assert database records
    $answer1 = SurveyAnswer::where('question_id', $question1->id)->where('account_id', $student->identity_number)->first();
    $answer2 = SurveyAnswer::where('question_id', $question2->id)->where('account_id', $student->identity_number)->first();

    expect($answer1)->not->toBeNull();
    expect($answer1->answer_ar_text)->toBe('2');
    expect($answer1->answer_label)->toBe('Option Two');

    expect($answer2)->not->toBeNull();
    expect($answer2->answer_ar_text)->toBe('Free Text Response');
    expect($answer2->answer_label)->toBeNull();
});

it('resolves and saves answer_label on single edit save', function () {
    $section = Status::forceCreate([
        'id' => 451,
        'status_name' => 'Section Test B',
    ]);

    $student = Student::create([
        'identity_number' => 987654321,
        'full_name' => 'Jane Doe',
        'birth_date' => now()->subYears(8)->format('Y-m-d'),
        'gender' => 1,
        'enrollment_type' => 'full_week',
    ]);

    $question = SurveyQuestion::forceCreate([
        'question_ar_text' => 'Question with options',
        'survey_for_section' => $section->id,
        'answer_input_type' => 2,
        'answer_options' => [
            ['value' => 'yes', 'label' => 'نعم'],
            ['value' => 'no', 'label' => 'لا'],
        ],
        'batch_no' => 1,
    ]);

    $answer = SurveyAnswer::forceCreate([
        'account_id' => $student->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question->id,
        'answer_ar_text' => 'yes',
        'answer_label' => 'نعم',
    ]);

    Livewire::test(Edit::class, ['surveyAnswer' => $answer])
        ->set('answer_ar_text', 'no')
        ->call('save')
        ->assertHasNoErrors();

    $answer->refresh();
    expect($answer->refresh()->answer_ar_text)->toBe('no');
    expect($answer->answer_label)->toBe('لا');
});

it('exports survey answers and groups repeated student metadata correctly', function () {
    $section = Status::forceCreate([
        'id' => 452,
        'status_name' => 'Section Test Export',
    ]);

    $group = \App\Models\StudentGroup::create([
        'name' => 'Export Group',
        'batch_no' => 1,
    ]);

    $student = Student::create([
        'identity_number' => 777888999,
        'full_name' => 'Export Student',
        'birth_date' => now()->subYears(8)->format('Y-m-d'),
        'gender' => 1,
        'enrollment_type' => 'full_week',
        'student_groups_id' => $group->id,
    ]);

    $question1 = SurveyQuestion::forceCreate([
        'question_ar_text' => 'Q1',
        'survey_for_section' => $section->id,
        'answer_input_type' => 1,
        'batch_no' => 1,
    ]);

    $question2 = SurveyQuestion::forceCreate([
        'question_ar_text' => 'Q2',
        'survey_for_section' => $section->id,
        'answer_input_type' => 1,
        'batch_no' => 1,
    ]);

    $answer1 = SurveyAnswer::forceCreate([
        'account_id' => $student->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question1->id,
        'answer_ar_text' => 'Ans 1',
    ]);

    $answer2 = SurveyAnswer::forceCreate([
        'account_id' => $student->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question2->id,
        'answer_ar_text' => 'Ans 2',
    ]);

    // Load relationships as done in the export
    $answer1->load(['student.studentGroup', 'surveyfor', 'question', 'creator']);
    $answer2->load(['student.studentGroup', 'surveyfor', 'question', 'creator']);

    $export = new \App\Exports\SurveyAnswersExport($section->id, 1, $group->id);

    $row1 = $export->map($answer1);
    $row2 = $export->map($answer2);

    // Row 1 should have full details
    expect($row1[0])->toBe('Section Test Export');
    expect($row1[1])->toBe(777888999);
    expect($row1[2])->toBe('Export Student');
    expect($row1[3])->toBe('Export Group');
    expect($row1[4])->toBe('N/A');
    expect($row1[5])->toBe('Q1');
    expect($row1[6])->toBe('Ans 1');

    // Row 2 is for the same student and survey, so student metadata should be blank
    expect($row2[0])->toBe('');
    expect($row2[1])->toBe('');
    expect($row2[2])->toBe('');
    expect($row2[3])->toBe('');
    expect($row2[4])->toBe('N/A');
    expect($row2[5])->toBe('Q2');
    expect($row2[6])->toBe('Ans 2');
});

it('ExportFiles component correctly lists accessible batches and groups using AccessibleGroupsTrait', function () {
    // Clear caches that might be used by repositories
    \Illuminate\Support\Facades\Cache::flush();

    // Create student groups with different batch numbers
    $group1 = \App\Models\StudentGroup::create([
        'name' => 'Group Batch 10',
        'batch_no' => 10,
    ]);

    $group2 = \App\Models\StudentGroup::create([
        'name' => 'Group Batch 20',
        'batch_no' => 20,
    ]);

    // Create a regular user who is an employee/teacher and link group1 to it
    $teacherUser = User::factory()->create(['activation' => 1]);
    
    // Assign permission so they don't get 403 (Gate::denies('survey.export'))
    // Let's check how permissions/abilities work for users. 
    // Superadmin has ID 1, but we can assign roles/abilities or we can just mock the Gate if needed.
    // Let's see: user has no roles by default. Let's see how permissions are checked in policies or tests.
    // In our beforeEach, $this->user has ID 1, which makes them Super Admin.
    // But we need to act as a regular teacher who has permission to export.
    // Let's see if we can define Gate 'survey.export' to return true.
    Gate::define('survey.export', function () {
        return true;
    });

    $employee = \App\Models\Employee::forceCreate([
        'user_id' => $teacherUser->id,
        'employee_number' => 'EMP123',
        'full_name' => 'Teacher Name',
        'email' => $teacherUser->email,
        'phone' => '1234567890',
        'gender' => 2,
        'activation' => 1,
    ]);

    // Link group1 to employee via pivot table
    $employee->studentGroups()->attach($group1->id);

    // Act as the teacher
    $this->actingAs($teacherUser);

    // Test Livewire component
    $test = Livewire::test(\App\Livewire\OrgApp\SurveyQuestions\ExportFiles::class);
    
    // The teacher should only see batch 10 and not batch 20
    $test->assertViewHas('batchNumbers', function ($batches) {
        $array = is_array($batches) ? $batches : (is_object($batches) && method_exists($batches, 'toArray') ? $batches->toArray() : (array) $batches);
        return in_array(10, $array) && !in_array(20, $array);
    });

    // The teacher should only have Group Batch 10 as accessible groups
    $test->assertViewHas('groupNames', function ($groups) use ($group1, $group2) {
        $ids = $groups->pluck('id')->toArray();
        return in_array($group1->id, $ids) && !in_array($group2->id, $ids);
    });
});
