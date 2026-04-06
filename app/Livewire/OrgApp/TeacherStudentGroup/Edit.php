<?php

namespace App\Livewire\OrgApp\TeacherStudentGroup;

use App\Models\Employee;
use App\Models\StudentGroup;
use App\Models\TeacherStudentGroup;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Edit extends Component
{
    public TeacherStudentGroup $mapping;
    public $teacher_id;
    public $student_group_id;

    public function rules()
    {
        return [
            'teacher_id' => 'required',
            'student_group_id' => 'required',
        ];
    }

    public function mount(TeacherStudentGroup $teacherStudentGroup)
    {
        $this->mapping = $teacherStudentGroup;
        $this->teacher_id = $teacherStudentGroup->teacher_id;
        $this->student_group_id = $teacherStudentGroup->student_group_id;
    }

    public function edit()
    {
        $this->validate();

        $this->mapping->update([
            'teacher_id' => $this->teacher_id,
            'student_group_id' => $this->student_group_id,
        ]);

        session()->flash('message', __('Assignment updated successfully.'));
        return redirect()->route('teacher-student-groups.index');
    }

    public function render()
    {
        if (Gate::denies('teacher-student-groups.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        
        return view('livewire.org-app.teacher-student-group.edit', [
            'heading' => __('Edit Assignment'),
            'type' => 'edit',
            'employees' => Employee::whereNotNull('user_id')->with('user')->get(),
            'student_groups' => StudentGroup::all(),
        ]);
    }
}
