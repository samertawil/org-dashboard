<?php

namespace App\Livewire\OrgApp\Student;

use Livewire\Component;

use App\Models\Student;

class Show extends Component
{
    public Student $student;

    public function mount(Student $student)
    {
        $this->student = $student->load(['surveyStudentanswers.question','group:id,status_name']);
    }

    public function render()
    {
        return view('livewire.org-app.student.show');
    }
}
