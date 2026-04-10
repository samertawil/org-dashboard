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

  public static function studentsGradingScaleTables()
  {


    $subQuery = DB::table('survey_answers as a')
      ->selectRaw(
        "a.account_id,
        SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) AS total_marks,
        SUM(q.max_score) AS max_total_score,
        ROUND(SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) / SUM(q.max_score) * 100) AS grade,
        q.domain_id,
        MIN(a.survey_no) AS survey_no"
      )
      ->join('survey_questions as q', 'a.question_id', '=', 'q.id')
      ->whereNotNull('q.domain_id')
      ->where('q.survey_for_section', '!=', 120)
      ->whereNotNull('q.min_score')
      ->whereNotNull('q.max_score')
      ->groupBy('a.account_id', 'q.domain_id');


    $result = DB::query()->fromSub($subQuery, 'grades')
      ->join('survey_grading_scale_tables as g', function ($join) {
        $join->on(DB::raw('grades.grade'), '>=', 'g.from_percentage')
          ->on(DB::raw('grades.grade'), '<=', 'g.to_percentage');
      })
      ->select(
        'grades.account_id',
        'grades.total_marks',
        'grades.max_total_score',
        'grades.grade',
        'grades.domain_id',
        'grades.survey_no',
        'g.evaluation',
        'g.description'
      )

      ->orderBy('grades.account_id')
      ->orderBy('grades.domain_id')

      ->get();

    return $result;
  }

  public static function studentGradingScaleTables($studentId)
  {

    $subQuery = DB::table('survey_answers as a')
      ->selectRaw(
        "a.account_id,
        SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) AS total_marks,
        SUM(q.max_score) AS max_total_score,
        ROUND(SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) / SUM(q.max_score) * 100) AS grade,
        q.domain_id,
        MIN(a.survey_no) AS survey_no"
      )
      ->where('a.account_id', $studentId)
      ->join('survey_questions as q', 'a.question_id', '=', 'q.id')
      ->whereNotNull('q.domain_id')
      ->where('q.survey_for_section', '!=', 120)
      ->whereNotNull('q.min_score')
      ->whereNotNull('q.max_score')
      ->groupBy('a.account_id', 'q.domain_id', 'a.survey_no');

    $result = DB::query()->fromSub($subQuery, 'grades')
      ->join('survey_grading_scale_tables as g', function ($join) {
        $join->on(DB::raw('grades.grade'), '>=', 'g.from_percentage')
          ->on(DB::raw('grades.grade'), '<=', 'g.to_percentage');
      })
      ->leftJoin('statuses as s', 'grades.survey_no', '=', 's.id')
      ->select(
        'grades.account_id',
        'grades.total_marks',
        'grades.max_total_score',
        'grades.grade',
        'grades.domain_id',
        'grades.survey_no',
        'g.evaluation',
        'g.description',
        's.status_name as survey_name'
      )
      ->orderBy('grades.account_id')
      ->orderBy('grades.domain_id')
      ->get();

    return $result;
  }
  public static function studentGradingScaleTablesAll($studentId = null)
  {
      // Base query (no GROUP BY, no domain filter)
      $baseSub = DB::table('survey_answers as a')
          ->join('survey_questions as q', 'a.question_id', '=', 'q.id')
          ->join('students as st', 'st.identity_number', '=', 'a.account_id')
          ->join('student_groups as sgp', 'sgp.id', '=', 'st.student_groups_id')
          ->where('q.survey_for_section', '!=', 120)
          ->whereNotNull('q.min_score')
          ->whereNotNull('q.max_score')
          ->whereColumn('q.batch_no', 'sgp.batch_no');  // يجب أن يكون للـ batch أسئلة فعليًا
  
      if ($studentId) {
          // EARLY filter – best for performance
          $baseSub->where('a.account_id', $studentId);
      }
  
      // ---------- First subquery: with domain ----------
      $firstSub = clone $baseSub;
      $firstSub->selectRaw('
              a.account_id as account_id,
              SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) as total_marks,
              SUM(q.max_score) as max_total_score,
              ROUND(SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) / SUM(q.max_score) * 100, 0) as grade,
              q.domain_id as domain_id,
              MIN(a.survey_no) as survey_no,
              sgp.batch_no as batch_no,
              q.survey_for_section
          ')
          ->whereNotNull('q.domain_id')
          ->groupBy(
              'a.account_id',
              'q.domain_id',
              'a.survey_no',
              'sgp.batch_no',
              'q.survey_for_section'
          );
  
      $firstQuery = DB::query()->fromSub($firstSub, 'grades')
          ->join('survey_grading_scale_tables as g', function ($join) {
              $join->on(DB::raw('grades.grade'), '>=', 'g.from_percentage')
                   ->on(DB::raw('grades.grade'), '<=', 'g.to_percentage')
                   ->where('g.type', '=', 150)
                   ->whereColumn('g.batch_no', 'grades.batch_no')
                   ->whereColumn('g.survey_for_section', 'grades.survey_for_section');
          })
          ->leftJoin('statuses as s', 'grades.survey_no', '=', 's.id')
          ->selectRaw('
              grades.account_id,
              grades.total_marks,
              grades.max_total_score,
              grades.grade,
              grades.domain_id,
              grades.survey_no,
              g.evaluation,
              g.description,
              s.status_name
          ');
  
      // ---------- Second subquery: all questions, no domain ----------
      $secondSub = clone $baseSub;
      $secondSub->selectRaw('
              a.account_id as account_id,
              SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) as total_marks,
              SUM(q.max_score) as max_total_score,
              ROUND(SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) / SUM(q.max_score) * 100, 0) as grade,
              NULL as domain_id,
              MIN(a.survey_no) as survey_no,
              sgp.batch_no as batch_no,
              q.survey_for_section
          ')
          ->groupBy(
              'a.account_id',
              'a.survey_no',
              'sgp.batch_no',
              'q.survey_for_section'
          );
  
      $secondQuery = DB::query()->fromSub($secondSub, 'grades')
          ->join('survey_grading_scale_tables as g', function ($join) {
              $join->on(DB::raw('grades.grade'), '>=', 'g.from_percentage')
                   ->on(DB::raw('grades.grade'), '<=', 'g.to_percentage')
                   ->where('g.type', '=', 151)
                   ->whereColumn('g.batch_no', 'grades.batch_no')
                   ->whereColumn('g.survey_for_section', 'grades.survey_for_section');
          })
          ->leftJoin('statuses as s', 'grades.survey_no', '=', 's.id')
          ->selectRaw('
              grades.account_id,
              grades.total_marks,
              grades.max_total_score,
              grades.grade,
              grades.domain_id,
              grades.survey_no,
              g.evaluation,
              g.description,
              s.status_name
          ');
  
      // UNION + ordering
      return $firstQuery
          ->union($secondQuery)
          ->orderBy('account_id')
          ->orderBy('domain_id')
          ->get();
  }
  public static function studentsNames()
  {
    $user = auth()->user();
    return Student::visibleToTeacher($user)->get();
  }

 
}
