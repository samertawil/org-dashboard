<?php

namespace App\Reposotries;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;

class StudentRepo
{  
    public static function students() {
       return Cache::rememberForever('StudentData-all', function () {
          return  Student::get();
        });
    }

    public static function studentsWithRelations() {
        return Cache::rememberForever('StudentData-all-with-relations', function () {
           return  Student::with(['surveyStudentanswers.question','group:id,status_name','surveyStudentanswers'])->get();
         });
     }

     
}
