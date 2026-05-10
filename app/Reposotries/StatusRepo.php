<?php

namespace App\Reposotries;
use App\Models\Status;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatusRepo  
{
    public static function statuses()
    {
        return Cache::rememberForever('statuses-all', function () {
            return Status::select('id', 'status_name', 'p_id', 'p_id_sub','description')->get();
        }); 
    }

    public static function studentBySurveyByAge(Student $student) {

        if($student ) {
            return DB::table('statuses')
             ->where('p_id_sub',config('appConstant.survey_for'))
             ->join('survey_table', 'statuses.id', '=', 'survey_table.survey_for_section')
             ->where('survey_table.from_age', '<=', $student->student_age_when_join)
             ->where('survey_table.to_age', '>=', $student->student_age_when_join)
             ->select('statuses.id','statuses.status_name', 'survey_table.from_age', 'survey_table.to_age')
             ->get();
           
         }

    }
}