<?php

namespace App\Exports;

use App\Models\Status;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\SurveyAnswer;
use App\Models\SurveyComparisonScale;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class SurveyComparisonExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $groupId;
    protected $surveyMapping = [
        137 => 139,
        138 => 140,
        141 => 143,
        142 => 144,
    ];

    public function __construct($groupId = null, $surveyMapping = null)
    {
        $this->groupId = $groupId;
        if ($surveyMapping) {
            $this->surveyMapping = $surveyMapping;
        }
    }

    public function collection()
    {
        // 1. Pre-fetch shared data
        $scales = SurveyComparisonScale::all();
        $domains = Status::where('p_id_sub', config('appConstant.domains_of_assessment', 145))->get();
        $allSurveyIds = array_merge(array_keys($this->surveyMapping), array_values($this->surveyMapping));

        // 2. Fetch all max scores for all involved surveys
        $maxScoresByDomain = SurveyQuestion::whereIn('survey_for_section', $allSurveyIds)
            ->select('survey_for_section', 'domain_id', DB::raw('SUM(max_score) as total_max'))
            ->groupBy('survey_for_section', 'domain_id')
            ->get()
            ->groupBy('survey_for_section');

        // 3. Multi-set Aggregated scores for all students
        $scores = DB::table('survey_answers as a')
            ->join('survey_questions as q', 'a.question_id', '=', 'q.id')
            ->whereIn('a.survey_no', $allSurveyIds)
            ->when($this->groupId, function ($query) {
                return $query->whereIn('a.account_id', function ($sub) {
                    $sub->select('identity_number')->from('students')->where('student_groups_id', $this->groupId);
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

        $students = Student::with(['studentGroup'])
            ->when($this->groupId, fn($q) => $q->where('student_groups_id', $this->groupId))
            ->get();

        $rows = [];

        foreach ($students as $student) {
            $studentRawScores = $scores->get($student->identity_number) ?? collect();

            $row = [
                'full_name' => $student->full_name,
                'identity_number' => $student->identity_number,
                'group' => $student->studentGroup->name ?? '-',
            ];

            // Loop through each pair
            foreach ($this->surveyMapping as $preId => $postId) {
                $totalPre = 0; $totalPost = 0; $totalMax = 0;

                foreach ($domains as $domain) {
                    $max = $maxScoresByDomain->get($preId)?->where('domain_id', $domain->id)->first()?->total_max ?? 0;
                    if ($max <= 0) continue;

                    $pre = $studentRawScores->where('survey_no', $preId)->where('domain_id', $domain->id)->first()?->total_score ?? 0;
                    $post = $studentRawScores->where('survey_no', $postId)->where('domain_id', $domain->id)->first()?->total_score ?? 0;

                    $row[$preId . '_' . $domain->id . '_pre'] = $pre;
                    $row[$preId . '_' . $domain->id . '_post'] = $post;
                    
                    if ($max > 0) {
                        if ($pre > 0 && $post > 0) {
                            $diffPercent = (($post / $max) * 100) - (($pre / $max) * 100);
                            $scale = $this->findMatchInCollection($scales, $diffPercent, $domain->id);
                            $row[$preId . '_' . $domain->id . '_diff'] = round($diffPercent, 1) . '%';
                            $row[$preId . '_' . $domain->id . '_eval'] = $scale->evaluation ?? '-';
                        } else {
                            $row[$preId . '_' . $domain->id . '_diff'] = '---';
                            $row[$preId . '_' . $domain->id . '_eval'] = ($pre > 0 && $post == 0) ? __('Pending') : '-';
                        }
                        $totalPre += $pre; $totalPost += $post; $totalMax += $max;
                    }
                }

                // Pair Totals
                if ($totalMax > 0 && $totalPre > 0 && $totalPost > 0) {
                    $totalDiffPercent = (($totalPost / $totalMax) * 100) - (($totalPre / $totalMax) * 100);
                    $totalScale = $this->findMatchInCollection($scales, $totalDiffPercent, null);
                    $row[$preId . '_total_pre'] = $totalPre;
                    $row[$preId . '_total_post'] = $totalPost;
                    $row[$preId . '_total_diff'] = round($totalDiffPercent, 1) . '%';
                    $row[$preId . '_total_eval'] = $totalScale->evaluation ?? '-';
                } else {
                    $row[$preId . '_total_pre'] = $totalPre;
                    $row[$preId . '_total_post'] = $totalPost;
                    $row[$preId . '_total_diff'] = '---';
                    $row[$preId . '_total_eval'] = ($totalPre > 0 && $totalPost == 0) ? __('Pending') : '-';
                }
            }

            $rows[] = $row;
        }

        return collect($rows);
    }

    protected function findMatchInCollection($scales, $diff, $domainId)
    {
        return $scales->filter(function ($s) use ($diff, $domainId) {
            return $diff >= $s->from_percentage && $diff <= $s->to_percentage 
                && ($s->domain_id == $domainId || is_null($s->domain_id));
        })->sortByDesc(fn($s) => !is_null($s->domain_id))->first();
    }

    public function headings(): array
    {
        $headings = [__('Full Name'), __('Identity Number'), __('Student Group')];

        $domains = Status::where('p_id_sub', config('appConstant.domains_of_assessment', 145))->get();
        $surveyStatuses = Status::whereIn('id', array_keys($this->surveyMapping))->get();
        $allSurveyIds = array_merge(array_keys($this->surveyMapping), array_values($this->surveyMapping));
        
        $maxScoresByDomain = SurveyQuestion::whereIn('survey_for_section', $allSurveyIds)
            ->select('survey_for_section', 'domain_id', DB::raw('SUM(max_score) as total_max'))
            ->groupBy('survey_for_section', 'domain_id')
            ->get()
            ->groupBy('survey_for_section');

        foreach ($this->surveyMapping as $preId => $postId) {
            $surveyName = $surveyStatuses->firstWhere('id', $preId)?->status_name ?? $preId;
            
            foreach ($domains as $domain) {
                // Filter domains by survey context
                $max = $maxScoresByDomain->get($preId)?->where('domain_id', $domain->id)->first()?->total_max ?? 0;
                if ($max <= 0) continue;

                $domainName = $domain->status_name;
                $prefix =   $domainName;
                $headings[] = $prefix . ' (' . __('Pre') . ')'; 
                $headings[] = $prefix . ' (' . __('Post') . ')';
                $headings[] = $prefix . ' (' . __('Diff %') . ')'; 
                $headings[] = $prefix . ' (' . __('Status') . ')';
            }

            $headings[] =  ' (' . __('Total Pre') . ')';
            $headings[] =   ' (' . __('Total Post') . ')';
            $headings[] =   ' (' . __('Total Diff %') . ')';
            $headings[] =   ' (' . __('Final Evaluation') . ')';
        }

        return $headings;
    }

    public function map($row): array
    {
        return array_values($row);
    }
}
