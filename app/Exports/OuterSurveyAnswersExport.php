<?php

namespace App\Exports;

use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OuterSurveyAnswersExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $surveyTableId;
    protected $data = null;
    protected $questions = [];

    public function __construct($surveyTableId = null)
    {
        $this->surveyTableId = $surveyTableId;
    }

    protected function prepareData()
    {
        if ($this->data !== null) {
            return;
        }

        $answers = SurveyAnswer::query()
            ->with(['question', 'surveyTable'])
            ->where('survey_table_id',$this->surveyTableId)
            // ->when($this->surveyNo, fn($q) => $q->where('survey_no', $this->surveyNo))
            ->orderBy('created_at', 'desc')
            ->orderBy('account_id')
            ->get();

        // Get all unique questions for this survey to act as headings, ordered by question_order
        $this->questions = SurveyQuestion::where('survey_table_id', $this->surveyTableId)
            ->orderBy('question_order')
            ->pluck('question_ar_text')
            ->unique()
            ->values()
            ->toArray();

        // Group the answers by survey and account to create rows
        $grouped = $answers->groupBy(function ($item) {
            return $item->survey_table_id . '_' . $item->account_id;
        });

        $rows = [];

        foreach ($grouped as $group) {
            $first = $group->first();
            
            $row = [
                'survey_name' => $first->surveyTable?->survey_name,
                'account_id'  => $first->account_id,
            ];

            // Initialize all question columns from the master list
            foreach ($this->questions as $qText) {
                $row[$qText] = '';
            }

            // Fill in the answers
            foreach ($group as $answer) {
                $qText = $answer->question?->question_ar_text;
                if ($qText && $qText !== 'N/A' && isset($row[$qText])) {
                    if ($row[$qText] !== '') {
                        $row[$qText] .= ' - ' . $answer->answer_ar_text;
                    } else {
                        $row[$qText] = $answer->answer_ar_text;
                    }
                }
            }

            // Add standard metadata fields at the end
            $row['created_by'] = $first->created_by ?? 'N/A';
            $row['created_at'] = $first->created_at ? $first->created_at->format('Y-m-d H:i') : 'N/A';

            $rows[] = $row;
        }

        $this->data = collect($rows);
    }

    public function collection()
    {
        $this->prepareData();
        return $this->data;
    }

    public function headings(): array
    {
        $this->prepareData();
        return array_merge([
            'اسم الرابط الخارجي',
            'account_id',
        ], $this->questions, [
            'Created By',
            'Created At',
        ]);
    }

    public function map($row): array
    {
        return array_values($row);
    }
}
