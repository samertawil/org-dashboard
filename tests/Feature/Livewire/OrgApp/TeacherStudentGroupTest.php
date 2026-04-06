<?php

use App\Models\User;
use App\Models\Employee;
use App\Models\StudentGroup;
use App\Models\TeacherStudentGroup;
use Livewire\Livewire;
use App\Livewire\OrgApp\TeacherStudentGroup\Index;
use App\Livewire\OrgApp\TeacherStudentGroup\Create;
use App\Livewire\OrgApp\TeacherStudentGroup\Edit;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders the teacher student group index page', function () {
    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertSee(__('Teacher Student Groups'));
});

it('can create a teacher student group assignment', function () {
    // We may need foreign keys like Status but let's suppress that if not strictly required, or create them.
    $teacherUser = User::factory()->create();
    $employee = Employee::forceCreate([
        'user_id' => $teacherUser->id,
        'employee_number' => 'EMP-' . uniqid(),
        'full_name' => 'Test Employee ' . uniqid(),
        'gender' => '2',
        'activation' => '1',
    ]);

    $studentGroup = StudentGroup::forceCreate([
        'name' => 'Group A'
    ]);

    Livewire::test(Create::class)
        ->set('teacher_id', $teacherUser->id)
        ->set('student_group_id', $studentGroup->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('teacher-student-groups.index'));

    $this->assertDatabaseHas('teacher_student_group', [
        'teacher_id' => $teacherUser->id,
        'student_group_id' => $studentGroup->id,
    ]);
});

it('can edit a teacher student group assignment', function () {
    $teacherUser = User::factory()->create();
    $employee = Employee::forceCreate([
        'user_id' => $teacherUser->id,
        'employee_number' => 'EMP-' . uniqid(),
        'full_name' => 'Test Employee ' . uniqid(),
        'gender' => '2',
        'activation' => '1',
    ]);
    
    $studentGroup = StudentGroup::forceCreate([
        'name' => 'Group A'
    ]);

    $mapping = TeacherStudentGroup::forceCreate([
        'teacher_id' => $teacherUser->id,
        'student_group_id' => $studentGroup->id,
    ]);

    $newStudentGroup = StudentGroup::forceCreate([
        'name' => 'Group B'
    ]);

    Livewire::test(Edit::class, ['teacherStudentGroup' => $mapping])
        ->set('student_group_id', $newStudentGroup->id)
        ->call('edit')
        ->assertHasNoErrors()
        ->assertRedirect(route('teacher-student-groups.index'));

    $this->assertDatabaseHas('teacher_student_group', [
        'id' => $mapping->id,
        'student_group_id' => $newStudentGroup->id,
    ]);
});

it('can delete a teacher student group assignment', function () {
    $teacherUser = User::factory()->create();
    $employee = Employee::forceCreate([
        'user_id' => $teacherUser->id,
        'employee_number' => 'EMP-' . uniqid(),
        'full_name' => 'Test Employee ' . uniqid(),
        'gender' => '2',
        'activation' => '1',
    ]);

    $studentGroup = StudentGroup::forceCreate([
        'name' => 'Group A'
    ]);

    $mapping = TeacherStudentGroup::forceCreate([
        'teacher_id' => $teacherUser->id,
        'student_group_id' => $studentGroup->id,
    ]);

    Livewire::test(Index::class)
        ->call('delete', $mapping->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('teacher_student_group', [
        'id' => $mapping->id,
    ]);
});
