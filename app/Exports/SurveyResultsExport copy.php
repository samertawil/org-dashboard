<?php
// app/Exports/SurveyResultsExport.php - الحل الكامل المُصحح
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class SurveyResultsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $surveyNo;
    protected $groupId;

    public function __construct($surveyNo = null, $groupId = null)
    {
        $this->surveyNo = $surveyNo;
        $this->groupId = $groupId;
    }

    public function query()
    {
        // Debugging logs
        \Illuminate\Support\Facades\Log::info("SurveyResultsExport for No: {$this->surveyNo}, Group: {$this->groupId}");
        
        // 1. Subquery RAW (الأساسي)
        $rawSub = DB::table('afsc.survey_answers as a')
            ->join('afsc.survey_questions as q', 'a.question_id', '=', 'q.id')
            ->where('q.survey_for_section', '!=', 120)
            ->whereNotNull('q.min_score')
            ->whereNotNull('q.max_score')
            ->select(
                'a.account_id',
                DB::raw('SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) AS total_marks'),
                DB::raw('SUM(q.max_score) AS max_total_score'),
                DB::raw('ROUND(SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) / SUM(q.max_score) * 100, 0) AS grade'),
                'q.domain_id',
                'a.survey_no',
                DB::raw('MIN(a.created_by) AS created_by')
            )
            ->when($this->surveyNo, function ($query) {
                $query->where('a.survey_no', $this->surveyNo);
            })
            ->groupBy('a.account_id', 'q.domain_id', 'a.survey_no');

        // 2. Subquery G مع الـ joins
        $gSub = DB::table('afsc.survey_grading_scale_tables as sg')
            ->rightJoinSub($rawSub, 'raw', function($join) {
                $join->on('raw.grade', '>=', 'sg.from_percentage')
                     ->on('raw.grade', '<=', 'sg.to_percentage')
                     ->where('sg.type', '=', 150);
            })
            ->leftJoin('afsc.statuses as s', 'raw.survey_no', '=', 's.id')
            ->leftJoin('afsc.students as st', 'st.identity_number', '=', 'raw.account_id')
            ->leftJoin('afsc.student_groups as ep', 'ep.id', '=', 'st.student_groups_id')
            ->leftJoin('afsc.employees as e', 'e.id', '=', 'raw.created_by')
            ->when($this->groupId, function ($query) {
                $query->where('st.student_groups_id', $this->groupId);
            })
            ->select(
                'raw.account_id',
                'raw.total_marks',
                'raw.grade',
                'raw.domain_id',
                'raw.survey_no',
                'sg.evaluation',
                's.status_name',
                'st.full_name',
                'ep.name as education_point_name',
                'e.full_name as teacher_name'
            );

        // 3. Subquery P (الـ Pivot الرئيسي)
        $pQuery = DB::table('afsc.survey_answers') // base doesn't matter, will be replaced by fromSub
            ->fromSub($gSub, 'g')
            ->groupBy('g.account_id', 'g.full_name', 'g.education_point_name', 'g.teacher_name', 'g.survey_no')
            ->select([
                'g.account_id',
                'g.full_name',
                'g.education_point_name',
                'g.teacher_name',
                'g.survey_no',
                DB::raw('MAX(CASE WHEN g.domain_id = 146 THEN g.total_marks END) AS درجات_البعد_العاطفي_الانفعالي'),
                DB::raw('MAX(CASE WHEN g.domain_id = 146 THEN g.evaluation END) AS تقييم_البعد_العاطفي_الانفعالي'),
                DB::raw('MAX(CASE WHEN g.domain_id = 147 THEN g.total_marks END) AS درجات_البعد_النفسي_والعقلي'),
                DB::raw('MAX(CASE WHEN g.domain_id = 147 THEN g.evaluation END) AS تقييم_البعد_النفسي_والعقلي'),
                DB::raw('MAX(CASE WHEN g.domain_id = 148 THEN g.total_marks END) AS درجات_البعد_الجسدي_والاجتماعي'),
                DB::raw('MAX(CASE WHEN g.domain_id = 148 THEN g.evaluation END) AS تقييم_البعد_الجسدي_والاجتماعي'),
                DB::raw('COALESCE(MAX(CASE WHEN g.domain_id = 146 THEN g.total_marks END), 0) + 
                         COALESCE(MAX(CASE WHEN g.domain_id = 147 THEN g.total_marks END), 0) + 
                         COALESCE(MAX(CASE WHEN g.domain_id = 148 THEN g.total_marks END), 0) AS المجموع_الكلي'),
                DB::raw('MAX(g.status_name) AS survey_name')
            ]);

        // 4. Subquery TOT (التقييم الكلي - مُصحح)
        $tSub = DB::table('afsc.survey_answers as a')
            ->join('afsc.survey_questions as q', 'a.question_id', '=', 'q.id')
            ->where('q.survey_for_section', '!=', 120)
            ->whereNotNull('q.min_score')
            ->whereNotNull('q.max_score')
            ->select([
                'a.account_id',
                'a.survey_no',
                DB::raw('ROUND(SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) / SUM(q.max_score) * 100, 0) AS total_grade')
            ])
            ->when($this->surveyNo, function ($query) {
                $query->where('a.survey_no', $this->surveyNo);
            })
            ->groupBy('a.account_id', 'a.survey_no');

        $totQuery = DB::table('afsc.survey_grading_scale_tables as sg2')
            ->rightJoinSub($tSub, 't', function($join) {
                $join->on('t.total_grade', '>=', 'sg2.from_percentage')
                     ->on('t.total_grade', '<=', 'sg2.to_percentage')
                     ->where('sg2.type', '=', 151);
            })
            ->select([
                't.account_id',
                't.survey_no',
                DB::raw('COALESCE(sg2.evaluation, "غير محدد") AS evaluation')
            ]);

        // 5. الاستعلام الرئيسي النهائي
        return DB::table('p_alias') // basic placeholder
            ->fromSub($pQuery, 'p')
            ->leftJoinSub($totQuery, 'tot', function($join) {
                $join->on('tot.account_id', '=', 'p.account_id')
                     ->on('tot.survey_no', '=', 'p.survey_no');
            })
            ->select([
                'p.account_id',
                'p.full_name',
                'p.education_point_name',
                'p.teacher_name',
                'p.survey_no',
                'p.درجات_البعد_العاطفي_الانفعالي',
                'p.تقييم_البعد_العاطفي_الانفعالي',
                'p.درجات_البعد_النفسي_والعقلي',
                'p.تقييم_البعد_النفسي_والعقلي',
                'p.درجات_البعد_الجسدي_والاجتماعي',
                'p.تقييم_البعد_الجسدي_والاجتماعي',
                'p.المجموع_الكلي',
                'tot.evaluation AS التقييم_الكلي',
                'p.survey_name AS اسم_الاستبيان'
            ])
            ->when($this->surveyNo, function ($query) {
                $query->where('p.survey_no', $this->surveyNo);
            })
            ->orderBy('p.account_id')
            ->orderBy('p.survey_no');
    }

    public function headings(): array
    {
        return [
            'account_id', 'full_name', 'education_point_name', 'teacher_name', 'survey_no',
            'درجات البعد العاطفي الانفعالي', 'تقييم البعد العاطفي الانفعالي',
            'درجات البعد النفسي والعقلي', 'تقييم البعد النفسي والعقلي',
            'درجات البعد الجسدي والاجتماعي', 'تقييم البعد الجسدي والاجتماعي',
            'المجموع الكلي', 'التقييم الكلي', 'اسم الاستبيان'
        ];
    }

    // WithMapping لتنسيق النتائج (اختياري)
    public function map($row): array
    {
        return [
            $row->account_id,
            $row->full_name,
            $row->education_point_name,
            $row->teacher_name,
            $row->survey_no,
            $row->درجات_البعد_العاطفي_الانفعالي ?? 0,
            $row->تقييم_البعد_العاطفي_الانفعالي ?? '',
            $row->درجات_البعد_النفسي_والعقلي ?? 0,
            $row->تقييم_البعد_النفسي_والعقلي ?? '',
            $row->درجات_البعد_الجسدي_والاجتماعي ?? 0,
            $row->تقييم_البعد_الجسدي_والاجتماعي ?? '',
            $row->المجموع_الكلي,
            $row->التقييم_الكلي,
            $row->اسم_الاستبيان
        ];
    }
}