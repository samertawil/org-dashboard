<?php

namespace App\Livewire\OrgApp\SurveyQuestions;

use App\Models\SurveyTable;
use App\Models\SurveyQuestion;
use App\Models\SurveyGradingScaleTable;
use App\Models\SurveyGradingScaleDescription;
use App\Models\SurveyComparisonScale;
use App\Models\Status;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

use Barryvdh\DomPDF\Facade\Pdf;

class Show extends Component
{
    #[Url(as: 'q')]
    public $search = '';

    #[Url(as: 'section')]
    public $selectedSection = '';

    #[Url(as: 'batch')]
    public $selectedBatch = '';

    #[Url(as: 'target')]
    public $selectedTarget = '';

    public function resetFilters()
    {
        $this->reset(['search', 'selectedSection', 'selectedBatch', 'selectedTarget']);
    }

    /**
     * Build the survey tree structure in memory to avoid N+1 queries.
     */
    public function getSurveyTree()
    {
        // 1. Fetch lookup databases
        $allGradingScales = SurveyGradingScaleTable::with(['typeRel', 'surveyForSection'])->get();
        $allDescriptions = SurveyGradingScaleDescription::with('domainRel')->get();
        $allComparisonScales = SurveyComparisonScale::with(['domain', 'surveyForSection'])->get();

        // 2. Query Survey tables with filters
        $surveysQuery = SurveyTable::query()->with(['sectionRel', 'targetRel']);

        if ($this->selectedSection) {
            $surveysQuery->where('survey_for_section', $this->selectedSection);
        }
        if ($this->selectedTarget) {
            $surveysQuery->where('survey_target', $this->selectedTarget);
        }
        if ($this->search) {
            $surveysQuery->where('survey_name', 'like', '%' . $this->search . '%');
        }

        $surveys = $surveysQuery->get();

        // 3. Query questions with filters
        $questionsQuery = SurveyQuestion::query()->with(['domainRel', 'surveyForSection', 'batchs']);

        if ($this->selectedBatch) {
            $questionsQuery->where('batch_no', $this->selectedBatch);
        }

        if ($this->search) {
            $questionsQuery->where(function ($q) {
                $q->where('question_ar_text', 'like', '%' . $this->search . '%')
                    ->orWhere('question_en_text', 'like', '%' . $this->search . '%');
            });
        }

        $questions = $questionsQuery->get();

        // 4. Build the hierarchy
        $tree = [];

        foreach ($surveys as $survey) {
            // Find questions belonging to this survey's section
            $surveyQuestions = $questions->filter(function ($q) use ($survey) {
                return $q->survey_for_section == $survey->survey_for_section;
            })->sortBy('question_order');

            if ($surveyQuestions->isEmpty() && $this->search && !str_contains(strtolower($survey->survey_name), strtolower($this->search))) {
                // If searching and neither survey name nor questions match, exclude this survey
                continue;
            }

            // Group questions by batch_no
            $batchesGrouped = [];
            $groupedByBatch = $surveyQuestions->groupBy('batch_no');

            foreach ($groupedByBatch as $batchNo => $batchQuestions) {
                // Get grading scales for this batch under the survey's section
                $batchScales = $allGradingScales->filter(function ($gs) use ($survey, $batchNo) {
                    return $gs->survey_for_section == $survey->survey_for_section
                        && $gs->batch_no == $batchNo;
                })->sortBy('from_percentage');

                $scalesList = [];
                foreach ($batchScales as $scale) {
                    // Match grading scale descriptions
                    $matchedDescriptions = $allDescriptions->filter(function ($desc) use ($scale) {
                        return $desc->survey_grading_scale_id == $scale->id;
                    });

                    $descriptionsList = [];
                    foreach ($matchedDescriptions as $desc) {
                        // Match comparison scales
                        $matchedComparisons = $allComparisonScales->filter(function ($cs) use ($desc, $scale) {
                            return $cs->domain_id == $desc->domain_id
                                && $cs->survey_for_section == $scale->survey_for_section
                                && $cs->batch_no == $scale->batch_no
                                && floatval($cs->from_percentage) == floatval($scale->from_percentage)
                                && floatval($cs->to_percentage) == floatval($scale->to_percentage);
                        });

                        $descriptionsList[] = [
                            'record' => $desc,
                            'comparisons' => $matchedComparisons->values()->toArray(),
                        ];
                    }

                    $scalesList[] = [
                        'record' => $scale,
                        'descriptions' => $descriptionsList,
                    ];
                }

                // Get questions list
                $questionsList = [];
                foreach ($batchQuestions as $question) {
                    $questionsList[] = [
                        'record' => $question,
                    ];
                }

                $batchName = $batchQuestions->first()->batchs->name ?? __('Batch') . ' ' . $batchNo;

                $batchesGrouped[] = [
                    'batch_no' => $batchNo,
                    'batch_name' => $batchName,
                    'grading_scales' => $scalesList,
                    'questions' => $questionsList,
                ];
            }

            $tree[] = [
                'record' => $survey,
                'batches' => array_values($batchesGrouped),
            ];
        }

        return $tree;
    }

    public function downloadPdf()
    {
        if (Gate::denies('survey-questions.show')) {
            abort(403, __('You do not have the necessary permissions.'));
        }

        $pdf = Pdf::loadView('livewire.org-app.survey-questions.pdf', [
            'surveyTree' => $this->getSurveyTree(),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'survey-structure.pdf');
    }

    #[Title('Survey Tree View')]
    public function render()
    {
        if (Gate::denies('survey-questions.show')) {
            abort(403, __('You do not have the necessary permissions.'));
        }

        // Get filter options from existing database entries to ensure they match actual data
        $sections = Status::whereIn('id', SurveyTable::pluck('survey_for_section')->filter()->unique())
            ->get();

        $targets = Status::whereIn('id', SurveyTable::pluck('survey_target')->filter()->unique())
            ->get();

        $batches = SurveyQuestion::select('batch_no')
            ->distinct()
            ->whereNotNull('batch_no')
            ->orderBy('batch_no')
            ->pluck('batch_no');

        $surveyTree = $this->getSurveyTree();

        // Build list of all toggle keys
        $toggleKeys = [];
        foreach ($surveyTree as $surveyItem) {
            $survey = $surveyItem['record'];
            $toggleKeys[] = 'survey_' . $survey->id;
            foreach ($surveyItem['batches'] as $batchItem) {
                $batchNo = $batchItem['batch_no'];
                $toggleKeys[] = 'batch_' . $survey->id . '_' . $batchNo;
                if (!empty($batchItem['grading_scales'])) {
                    $toggleKeys[] = 'scales_' . $survey->id . '_' . $batchNo;
                }
            }
        }

        return view('livewire.org-app.survey-questions.show', [
            'surveyTree' => $surveyTree,
            'toggleKeys' => $toggleKeys,
            'sections' => $sections,
            'targets' => $targets,
            'batches' => $batches,
        ]);
    }
}
