<?php

namespace App\Livewire\OrgApp\Student;


use App\Models\Student;
use App\Reposotries\StudentRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{

    public $studentData;
    public $lateSurveyStudentData;
    public $showGradingScale = false;


public function mount($student)
{
    $user = auth()->user();

    // نستخدم الـ Scope للتأكد من أن المدرس له وصول لمجموعة هذا الطالب بالتحديد
    $this->studentData = Student::visibleToTeacher($user)->find($student);

    if (! $this->studentData) {
        abort(403, 'ليس لديك صلاحية للوصول لبيانات هذا الطالب.');
    }

    $this->lateSurveyStudentData = StudentRepo::studentSurveyLate($this->studentData->id);
}

 
    #[Computed()]
    public function studentGradingScale() {
        return  StudentRepo::studentGradingScaleTablesAll($this->studentData->identity_number);    
    }

    public function render()
    {

//     dd(StudentRepo::studentGradingScaleTablesAll(43725788));
//    dd(StudentRepo::studentGradingScaleTablesAll(43239543));
 
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
