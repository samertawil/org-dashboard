<?php

namespace App\Livewire\OrgApp\SurveyQuestions;

use App\Exports\SurveyAnswersExport;
use App\Exports\SurveyAnswersPivotExport;
use App\Exports\SurveyLate;
use App\Models\Status;
use App\Models\StudentGroup;
use App\Reposotries\StudentGroupRepo;
use Livewire\Component;

class ExportFiles extends Component
{
    public $surveyNo = '';
    public $surveyNoPivot = '';
    public $surveyLate = '';
    public $groupIdPivot = '';
    public $groupIdLate = '';

    public function exportSurveyAnswers()
    {
        $surveyName = $this->surveyNo 
            ? Status::find($this->surveyNo)?->status_name 
            : __('All Surveys');

        $filename = "Survey_Answers_List_{$surveyName}_" . now()->format('Y-m-d_H-i') . ".xlsx";

        return (new SurveyAnswersExport($this->surveyNo))->download($filename);
    }

    public function exportSurveyAnswersPivot()
    {
        $this->validate([
            'surveyNoPivot' => 'required|exists:statuses,id',
        ]);

        $surveyName = Status::find($this->surveyNoPivot)?->status_name;
        $filename = "Survey_Answers_Pivot_{$surveyName}_" . now()->format('Y-m-d_H-i') . ".xlsx";

        return (new SurveyAnswersPivotExport($this->surveyNoPivot,$this->groupIdPivot))->download($filename);
    }

    public function exportSurveyLate()
    {
        $surveyName = $this->surveyLate 
            ? Status::find($this->surveyLate)?->status_name 
            : __('All Late Surveys');

        $filename = "Survey_Late_{$surveyName}_" . now()->format('Y-m-d_H-i') . ".xlsx";

        return (new SurveyLate($this->surveyLate, $this->groupIdLate))->download($filename);
    }

    public function render()
    {
        $surveys = Status::whereIn('p_id_sub', [config('appConstant.survey_for')])
            ->orderBy('status_name')
        ->get();
        
        $groupNames = StudentGroupRepo::studentGroups();

        return view('livewire.org-app.survey-questions.export-files', [
            'surveys' => $surveys,
            'groupNames' => $groupNames,
        ]);
    }
}
