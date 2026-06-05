<?php

namespace App\Livewire\OrgApp\SurveyQuestions;

use App\Concerns\AccessibleGroupsTrait;
use App\Models\Status;
use App\Models\Student;
use App\Models\SurveyAnswer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Statistics extends Component
{
    use AccessibleGroupsTrait;

    public string $surveyNo    = '';
    public string $batchNo     = '';
    public string $groupId     = '';

    public function updatedBatchNo(): void
    {
        $this->groupId = '';
    }

    // ─── Computed: filtered group list ────────────────────────────────────────

    #[Computed()]
    public function filteredGroups()
    {
        $groups = $this->accessibleGroups;

        if ($this->batchNo) {
            $groups = $groups->where('batch_no', $this->batchNo)->sortBy('name')->values();
        }

        return $groups;
    }

    // ─── Computed: stats table ─────────────────────────────────────────────────

    /**
     * إرجاع إحصائيات الاستجابة لكل مجموعة متاحة دفعةً واحدة.
     * إذا تم تحديد groupId → نتيجة مجموعة واحدة فقط.
     * إذا كان groupId فارغاً  → جميع المجموعات في الدفعة.
     * يستخدم استعلامَين فقط بغض النظر عن عدد المجموعات.
     */
    #[Computed()]
    public function statsPerGroup(): array
    {
        if (!$this->surveyNo || !$this->batchNo) {
            return [];
        }

        // تحديد المجموعات المراد حساب الإحصائية لها
        $groups = $this->filteredGroups;
        if ($this->groupId) {
            $groups = $groups->where('id', (int) $this->groupId)->values();
        }

        if ($groups->isEmpty()) {
            return [];
        }

        $groupIds = $groups->pluck('id')->toArray();

        // ── استعلام 1: عدد المستجيبين (distinct account_id) لكل مجموعة ───────
        $respondentsByGroup = SurveyAnswer::where('survey_no', $this->surveyNo)
            ->join('students', function ($join) {
                $join->on('survey_answers.account_id', '=', 'students.identity_number');
            })
            ->whereIn('students.student_groups_id', $groupIds)
            ->select(
                'students.student_groups_id',
                DB::raw('COUNT(DISTINCT survey_answers.account_id) as respondents')
            )
            ->groupBy('students.student_groups_id')
            ->pluck('respondents', 'student_groups_id');

        // ── استعلام 2: إجمالي الطلاب في كل مجموعة ───────────────────────────
        $totalByGroup = Student::whereIn('student_groups_id', $groupIds)
            ->select('student_groups_id', DB::raw('COUNT(*) as total'))
            ->groupBy('student_groups_id')
            ->pluck('total', 'student_groups_id');

        // ── تجميع النتائج ─────────────────────────────────────────────────────
        $stats = [];
        foreach ($groups as $group) {
            $respondents = (int) ($respondentsByGroup[$group->id] ?? 0);
            $total       = (int) ($totalByGroup[$group->id] ?? 0);
            $notReplied  = max(0, $total - $respondents);
            $rate        = $total > 0 ? round(($respondents / $total) * 100, 1) : 0.0;

            $stats[] = [
                'id'          => $group->id,
                'name'        => $group->name,
                'respondents' => $respondents,
                'not_replied' => $notReplied,
                'total'       => $total,
                'rate'        => $rate,
                'color'       => $rate >= 80 ? 'green' : ($rate >= 50 ? 'amber' : 'red'),
            ];
        }

        // ترتيب تنازلي حسب النسبة
        usort($stats, fn($a, $b) => $b['rate'] <=> $a['rate']);

        return $stats;
    }

    // ─── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        Gate::authorize('survey.export');

        $surveys = Status::whereIn('p_id_sub', [config('appConstant.survey_for')])
            ->orderBy('status_name')
            ->get();

        return view('livewire.org-app.survey-questions.statistics', [
            'surveys'      => $surveys,
            'batchNumbers' => $this->availableBatches,
        ])->title(__('Survey Statistics'));
    }
}
