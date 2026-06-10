<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Status;
use App\Models\SurveyQuestion;
use App\Models\SurveyAnswer;
use App\Models\TeacherStudentGroup;
use Livewire\Livewire;
use App\Livewire\OrgApp\SurveyAnswers\Index;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = User::factory()->create(['id' => 1]);
    $this->actingAs($this->user);

    // Setup gate for index and create/edit/delete permissions
    Gate::define('survey.index', fn () => true);
    Gate::define('survey.create', fn () => true);
});

it('groups answers by student and survey in the main listing', function () {
    $section = Status::forceCreate([
        'id' => 460,
        'status_name' => 'Section Test Modal A',
    ]);

    $student = Student::create([
        'identity_number' => 123400001,
        'full_name' => 'Test Student One',
        'birth_date' => '2018-01-01',
        'gender' => 1,
        'enrollment_type' => 'full_week',
    ]);

    $question1 = SurveyQuestion::forceCreate([
        'question_ar_text' => 'Question A',
        'survey_for_section' => $section->id,
        'answer_input_type' => 1,
        'batch_no' => 1,
    ]);

    $question2 = SurveyQuestion::forceCreate([
        'question_ar_text' => 'Question B',
        'survey_for_section' => $section->id,
        'answer_input_type' => 1,
        'batch_no' => 1,
    ]);

    SurveyAnswer::forceCreate([
        'account_id' => $student->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question1->id,
        'answer_ar_text' => 'Ans A',
    ]);

    SurveyAnswer::forceCreate([
        'account_id' => $student->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question2->id,
        'answer_ar_text' => 'Ans B',
    ]);

    // Test listing groups
    $component = Livewire::test(Index::class)
        ->set('searchAccountId', $student->identity_number)
        ->set('readyToLoad', true);

    $answersListing = $component->get('answers');
    
    // It should group the two answers into 1 row
    expect($answersListing->total())->toBe(1);
    expect((int) $answersListing->first()->account_id)->toBe($student->identity_number);
    expect($answersListing->first()->survey_no)->toBe($section->id);
});

it('opens answers modal and loads individual answers', function () {
    $section = Status::forceCreate([
        'id' => 461,
        'status_name' => 'Section Test Modal B',
    ]);

    $student = Student::create([
        'identity_number' => 123400002,
        'full_name' => 'Test Student Two',
        'birth_date' => '2018-01-01',
        'gender' => 1,
        'enrollment_type' => 'full_week',
    ]);

    $question = SurveyQuestion::forceCreate([
        'question_ar_text' => 'Question in modal',
        'survey_for_section' => $section->id,
        'answer_input_type' => 1,
        'batch_no' => 1,
    ]);

    $answer = SurveyAnswer::forceCreate([
        'account_id' => $student->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question->id,
        'answer_ar_text' => 'Original Answer',
    ]);

    $component = Livewire::test(Index::class)
        ->call('openAnswersModal', $student->identity_number, $section->id)
        ->assertSet('showAnswersModal', true)
        ->assertSet('selectedAccountId', $student->identity_number)
        ->assertSet('selectedSurveyNo', $section->id);

    $selectedAnswers = $component->get('selectedSurveyAnswers');
    expect($selectedAnswers->count())->toBe(1);
    expect($selectedAnswers->first()->id)->toBe($answer->id);
});

