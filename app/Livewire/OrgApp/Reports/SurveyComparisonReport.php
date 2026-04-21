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
    public $selectedLevel = 'level_1'; // level_1 or level_2
    public $selectedGroupId;

    protected $levelMapping = [
        'level_1' => [
            'name' => 'الأطفال من سن 6-9 سنوات',
            'pairs' => [
                ['pre' => 137, 'post' => 139, 'label' => 'دعم نفسي'],
                ['pre' => 141, 'post' => 143, 'label' => 'تعليم'],
            ]
        ],
        'level_2' => [
            'name' => 'الأطفال من سن 10-12 سنوات',
            'pairs' => [
                ['pre' => 138, 'post' => 140, 'label' => 'دعم نفسي'],
                ['pre' => 142, 'post' => 144, 'label' => 'تعليم'],
            ]
        ]
    ];
    
    public function mount()
    {
        // Initial state
    }

    public function export()
    {
        if (Gate::denies('reports.all')) {
            abort(403);
        }

        $activePairs = $this->levelMapping[$this->selectedLevel]['pairs'];
        $mapping = collect($activePairs)->pluck('post', 'pre')->toArray();

        $filename = "Comparison_" . str_replace([' ', '/', '\\'], '_', $this->levelMapping[$this->selectedLevel]['name']) . "_" . now()->format('Y-m-d_H-i') . ".xlsx";
        return (new SurveyComparisonExport($this->selectedGroupId, $mapping))->download($filename);
    }

    public function render()
    {
        if (Gate::denies('reports.all')) {
            abort(403);
        }

        $levels = $this->levelMapping;
        $activePairs = $levels[$this->selectedLevel]['pairs'];
        
        $groups = StudentGroup::where('activation', 1)->get();
        
        $reportData = $this->calculateReportData($activePairs);
        $groupComparisonData = $this->calculateGroupComparison($activePairs);

        return view('livewire.org-app.reports.survey-comparison-report', [
            'levels' => $levels,
            'activePairs' => $activePairs,
            'groups' => $groups,
            'reportData' => $reportData,
            'groupComparisonData' => $groupComparisonData,
        ]);
    }

    protected function calculateReportData($pairs)
    {
        $scales = SurveyComparisonScale::all();
        $domains = Status::where('p_id_sub', config('appConstant.domains_of_assessment', 145))->get();
        
        $surveyIds = collect($pairs)->flatMap(fn($p) => [$p['pre'], $p['post']])->toArray();

        // Fetch max scores
        $maxScoresByDomain = SurveyQuestion::whereIn('survey_for_section', $surveyIds)
            ->select('survey_for_section', 'domain_id', DB::raw('SUM(max_score) as total_max'))
            ->groupBy('survey_for_section', 'domain_id')
            ->get()
            ->groupBy('survey_for_section');

        // Aggregated Scores
        $scores = DB::table('survey_answers as a')
            ->join('survey_questions as q', 'a.question_id', '=', 'q.id')
            ->whereIn('a.survey_no', $surveyIds)
            ->when($this->selectedGroupId, function ($query) {
                return $query->whereIn('a.account_id', function ($sub) {
                    $sub->select('identity_number')->from('students')->where('student_groups_id', $this->selectedGroupId);
                });
            })
            ->select(
                'a.account_id',
                'a.survey_no',
                'q.domain_id',
                DB::raw("SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) as total_score")
            )
            ->groupBy('a.account_id', 'a.survey_no', 'q.domain_id')
            ->get()
            ->groupBy('account_id');

        $students = Student::query()
            ->when($this->selectedGroupId, fn($q) => $q->where('student_groups_id', $this->selectedGroupId))
            ->get();

        $results = [];

        foreach ($students as $student) {
            $studentRawScores = $scores->get($student->identity_number) ?? collect();
            
            $studentData = [
                'full_name' => $student->full_name,
                'identity_number' => $student->identity_number,
                'pair_results' => []
            ];

            foreach ($pairs as $pair) {
                $preId = $pair['pre'];
                $postId = $pair['post'];
                
                $totalPre = 0; $totalPost = 0; $totalMax = 0;
                $pairDomains = [];

                foreach ($domains as $domain) {
                    // Check if this domain actually exists for this survey
                    $max = $maxScoresByDomain->get($preId)?->where('domain_id', $domain->id)->first()?->total_max ?? 0;
                    if ($max <= 0) continue;

                    $preScore = $studentRawScores->where('survey_no', $preId)->where('domain_id', $domain->id)->first()?->total_score ?? 0;
                    $postScore = $studentRawScores->where('survey_no', $postId)->where('domain_id', $domain->id)->first()?->total_score ?? 0;

                    if ($max > 0) {
                        $hasPost = ($postScore > 0);
                        $diff = ($preScore > 0 && $hasPost) ? ($postScore / $max * 100) - ($preScore / $max * 100) : null;
                        $scale = $diff !== null ? $this->findMatchInCollection($scales, $diff, $domain->id) : null;

                        $pairDomains[$domain->id] = [
                            'name' => $domain->status_name,
                            'pre' => $preScore,
                            'post' => $postScore,
                            'diff' => $diff !== null ? round($diff, 1) : null,
                            'evaluation' => $scale->evaluation ?? ($preScore > 0 && !$hasPost ? __('Pending') : '-'),
                            'color' => $scale->color ?? '#9ca3af',
                        ];

                        $totalPre += $preScore; $totalPost += $postScore; $totalMax += $max;
                    }
                }

                $pairTotal = null;
                if ($totalMax > 0) {
                    $hasTotalPost = ($totalPost > 0);
                    $totalDiff = ($totalPre > 0 && $hasTotalPost) ? (($totalPost / $totalMax) * 100) - (($totalPre / $totalMax) * 100) : null;
                    $totalScale = $totalDiff !== null ? $this->findMatchInCollection($scales, $totalDiff, null) : null;

                    $pairTotal = [
                        'pre' => $totalPre,
                        'post' => $totalPost,
                        'diff' => $totalDiff !== null ? round($totalDiff, 1) : null,
                        'evaluation' => $totalScale->evaluation ?? ($totalPre > 0 && !$hasTotalPost ? __('Pending') : '-'),
                        'color' => $totalScale->color ?? '#9ca3af',
                    ];
                }

                $studentData['pair_results'][$preId] = [
                    'domains' => $pairDomains,
                    'total' => $pairTotal
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

    protected function calculateGroupComparison($pairs)
    {
        $surveyIds = collect($pairs)->flatMap(fn($p) => [$p['pre'], $p['post']])->toArray();

        $studentTotals = DB::table('survey_answers as a')
            ->whereIn('a.survey_no', $surveyIds)
            ->select(
                'a.account_id',
                'a.survey_no',
                DB::raw("SUM(CAST(a.answer_ar_text AS DECIMAL(10,2))) as total_score")
            )
            ->groupBy('a.account_id', 'a.survey_no')
            ->get()
            ->groupBy('account_id');

        $groups = StudentGroup::with(['students'])->where('activation', 1)->get();
        
        $comparison = [];

        foreach ($pairs as $pair) {
            $preId = $pair['pre'];
            $postId = $pair['post'];
            
            $pairGroupData = [];

            foreach ($groups as $group) {
                $improvedCount = 0;
                $totalInSurvey = 0;

                foreach ($group->students as $student) {
                    $scores = $studentTotals->get($student->identity_number) ?? collect();
                    $preTotal = $scores->where('survey_no', $preId)->first()?->total_score ?? 0;
                    $postTotal = $scores->where('survey_no', $postId)->first()?->total_score ?? 0;

                    if ($preTotal > 0 && $postTotal > 0) {
                        $totalInSurvey++;
                        if ($postTotal > $preTotal) {
                            $improvedCount++;
                        }
                    }
                }

                if ($totalInSurvey > 0) {
                    $pairGroupData[] = [
                        'group_name' => $group->name,
                        'improved_count' => $improvedCount,
                        'total_count' => $totalInSurvey,
                        'improvement_rate' => round(($improvedCount / $totalInSurvey) * 100, 1),
                    ];
                }
            }
            $comparison[$preId] = $pairGroupData;
        }

        return $comparison;
    }
}
