<?php

namespace App\Livewire\OrgApp\SurveyQuestions;

use App\Exports\OuterSurveyAnswersExport;
use App\Exports\SurveyAnswersExport;
use App\Exports\SurveyAnswersPivotExport;
use App\Exports\SurveyLate;
use App\Exports\SurveyResultsExport;
use App\Models\Status;
use App\Models\StudentGroup;
use App\Models\SurveyTable;
use App\Reposotries\StudentGroupRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ExportFiles extends Component
{
    public $surveyNo = '';
    public $surveyNoPivot = '';
    public $surveyLate = '';
    public $groupIdPivot = '';
    public $groupIdLate = '';
    public $surveyNoResults = '';
    public $groupIdResults = '';
    public $publicSurveyNo = '';


    public function outerExportSurveyAnswers()
    {

        $this->validate([
            'publicSurveyNo' => 'required',
        ]);
        $surveyName = $this->publicSurveyNo 
            ? SurveyTable::find($this->publicSurveyNo)?->survey_name 
            : __('All Surveys');

        $filename = "Survey_Answers_List_{$surveyName}_" . now()->format('Y-m-d_H-i') . ".xlsx";

        return (new OuterSurveyAnswersExport($this->publicSurveyNo))->download($filename);
    }



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

    public function exportSurveyResults()
    {
        $surveyName = $this->surveyNoResults 
            ? Status::find($this->surveyNoResults)?->status_name 
            : __('All Surveys');

        $filename = "Survey_Results_{$surveyName}_" . now()->format('Y-m-d_H-i') . ".xlsx";

        return (new SurveyResultsExport($this->surveyNoResults, $this->groupIdResults))->download($filename);
    }

    public function render()
    {
        
        if (Gate::denies('survey.export')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $surveys = Status::whereIn('p_id_sub', [config('appConstant.survey_for')])
            ->orderBy('status_name')
        ->get();


        $publicSurveys = SurveyTable::whereIn('survey_target', [config('appConstant.public_links'),config('appConstant.parent_links')])
        ->orderBy('id','DESC')
    ->get();
        
        $groupNames = StudentGroupRepo::studentGroups();

        return view('livewire.org-app.survey-questions.export-files', [
            'surveys' => $surveys,
            'groupNames' => $groupNames,
            'publicSurveys'=>$publicSurveys,
        ]);
    }
}
