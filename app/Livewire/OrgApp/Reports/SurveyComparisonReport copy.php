<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Status;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\SurveyAnswer;
use App\Models\SurveyComparisonScale;
use App\Models\SurveyQuestion;
use App\Reposotries\StatusRepo;
use App\Exports\SurveyComparisonExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class SurveyComparisonReport extends Component
{
    public $preSurveyId;
    public $postSurveyId;
    public $selectedGroupId;
    
    public function mount()
    {
        // Default pairs based on user's list
        $this->preSurveyId = 137;
        $this->postSurveyId = 139;
    }

    public function export()
    {
        if (Gate::denies('reports.all')) {
            abort(403);
        }

        $filename = "Survey_Comparison_" . now()->format('Y-m-d_H-i') . ".xlsx";
        return (new SurveyComparisonExport($this->preSurveyId, $this->postSurveyId, $this->selectedGroupId))->download($filename);
    }

    public function render()
    {
        if (Gate::denies('reports.all')) {
            abort(403);
        }

        $surveys = StatusRepo::statuses()->where('p_id_sub', config('appConstant.survey_for', 119));
        $groups = StudentGroup::where('activation', 1)->get();
        
        $reportData = $this->calculateReportData();
        $groupComparisonData = $this->calculateGroupComparison();

        return view('livewire.org-app.reports.survey-comparison-report', [
            'surveys' => $surveys,
            'groups' => $groups,
            'reportData' => $reportData,
            'groupComparisonData' => $groupComparisonData,
        ]);
    }

    protected function calculateReportData()
    {
        if (!$this->preSurveyId || !$this->postSurveyId) return [];

        // 1. Fetch all scales to avoid repeated queries
        $scales = SurveyComparisonScale::all();

        // 2. Fetch all domains
        $domains = Status::where('p_id_sub', config('appConstant.domains_of_assessment', 145))->get();
        $domainIds = $domains->pluck('id');

        // 3. Fetch max scores for both surveys
        $maxScoresByDomain = SurveyQuestion::whereIn('survey_for_section', [$this->preSurveyId, $this->postSurveyId])
            ->select('survey_for_section', 'domain_id', DB::raw('SUM(max_score) as total_max'))
            ->groupBy('survey_for_section', 'domain_id')
            ->get()
            ->groupBy('domain_id')
            ->map(function ($items) {
                return $items->where('survey_for_section', $this->preSurveyId)->first()?->total_max ?? 0;
            });

        // 4. Aggregated Scores for all students in group
        $scores = DB::table('survey_answers as a')
            ->join('survey_questions as q', 'a.question_id', '=', 'q.id')
            ->whereIn('a.survey_no', [$this->preSurveyId, $this->postSurveyId])
            ->when($this->selectedGroupId, function ($query) {
                return $query->whereIn('a.account_id', function ($sub) {
                    $sub->select('identity_number')->from('students')->where('student_groups_id', $this->selectedGroupId);
                });
            })
            ->select(
                'a.account_id',
                'q.domain_id',
                DB::raw("SUM(CASE WHEN a.survey_no = {$this->preSurveyId} THEN CAST(a.answer_ar_text AS DECIMAL(10,2)) ELSE 0 END) as pre_score"),
                DB::raw("SUM(CASE WHEN a.survey_no = {$this->postSurveyId} THEN CAST(a.answer_ar_text AS DECIMAL(10,2)) ELSE 0 END) as post_score")
            )
            ->groupBy('a.account_id', 'q.domain_id')
            ->get()
            ->groupBy('account_id');

        // 5. Build results from aggregated data
        $students = Student::query()
            ->when($this->selectedGroupId, fn($q) => $q->where('student_groups_id', $this->selectedGroupId))
            ->get();

        $results = [];

        foreach ($students as $student) {
            $studentScores = $scores->get($student->identity_number) ?? collect();
            
            $studentData = [
                'full_name' => $student->full_name,
                'identity_number' => $student->identity_number,
                'domains' => [],
                'total' => [
                    'pre' => 0, 'post' => 0, 'diff_percent' => 0, 'evaluation' => '', 'color' => '',
                ]
            ];

            $totalPre = 0; $totalPost = 0; $totalMax = 0;

            foreach ($domains as $domain) {
                $domainScore = $studentScores->firstWhere('domain_id', $domain->id);
                $pre = $domainScore?->pre_score ?? 0;
                $post = $domainScore?->post_score ?? 0;
                $max = $maxScoresByDomain->get($domain->id) ?? 0;

                if ($max > 0) {
                    $prePercent = ($pre / $max) * 100;
                    $postPercent = ($post / $max) * 100;
                    $diffPercent = $postPercent - $prePercent;
                    
                    $scale = $this->findMatchInCollection($scales, $diffPercent, $domain->id);

                    $studentData['domains'][$domain->id] = [
                        'name' => $domain->status_name,
                        'pre' => $pre,
                        'post' => $post,
                        'diff_percent' => round($diffPercent, 1),
                        'evaluation' => $scale->evaluation ?? '-',
                        'color' => $scale->color ?? '#9ca3af',
                    ];

                    $totalPre += $pre; $totalPost += $post; $totalMax += $max;
                }
            }

            if ($totalMax > 0) {
                $totalDiffPercent = (($totalPost / $totalMax) * 100) - (($totalPre / $totalMax) * 100);
                $totalScale = $this->findMatchInCollection($scales, $totalDiffPercent, null);

                $studentData['total'] = [
                    'pre' => $totalPre,
                    'post' => $totalPost,
                    'diff_percent' => round($totalDiffPercent, 1),
                    'evaluation' => $totalScale->evaluation ?? '-',
                    'color' => $totalScale->color ?? '#9ca3af',
                ];
            }

            $results[] = (object)$studentData;
        }

        return collect($results);
    }

    protected function findMatchInCollection($scales, $diff, $domainId)
    {
        return $scales->filter(function ($s) use ($diff, $domainId) {
            return $diff >= $s->from_percentage && $diff <= $s->to_percentage 
                && ($s->domain_id == $domainId || is_null($s->domain_id));
        })->sortByDesc(fn($s) => !is_null($s->domain_id))->first();
    }

    protected function calculateGroupComparison()
    {
        if (!$this->preSurveyId || !$this->postSurveyId) return [];

        // Aggregated scores per student across ALL groups
        $studentTotals = DB::table('survey_answers as a')
            ->select(
                'a.account_id',
                DB::raw("SUM(CASE WHEN a.survey_no = {$this->preSurveyId} THEN CAST(a.answer_ar_text AS DECIMAL(10,2)) ELSE 0 END) as pre_total"),
                DB::raw("SUM(CASE WHEN a.survey_no = {$this->postSurveyId} THEN CAST(a.answer_ar_text AS DECIMAL(10,2)) ELSE 0 END) as post_total")
            )
            ->whereIn('a.survey_no', [$this->preSurveyId, $this->postSurveyId])
            ->groupBy('a.account_id')
            ->get()
            ->keyBy('account_id');

        $groups = StudentGroup::with(['students' => function($q) {
            $q->where('activation', 1);
        }])->where('activation', 1)->get();
        
        $comparison = [];

        foreach ($groups as $group) {
            $improvedCount = 0;
            $totalInSurvey = 0;

            foreach ($group->students as $student) {
                $totals = $studentTotals->get($student->identity_number);
                if ($totals && ($totals->pre_total > 0 || $totals->post_total > 0)) {
                    $totalInSurvey++;
                    if ($totals->post_total > $totals->pre_total) {
                        $improvedCount++;
                    }
                }
            }

            if ($totalInSurvey > 0) {
                $comparison[] = [
                    'group_name' => $group->name,
                    'improved_count' => $improvedCount,
                    'total_count' => $totalInSurvey,
                    'improvement_rate' => round(($improvedCount / $totalInSurvey) * 100, 1),
                ];
            }
        }

        return $comparison;
    }
}
