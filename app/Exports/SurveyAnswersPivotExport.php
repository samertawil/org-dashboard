<?php

namespace App\Exports;

use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveyAnswersPivotExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $surveyNo;
    protected $groupIdPivot;
    protected $questions;

    public function __construct($surveyNo,$groupIdPivot)
    {
        $this->surveyNo = $surveyNo;
        $this->groupIdPivot = $groupIdPivot;
        // Fetch questions for this survey to define columns
        $this->questions = SurveyQuestion::where('survey_for_section', $surveyNo)
            ->orderBy('question_order')
            ->get();
    }

    public function collection()
    {
        // Fetch answers and group by account_id (identity number)
        return SurveyAnswer::query()
            ->with(['student.studentGroup', 'creator'])
            ->where('survey_no', $this->surveyNo)
            ->when($this->groupIdPivot, function ($query) {
                return $query->whereHas('student', function ($studentQuery) {
                    $studentQuery->where('student_groups_id', $this->groupIdPivot);
                });
            })
            ->get()
            ->groupBy('account_id');
    }

    public function headings(): array
    {
        $headings = [
            'Student ID',
            'Full Name',
            'Group Name',
            'Created By',
        ];

        foreach ($this->questions as $question) {
            $headings[] = $question->question_ar_text;
        }

        return $headings;
    }

    /**
    * @var \Illuminate\Support\Collection $answersByStudent
    */
    public function map($answersByStudent): array
    {
        $firstAnswer = $answersByStudent->first();
        $student = $firstAnswer->student;

        $row = [
            $firstAnswer->account_id,
            $student?->full_name ?? 'N/A',
            $student?->studentGroup?->name ?? 'N/A',
            $firstAnswer->creator?->full_name ?? 'N/A',
        ];

        // Map answers back to each question column
        foreach ($this->questions as $question) {
            $answer = $answersByStudent->firstWhere('question_id', $question->id);
            $row[] = $answer ? $answer->answer_ar_text : '';
        }

        return $row;
    }
}
