<?php

namespace App\Exports;

use App\Models\SurveyAnswer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SurveyAnswersExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    protected $surveyNo;
    protected $batchNo;
    protected $groupId;

    protected $lastAccountId = null;
    protected $lastSurveyNo = null;

    private $currentRow = 1;
    private $duplicateRows = [];

    public function __construct($surveyNo, $batchNo, $groupId)
    {
        $this->surveyNo = $surveyNo;
        $this->batchNo = $batchNo;
        $this->groupId = $groupId;
    }

    public function collection()
    {
        return SurveyAnswer::query()
            ->with(['student.studentGroup', 'surveyfor', 'question', 'creator'])
            ->where('survey_no', $this->surveyNo)
            ->whereHas('student', function ($q) {
                $q->where('student_groups_id', $this->groupId)
                    ->whereHas('studentGroup', function ($sq) {
                        $sq->where('batch_no', $this->batchNo);
                    });
            })
            ->orderBy('survey_no')
            ->orderBy('account_id')
            ->get();
    }

    public function headings(): array
    {
        return [

            'Survey Name',
            'Student ID',
            'Student Full Name',
            'Student Group',
            'Question',
            'Answer',
            'Answer Label',
            'Created By',
            'Created At',
        ];
    }

    /**
     * @var SurveyAnswer $answer
     */
    public function map($answer): array
    {
        $this->currentRow++;
        $isDuplicate = ($this->lastAccountId === $answer->account_id && $this->lastSurveyNo === $answer->survey_no);

        // Update tracking variables
        $this->lastAccountId = $answer->account_id;
        $this->lastSurveyNo = $answer->survey_no;

        if ($isDuplicate) {
            $this->duplicateRows[] = $this->currentRow;
        }

        return [
            $isDuplicate ? '' : ($answer->surveyfor?->status_name ?? 'N/A'),
            $isDuplicate ? '' : $answer->account_id,
            $isDuplicate ? '' : ($answer->student?->full_name ?? 'N/A'),
            $isDuplicate ? '' : ($answer->student?->studentGroup?->name ?? 'N/A'),
            $answer->question?->question_ar_text ?? 'N/A',
            $answer->answer_ar_text,
            $answer->answer_label,
            $answer->creator?->full_name ?? 'N/A',
            $answer->created_at ? $answer->created_at->format('Y-m-d H:i') : 'N/A',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $delegate = $event->sheet->getDelegate();
                $delegate->setRightToLeft(true);

                $highestRow = $delegate->getHighestRow();
                if ($highestRow >= 2) {
                    // Style font color of columns A to D (Survey Name, Student ID, Student Full Name, Student Group) to premium blue
                    $delegate->getStyle("A2:D{$highestRow}")->applyFromArray([
                        'font' => [
                            'color' => [
                                'rgb' => '1A73E8', // Premium vibrant blue
                            ],
                        ],
                    ]);
                }

                // Color duplicate cells in soft blue background
                // foreach ($this->duplicateRows as $row) {
                //     $delegate->getStyle("A{$row}:D{$row}")->applyFromArray([
                //         'fill' => [
                //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                //             'startColor' => [
                //                 'rgb' => 'E6F2FF', // Premium soft pastel blue
                //             ],
                //         ],
                //     ]);
                // }
            },
        ];
    }
}
