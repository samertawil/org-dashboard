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
use App\Concerns\AccessibleGroupsTrait;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ExportFiles extends Component
{
    use AccessibleGroupsTrait;
    public $surveyNo = '';
    public $surveyNoPivot = '';
    public $surveyLate = '';
    public $groupIdPivot = '';
    public $groupIdLate = '';
    public $surveyNoResults = '';
    public $groupIdResults = '';
    public $groupId = '';
    public $publicSurveyNo = '';
    public $batchNo = '';
    public $batchNoPivot = '';
    public $batchNoLate = '';
    public $batchNoResults = '';

    public function updatedBatchNo($value)
    {
        $this->groupId = '';
    }

    public function updatedBatchNoPivot($value)
    {
        $this->groupIdPivot = '';
    }

    public function updatedBatchNoLate($value)
    {
        $this->groupIdLate = '';
    }

    public function updatedBatchNoResults($value)
    {
        $this->groupIdResults = '';
    }


    public function outerExportSurveyAnswers()
    {
        if (Gate::denies('select.any_student')) {
            abort(403, 'You do not have the necessary permissions.');
        }
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
        $this->validate([
            'surveyNo' => 'required|exists:statuses,id',
            'batchNo' => 'required',
            'groupId' => 'required|exists:student_groups,id',
        ]);

        $surveyName = Status::find($this->surveyNo)?->status_name;
        $filename = "Survey_Answers_List_{$surveyName}_Batch_{$this->batchNo}_" . now()->format('Y-m-d_H-i') . ".xlsx";

        return (new SurveyAnswersExport($this->surveyNo, $this->batchNo, $this->groupId))->download($filename);
    }

    public function exportSurveyAnswersPivot()
    {
        $this->validate([
            'surveyNoPivot' => 'required|exists:statuses,id',
            'batchNoPivot' => 'required',
            'groupIdPivot' => 'required|exists:student_groups,id',
        ]);

        $surveyName = Status::find($this->surveyNoPivot)?->status_name;
        $filename = "Survey_Answers_Pivot_{$surveyName}_Batch_{$this->batchNoPivot}_" . now()->format('Y-m-d_H-i') . ".xlsx";

        return (new SurveyAnswersPivotExport($this->surveyNoPivot, $this->groupIdPivot, $this->batchNoPivot))->download($filename);
    }

    public function exportSurveyLate()
    {
        $this->validate([
            'surveyLate' => 'required|exists:statuses,id',
            'batchNoLate' => 'required',
            'groupIdLate' => 'required|exists:student_groups,id',
        ]);

        $surveyName = Status::find($this->surveyLate)?->status_name;
        $filename = "Survey_Late_{$surveyName}_Batch_{$this->batchNoLate}_" . now()->format('Y-m-d_H-i') . ".xlsx";

        return (new SurveyLate($this->surveyLate, $this->groupIdLate, $this->batchNoLate))->download($filename);
    }

    public function exportSurveyResults()
    {
        $this->validate([
            'surveyNoResults' => 'required|exists:statuses,id',
            'batchNoResults' => 'required',
            'groupIdResults' => 'required|exists:student_groups,id',
        ]);

        $surveyName = Status::find($this->surveyNoResults)?->status_name;
        $filename = "Survey_Results_{$surveyName}_Batch_{$this->batchNoResults}_" . now()->format('Y-m-d_H-i') . ".xlsx";

        return (new SurveyResultsExport($this->surveyNoResults, $this->groupIdResults, $this->batchNoResults))->download($filename);
    }

    public function render()
    {

        if (Gate::denies('survey.export')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $surveys = Status::whereIn('p_id_sub', [config('appConstant.survey_for')])
            ->orderBy('status_name')
            ->get();


        $publicSurveys = SurveyTable::whereIn('survey_target', [config('appConstant.public_links'), config('appConstant.parent_links')])
            ->orderBy('id', 'DESC')
            ->get();

        $groupNames = $this->accessibleGroups;
        $groupNamesPivot = $groupNames;
        $groupNamesLate = $groupNames;
        $groupNamesResults = $groupNames;

        if ($this->batchNo) {
            $groupNames = $this->accessibleGroups
                ->where('batch_no', $this->batchNo)
                ->sortBy('name');
        }

        if ($this->batchNoPivot) {
            $groupNamesPivot = $this->accessibleGroups
                ->where('batch_no', $this->batchNoPivot)
                ->sortBy('name');
        }

        if ($this->batchNoLate) {
            $groupNamesLate = $this->accessibleGroups
                ->where('batch_no', $this->batchNoLate)
                ->sortBy('name');
        }

        if ($this->batchNoResults) {
            $groupNamesResults = $this->accessibleGroups
                ->where('batch_no', $this->batchNoResults)
                ->sortBy('name');
        }

        $batchNumbers = $this->availableBatches;

        return view('livewire.org-app.survey-questions.export-files', [
            'surveys' => $surveys,
            'groupNames' => $groupNames,
            'groupNamesPivot' => $groupNamesPivot,
            'groupNamesLate' => $groupNamesLate,
            'groupNamesResults' => $groupNamesResults,
            'publicSurveys' => $publicSurveys,
            'batchNumbers' => $batchNumbers,
        ]);
    }
}
