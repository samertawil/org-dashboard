<?php

namespace App\Reposotries;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StudentRepo
{
  public static function students()
  {
    return Cache::rememberForever('StudentData-all', function () {
      return  Student::get();
    });
  }

  public static function studentsWithRelations()
  {
    return Cache::rememberForever('StudentData-all-with-relations', function () {
      return  Student::with(['surveyStudentanswers.question', 'group:id,status_name', 'surveyStudentanswers', 'surveyStudentanswers.surveyfor'])->get();
    });
  }



  public static function studentsSurveyLate()
  {
    return DB::select("
      SELECT DISTINCT
          s.id, s.identity_number, s.full_name, s.student_groups_id,
          st.id AS survey_table_id, st.survey_for_section, stat.status_name as section_name, st.semester, s.activation
      FROM survey_table st
      JOIN students s ON s.student_groups_id IS NOT NULL
      JOIN student_groups sg ON s.student_groups_id = sg.id
      LEFT JOIN statuses stat ON st.survey_for_section = stat.id
      LEFT JOIN survey_answers sa 
          ON s.identity_number = sa.account_id 
         AND sa.survey_no = st.survey_for_section
      WHERE TIMESTAMPDIFF(YEAR, s.birth_date, sg.start_date) 
          BETWEEN COALESCE(st.from_age, 0) AND COALESCE(st.to_age, 999)
        AND (
              st.semester IN (0, 1)
              OR (st.semester = 2 AND CURDATE() BETWEEN sg.start_date AND sg.end_date)
              OR (st.semester = 3 
                  AND CURDATE() >= DATE_SUB(sg.end_date, INTERVAL 14 DAY)
                  AND CURDATE() <= sg.end_date)
            )
        AND sa.account_id IS NULL
    ");
  }

  public static function studentSurveyLate($studentId)
  {
    return DB::select("
      SELECT DISTINCT
          s.id, s.identity_number, s.full_name, s.student_groups_id,
          st.id AS survey_table_id, st.survey_for_section, stat.status_name as section_name, st.semester, s.activation
      FROM survey_table st
      JOIN students s ON s.student_groups_id IS NOT NULL
      JOIN student_groups sg ON s.student_groups_id = sg.id
      LEFT JOIN statuses stat ON st.survey_for_section = stat.id
      LEFT JOIN survey_answers sa 
          ON s.identity_number = sa.account_id 
         AND sa.survey_no = st.survey_for_section
      WHERE TIMESTAMPDIFF(YEAR, s.birth_date, sg.start_date) 
          BETWEEN COALESCE(st.from_age, 0) AND COALESCE(st.to_age, 999)
        AND (
              st.semester IN (0, 1)
              OR (st.semester = 2 AND CURDATE() BETWEEN sg.start_date AND sg.end_date)
              OR (st.semester = 3 
                  AND CURDATE() >= DATE_SUB(sg.end_date, INTERVAL 14 DAY)
                  AND CURDATE() <= sg.end_date)
            )
        AND sa.account_id IS NULL
        AND s.id = ?
    ", [$studentId]);
  }
}
