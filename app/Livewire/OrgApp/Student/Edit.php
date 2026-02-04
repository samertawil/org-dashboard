<?php

namespace App\Livewire\OrgApp\Student;

use Livewire\Component;
use App\Models\Student;
use App\Concerns\Student\StudentTrait;

class Edit extends Component
{
    use StudentTrait;

    public Student $student;
    public $enrollment_type;

    public function rules()
    {
        return [
            'identity_number' => 'required|integer|min_digits:9|max_digits:9|unique:students,identity_number,' . $this->student->id,
            'birth_date' => 'required|date|before_or_equal:' . Student::maxBirthDate() . '|after_or_equal:' . Student::minBirthDate(),
            'enrollment_type' => 'required|in:full_week,sat_mon_wed,sun_tue_thu', 
        ];
    }

    public function mount(Student $student)
    {
        $this->student = $student;
        $this->identity_number = $student->identity_number;
        $this->full_name = $student->full_name;
        $this->birth_date = $student->birth_date;
        $this->student_groups_id = $student->student_groups_id;
        $this->enrollment_type = $student->enrollment_type;
        $this->gender = $student->gender;
        $this->activation = $student->activation;
        $this->status_id = $student->status_id;
        $this->parent_phone = $student->parent_phone;
        $this->living_parent_id = $student->living_parent_id;
        $this->notes = $student->notes;
    }

    public function save()
    {
        $this->validate();

        $this->student->update([
            'identity_number' => $this->identity_number,
            'full_name' => $this->full_name,
            'birth_date' => $this->birth_date,
            'student_groups_id' => $this->student_groups_id ?: null,
            'enrollment_type' => $this->enrollment_type,
            'gender' => $this->gender,
            'activation' => $this->activation,
            'status_id' => $this->status_id ?: null,
            'parent_phone' => $this->parent_phone,
            'living_parent_id' => $this->living_parent_id ?: null,
            'notes' => $this->notes,
            'updated_by' => auth()->id(),
        ]);

        session()->flash('message', __('Student successfully updated.'));

        return $this->redirect(route('student.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.org-app.student.edit', [
            'heading' => __('Edit Student'),
            'type' => 'save',
        ]);
    }
}