it('allows inline editing and resolves labels correctly', function () {
    $section = Status::forceCreate([
        'id' => 462,
        'status_name' => 'Section Test Modal C',
    ]);

    $student = Student::create([
        'identity_number' => 123400003,
        'full_name' => 'Test Student Three',
        'birth_date' => '2018-01-01',
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

    Livewire::test(Index::class)
        ->call('openAnswersModal', $student->identity_number, $section->id)
        ->call('startEditAnswer', $answer->id)
        ->assertSet('editingAnswerId', $answer->id)
        ->assertSet('editingAnswerText', 'yes')
        ->set('editingAnswerText', 'no')
        ->call('saveAnswer')
        ->assertSet('editingAnswerId', null)
        ->assertSet('editingAnswerText', '');

    $answer->refresh();
    expect($answer->answer_ar_text)->toBe('no');
    expect($answer->answer_label)->toBe('لا');
});

it('allows deleting an individual answer in the modal', function () {
    $section = Status::forceCreate([
        'id' => 463,
        'status_name' => 'Section Test Modal D',
    ]);

    $student = Student::create([
        'identity_number' => 123400004,
        'full_name' => 'Test Student Four',
        'birth_date' => '2018-01-01',
        'gender' => 1,
        'enrollment_type' => 'full_week',
    ]);

    $question = SurveyQuestion::forceCreate([
        'question_ar_text' => 'Question to delete',
        'survey_for_section' => $section->id,
        'answer_input_type' => 1,
        'batch_no' => 1,
    ]);

    $answer = SurveyAnswer::forceCreate([
        'account_id' => $student->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question->id,
        'answer_ar_text' => 'Answer to delete',
    ]);

    Livewire::test(Index::class)
        ->call('deleteAnswer', $answer->id);

    expect(SurveyAnswer::find($answer->id))->toBeNull();
});

it('allows deleting the entire survey group', function () {
    $section = Status::forceCreate([
        'id' => 464,
        'status_name' => 'Section Test Modal E',
    ]);

    $student = Student::create([
        'identity_number' => 123400005,
        'full_name' => 'Test Student Five',
        'birth_date' => '2018-01-01',
        'gender' => 1,
        'enrollment_type' => 'full_week',
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
        'answer_ar_text' => 'Ans A',
    ]);

    $answer2 = SurveyAnswer::forceCreate([
        'account_id' => $student->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question2->id,
        'answer_ar_text' => 'Ans B',
    ]);

    Livewire::test(Index::class)
        ->call('deleteSurveyGroup', $student->identity_number, $section->id);

    expect(SurveyAnswer::find($answer1->id))->toBeNull();
    expect(SurveyAnswer::find($answer2->id))->toBeNull();
});

it('enforces permission-based filtering on survey answers', function () {
    Cache::flush();

    // Create supervisor status first to avoid FK constraint violation
    Status::forceCreate([
        'id' => 167,
        'status_name' => 'Supervisor',
    ]);

    // 1. Create two employees/users
    $teacherUserA = User::factory()->create(['id' => 101, 'activation' => 1]);
    $employeeA = \App\Models\Employee::forceCreate([
        'user_id' => $teacherUserA->id,
        'employee_number' => 'EMPA',
        'full_name' => 'Teacher A',
        'email' => $teacherUserA->email,
        'phone' => '1234567891',
        'gender' => 1,
        'activation' => 1,
    ]);

    $teacherUserB = User::factory()->create(['id' => 102, 'activation' => 1]);
    $employeeB = \App\Models\Employee::forceCreate([
        'user_id' => $teacherUserB->id,
        'employee_number' => 'EMPB',
        'full_name' => 'Teacher B',
        'email' => $teacherUserB->email,
        'phone' => '1234567892',
        'gender' => 2,
        'activation' => 1,
    ]);

    // 2. Create student groups
    $group1 = \App\Models\StudentGroup::create([
        'name' => 'Group One',
        'batch_no' => 11,
    ]);
    $group2 = \App\Models\StudentGroup::create([
        'name' => 'Group Two',
        'batch_no' => 12,
    ]);

    // Link Teacher A to Group 1
    $employeeA->studentGroups()->attach($group1->id);

    // Link Teacher B to Group 2
    $employeeB->studentGroups()->attach($group2->id);

    // 3. Create students
    $student1 = Student::create([
        'identity_number' => 111111111,
        'full_name' => 'Student in Group One',
        'birth_date' => '2018-01-01',
        'gender' => 1,
        'enrollment_type' => 'full_week',
        'student_groups_id' => $group1->id,
    ]);

    $student2 = Student::create([
        'identity_number' => 222222222,
        'full_name' => 'Student in Group Two',
        'birth_date' => '2018-01-01',
        'gender' => 1,
        'enrollment_type' => 'full_week',
        'student_groups_id' => $group2->id,
    ]);

    // 4. Create survey answers
    $section = Status::forceCreate([
        'id' => 470,
        'status_name' => 'Section Test Perm',
    ]);
    $question = SurveyQuestion::forceCreate([
        'question_ar_text' => 'Q',
        'survey_for_section' => $section->id,
        'answer_input_type' => 1,
        'batch_no' => 11,
    ]);

    // Answer created by Teacher A for Student 1 (Group 1)
    $answerA = SurveyAnswer::forceCreate([
        'account_id' => $student1->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question->id,
        'answer_ar_text' => 'Ans by A',
        'created_by' => $employeeA->id,
    ]);

    // Answer created by Teacher B for Student 1 (Group 1)
    $answerB = SurveyAnswer::forceCreate([
        'account_id' => $student1->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question->id,
        'answer_ar_text' => 'Ans by B',
        'created_by' => $employeeB->id,
    ]);

    // Answer created by Teacher B for Student 2 (Group 2)
    $answerC = SurveyAnswer::forceCreate([
        'account_id' => $student2->identity_number,
        'survey_no' => $section->id,
        'question_id' => $question->id,
        'answer_ar_text' => 'Ans by B for student 2',
        'created_by' => $employeeB->id,
    ]);

    // 5. Test Teacher A:
    // Regular teacher without supervisor role and without select.any.student.
    // Teacher A belongs to Group 1, so they can see Student 1's records.
    // But since they are a regular teacher (not supervisor/coordinator), they can ONLY see answers created by themselves (employeeA->id).
    // They cannot see Student 2's records (different group).
    Gate::define('select.any.student', fn() => false);
    $this->actingAs($teacherUserA);
    
    $component = Livewire::test(Index::class)
        ->set('filterGroup', $group1->id)
        ->set('readyToLoad', true);

    $answersListing = $component->get('answers');
    expect($answersListing->total())->toBe(1);
    
    // Now make Teacher A a supervisor (coordinator) of Group 1
    TeacherStudentGroup::create([
        'teacher_id' => $teacherUserA->id,
        'student_group_id' => $group1->id,
        'job_title' => 167, // Supervisor/Coordinator
    ]);

    // Re-test as Supervisor of Group 1
    $component = Livewire::test(Index::class)
        ->set('filterGroup', $group1->id)
        ->set('readyToLoad', true);

    $answersListing = $component->get('answers');
    expect($answersListing->total())->toBe(1);
    
    // 6. Test select.any.student permission
    Gate::define('select.any.student', fn() => true);
    $component = Livewire::test(Index::class)
        ->set('readyToLoad', true)
        ->set('filterBatch', 12); // Group 2 batch
    
    $answersListing = $component->get('answers');
    expect($answersListing->total())->toBe(1); // Can see student 2 (Group 2) now because of select.any.student
});
