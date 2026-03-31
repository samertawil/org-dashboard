<?php

namespace App\Exports;

use App\Models\SurveyAnswer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveyLate implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $surveyNo;
    protected $groupId;
    private $rowNumber = 0;

    public function __construct($surveyNo = null,$groupId = null)
    {
        $this->surveyNo = $surveyNo;
        $this->groupId = $groupId;
    }

    public function collection()
    {
        $results = DB::select("
        SELECT DISTINCT
            s.id, s.identity_number, s.full_name, sg.name as group_name,
             stat.status_name as survey_name,   s.activation
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
          AND (:surveyNo IS NULL OR st.survey_for_section = :surveyNo2)
          AND (:groupId IS NULL OR s.student_groups_id = :groupId2)
        ", [
            'surveyNo'  => $this->surveyNo ?: null,
            'surveyNo2' => $this->surveyNo ?: null,
            'groupId'   => $this->groupId ?: null,
            'groupId2'  => $this->groupId ?: null,
        ]);

        return collect($results);
    }

    public function headings(): array
    {
        return [
            'Sequence',
            'Identity Number',
            'Full Name',
            'Group Name',
            'Survey Section',
            'Status',
        ];
    }

    /**
    * @param mixed $row
    * @return array
    */
    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->identity_number,
            $row->full_name,
            $row->group_name,
            $row->survey_name,
            $row->activation == 1 ? 'Active' : 'Inactive',
        ];
    }
}
