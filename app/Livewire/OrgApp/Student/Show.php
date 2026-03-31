<?php

namespace App\Livewire\OrgApp\Student;


use App\Reposotries\StudentRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Show extends Component
{
    // public Student $student;
    public $studentData;
    public $lateSurveyStudentData;

    public function mount($student)
    {
        $this->studentData = StudentRepo::studentsWithRelations()->find($student);
        $this->lateSurveyStudentData = StudentRepo::studentSurveyLate($this->studentData->id);

        if (! $this->studentData) {
            abort(404, 'Student not found.');
        }
    }

    // public function mount(Student $student)
    // {
    //     $this->student = $student->load(['surveyStudentanswers.question','group:id,status_name']);
    // }

    // #[Computed()]
    // public function studentsAgeWhenJoin() {
    //     return StudentRepo::students()->find($this->Student->id)->student_age_when_join;
    // }

    public function render()
    {
    
        if (Gate::denies('student.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.student.show', [
                'student' => $this->studentData,
                'lateSurveyStudentData' => $this->lateSurveyStudentData,
            ]
        );
    }
}
