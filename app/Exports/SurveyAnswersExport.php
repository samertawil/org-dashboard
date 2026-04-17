<?php

namespace App\Exports;

use App\Models\SurveyAnswer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveyAnswersExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $surveyNo;

    public function __construct($surveyNo = null)
    {
        $this->surveyNo = $surveyNo;
    }

    public function collection()
    {
        return SurveyAnswer::query()
            ->with(['student.studentGroup', 'surveyfor', 'question', 'creator','surveyTable'])
            ->when($this->surveyNo, fn($q) => $q->where('survey_no', $this->surveyNo))
            ->orderBy('survey_no')
            ->orderBy('account_id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'اسم الرابط الخارجي',
            'Survey Name',
            'Student ID',
            'Student Full Name',
            'Student Group',
            'Question',
            'Answer (AR)',
            'Created By',
            'Created At',
        ];
    }

    /**
    * @var SurveyAnswer $answer
    */
    public function map($answer): array
    {
        return [
            $answer->surveyTable?->survey_name,
            $answer->surveyfor?->status_name ?? 'N/A',
            $answer->account_id,
            $answer->student?->full_name ?? 'N/A',
            $answer->student?->studentGroup?->name ?? 'N/A',
            $answer->question?->question_ar_text ?? 'N/A',
            $answer->answer_ar_text,
            $answer->creator?->name ?? 'N/A',
            $answer->created_at ? $answer->created_at->format('Y-m-d H:i') : 'N/A',
        ];
    }
}
