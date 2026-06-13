<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\ActivitySchedule;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\SurveyAnswer;
use App\Models\SurveyTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class EducationDirectorDashboard extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $selectedGroupId = '';
    public $selectedBatchNo = '';
    public array $chartData = ['labels' => [], 'series' => []];

    // Section 5 – Supervisor Reports
    public $reportSearchGroup = '';
    public $reportSearchBatch = '';

    // Section 6 – Survey Assessment Stats (pre/post per batch)
    public $surveyBatchNo = '';

    public function mount()
    {
        if (Gate::denies('manager.reports.all') && Gate::denies('select.any.student') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have the necessary permissions.');
        }

        $highestBatch = StudentGroup::where('activation', 1)
            ->whereNotNull('batch_no')
            ->where('batch_no', '!=', '')
            ->max('batch_no') ?? '';

        $this->selectedBatchNo = $highestBatch;
        $this->reportSearchBatch = $highestBatch;
        $this->surveyBatchNo = $highestBatch;

        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function clearFilters()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->selectedGroupId = '';
        $this->selectedBatchNo = '';
    }

    public function getMetricsProperty()
    {
        $schedulesQuery = ActivitySchedule::query()
            ->whereDate('period_start', '>=', $this->dateFrom)
            ->whereDate('period_start', '<=', $this->dateTo)
            ->whereIn('educational_activity_domain', [187, 188, 190])
            ->completed()
            ->active();

        if ($this->selectedGroupId) {
            $schedulesQuery->where('group_id', $this->selectedGroupId);
        }

        $schedules = $schedulesQuery->with('activityDetail')->get();

        $totalExecuted = $schedules->count();

        $pairs = [];
        foreach ($schedules as $schedule) {
            if ($schedule->group_id && $schedule->period_start && $schedule->educational_period_groups) {
                $dateStr = Carbon::parse($schedule->period_start)->format('Y-m-d');
                $periodGroup = $schedule->educational_period_groups;
                $key = $schedule->group_id . '_' . $dateStr . '_' . $periodGroup;
                $pairs[$key] = [
                    'group_id'     => $schedule->group_id,
                    'date'         => $dateStr,
                    'period_group' => $periodGroup,
                ];
            }
        }

        $totalAttendance = 0;
        if (!empty($pairs)) {
            $groupIds  = collect($pairs)->pluck('group_id')->unique()->toArray();
            $dates     = collect($pairs)->pluck('date')->unique()->toArray();
            $statusIds = collect($pairs)->pluck('period_group')->unique()->toArray();

            $attendanceRows = DB::table('student_daily_attendances')
                ->join('students', 'student_daily_attendances.student_id', '=', 'students.id')
                ->whereIn('student_daily_attendances.student_group_id', $groupIds)
                ->whereIn('student_daily_attendances.attendance_date', $dates)
                ->whereIn('students.status_id', $statusIds)
                ->where('student_daily_attendances.status', 'present')
                ->select(
                    'student_daily_attendances.student_group_id',
                    'student_daily_attendances.attendance_date',
                    'students.status_id',
                    DB::raw("COUNT(*) as present_count")
                )
                ->groupBy('student_daily_attendances.student_group_id', 'student_daily_attendances.attendance_date', 'students.status_id')
                ->get();

            $attendanceCounts = [];
            foreach ($attendanceRows as $row) {
                $key = $row->student_group_id . '_' . $row->attendance_date . '_' . $row->status_id;
                $attendanceCounts[$key] = $row->present_count;
            }

            foreach ($schedules as $schedule) {
                if ($schedule->group_id && $schedule->period_start && $schedule->educational_period_groups) {
                    $dateStr = Carbon::parse($schedule->period_start)->format('Y-m-d');
                    $key = $schedule->group_id . '_' . $dateStr . '_' . $schedule->educational_period_groups;
                    $totalAttendance += $attendanceCounts[$key] ?? 0;
                }
            }
        }

        $executionDates = $schedules->map(fn($s) => Carbon::parse($s->period_start)->format('Y-m-d'))->unique();
        $executionDaysCount = $executionDates->count();
        $avgDailyAttendance = $executionDaysCount > 0 ? ($totalAttendance / $executionDaysCount) : 0;

        $executedEducational = $schedules->where('educational_activity_domain', 187)->count();
        $executedPsychological = $schedules->where('educational_activity_domain', 188)->count();
        $executedValues = $schedules->where('educational_activity_domain', 190)->count();

        // 1. نسبة الحضور الأسبوعية: (إجمالي الحضور الفعلي) ÷ (عدد الأطفال المسجلين × عدد أيام الأنشطة) × 100
        $activeGroupIds = $schedules->pluck('group_id')->unique()->toArray();
        $registeredStudentsCount = \App\Models\Student::whereIn('student_groups_id', $activeGroupIds)
            ->where('activation', 1)
            ->count();
        $weeklyAttendanceRate = ($registeredStudentsCount * $executionDaysCount) > 0
            ? (($totalAttendance / ($registeredStudentsCount * $executionDaysCount)) * 100)
            : 0;

        // 2. نسبة الانسجام الأسبوعية: متوسط عدد المنسجمين ÷ متوسط عدد الحضور × 100
        // وهي تعادل رياضياً: (إجمالي المنسجمين ÷ إجمالي الحضور) × 100
        $totalConsistent = $schedules->sum(fn($s) => (int)($s->activityDetail->consistent ?? 0));
        $weeklyHarmonyRate = $totalAttendance > 0
            ? (($totalConsistent / $totalAttendance) * 100)
            : 0;

        // 3. عدد الصور المرفوعة: عدد ملفات الصور في تقارير الأنشطة
        $totalImagesCount = 0;
        foreach ($schedules as $schedule) {
            if ($schedule->activityDetail && is_array($schedule->activityDetail->attchments)) {
                foreach ($schedule->activityDetail->attchments as $att) {
                    $path = $att['path'] ?? '';
                    $ext = strtolower($att['extension'] ?? pathinfo($path, PATHINFO_EXTENSION));
                    $typeId = $att['type_id'] ?? null;
                    if ($typeId == 48 || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                        $totalImagesCount++;
                    }
                }
            }
        }

        return [
            'total_executed' => $totalExecuted,
            'executed_educational' => $executedEducational,
            'executed_psychological' => $executedPsychological,
            'executed_values' => $executedValues,
            'total_attendance' => $totalAttendance,
            'avg_daily_attendance' => round($avgDailyAttendance, 2),
            'weekly_attendance_rate' => round($weeklyAttendanceRate, 2),
            'weekly_harmony_rate' => round($weeklyHarmonyRate, 2),
            'total_images_count' => $totalImagesCount,
        ];
    }

    public function getChartData()
    {
        $attendanceQuery = DB::table('student_daily_attendances')
            ->whereDate('attendance_date', '>=', $this->dateFrom)
            ->whereDate('attendance_date', '<=', $this->dateTo);

        if ($this->selectedGroupId) {
            $attendanceQuery->where('student_group_id', $this->selectedGroupId);
        }

        $attendanceData = $attendanceQuery
            ->select(
                'attendance_date',
                DB::raw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count"),
                DB::raw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            )
            ->groupBy('attendance_date')
            ->orderBy('attendance_date')
            ->get();

        $labels = $attendanceData->map(fn($row) => Carbon::parse($row->attendance_date)->format('Y-m-d'))->toArray();
        $present = $attendanceData->map(fn($row) => (int)$row->present_count)->toArray();
        $absent = $attendanceData->map(fn($row) => (int)$row->absent_count)->toArray();

        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'الحضور',
                    'data' => $present,
                ],
                [
                    'name' => 'الغياب',
                    'data' => $absent,
                ],
            ],
        ];
    }

    public function getSurveyMetricsProperty()
    {
        $surveyQuery = DB::table('survey_answers')
            ->join('students', 'survey_answers.account_id', '=', 'students.identity_number')
            ->join('student_groups', 'students.student_groups_id', '=', 'student_groups.id')
            ->where('survey_answers.survey_no', 120);

        if ($this->selectedGroupId) {
            $surveyQuery->where('students.student_groups_id', $this->selectedGroupId);
        }

        if ($this->selectedBatchNo) {
            $surveyQuery->where('student_groups.batch_no', $this->selectedBatchNo);
        }

        $answers = $surveyQuery->select(
            'survey_answers.account_id',
            'survey_answers.question_id',
            'survey_answers.answer_ar_text'
        )->get();

        $studentAnswers = [];
        foreach ($answers as $row) {
            $studentAnswers[$row->account_id][$row->question_id] = trim($row->answer_ar_text);
        }

        $totalRegistered = count($studentAnswers);

        $age6to9Count = 0;
        $age10to12Count = 0;
        $maleCount = 0;
        $femaleCount = 0;
        $elearningCount = 0;
        $faceToFaceCount = 0;
        $warInjuredCount = 0;
        $displacedCount = 0;
        $orphanCount = 0;
        $healthCount = 0;

        foreach ($studentAnswers as $accountId => $profile) {
            // Age: Question ID 105
            $age = (int)($profile[105] ?? 0);
            if ($age >= 6 && $age <= 9) {
                $age6to9Count++;
            }
            if ($age >= 10 && $age <= 12) {
                $age10to12Count++;
            }

            // Gender: Question ID 101
            $gender = $profile[101] ?? '';
            if ($gender == '2') {
                $maleCount++;
            } elseif ($gender == '3') {
                $femaleCount++;
            }

            // E-learning: Question ID 12
            $elearning = $profile[12] ?? '';
            if ($elearning === 'نعم') {
                $elearningCount++;
            }

            // Face-to-face: Question ID 13
            $faceToFace = $profile[13] ?? '';
            if ($faceToFace === 'نعم') {
                $faceToFaceCount++;
            }

            // War-injured: Question ID 9
            $warInjured = $profile[9] ?? '';
            if ($warInjured === 'نعم') {
                $warInjuredCount++;
            }

            // Displaced: Question ID 6
            $displaced = $profile[6] ?? '';
            if ($displaced === 'نازحة' || $displaced === 'نازح') {
                $displacedCount++;
            }

            // Orphan: Question ID 7
            $orphan = $profile[7] ?? '';
            if ($orphan === 'نعم') {
                $orphanCount++;
            }

            // Health issues: Question ID 10
            $health = $profile[10] ?? '';
            if ($health === 'نعم') {
                $healthCount++;
            }
        }

        $calcPct = fn($count) => $totalRegistered > 0 ? round(($count / $totalRegistered) * 100, 1) : 0;

        return [
            'total_registered' => [
                'count' => $totalRegistered,
                'pct'   => 100.0,
            ],
            'age_6_9' => [
                'count' => $age6to9Count,
                'pct'   => $calcPct($age6to9Count),
            ],
            'age_10_12' => [
                'count' => $age10to12Count,
                'pct'   => $calcPct($age10to12Count),
            ],
            'male' => [
                'count' => $maleCount,
                'pct'   => $calcPct($maleCount),
            ],
            'female' => [
                'count' => $femaleCount,
                'pct'   => $calcPct($femaleCount),
            ],
            'elearning' => [
                'count' => $elearningCount,
                'pct'   => $calcPct($elearningCount),
            ],
            'face_to_face' => [
                'count' => $faceToFaceCount,
                'pct'   => $calcPct($faceToFaceCount),
            ],
            'war_injured' => [
                'count' => $warInjuredCount,
                'pct'   => $calcPct($warInjuredCount),
            ],
            'displaced' => [
                'count' => $displacedCount,
                'pct'   => $calcPct($displacedCount),
            ],
            'orphan' => [
                'count' => $orphanCount,
                'pct'   => $calcPct($orphanCount),
            ],
            'health_issues' => [
                'count' => $healthCount,
                'pct'   => $calcPct($healthCount),
            ],
        ];
    }

    /**
     * القسم السادس: إحصائيات التقييم القبلي والبعدي مجمعةً حسب الدفعة.
     * يُرجع مصفوفة من الصفوف، كل صف يمثل زوج (نوع التقييم × الفئة العمرية).
     * لكل زوج: العدد المستهدف، المستجيبون للتقييم القبلي ونسبتهم،
     *           والمستجيبون للتقييم البعدي ونسبتهم.
     */
    public function getSurveyAssessmentStatsProperty(): array
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        // ── 1. جلب جميع استبيانات القبلي والبعدي مجمعةً حسب (section × target × age range)
        $surveys = SurveyTable::with(['sectionRel', 'targetRel'])
            ->whereNotNull('survey_for_section')
            ->whereNotNull('survey_target')
            ->orderBy('survey_for_section')
            ->orderBy('survey_target')
            ->orderBy('from_age')
            ->get();

        if ($surveys->isEmpty()) {
            return [];
        }

        // ── 2. تجميع الاستبيانات حسب (short_name × target × age range)
        //    نستخدم الاسم المختصر كجزء من المفتاح حتى يجتمع القبلي والبعدي في صف واحد
        $groupedSurveys = [];
        foreach ($surveys as $survey) {
            $sectionName = $survey->sectionRel?->status_name ?? (string) $survey->survey_for_section;
            $shortName   = $this->abbreviateSurveyName($sectionName);

            // المفتاح: الاسم المختصر + الفئة المستهدفة + نطاق العمر
            $key = $shortName . '||' . $survey->survey_target
                . '||' . ($survey->from_age ?? 'null') . '||' . ($survey->to_age ?? 'null');

            if (!isset($groupedSurveys[$key])) {
                $groupedSurveys[$key] = [
                    'section_name' => $sectionName,
                    'target_name'  => $survey->targetRel?->status_name ?? $survey->survey_target,
                    'from_age'     => $survey->from_age,
                    'to_age'       => $survey->to_age,
                    'pre_survey'   => null,  // survey_for_section للقبلي (semester=1)
                    'post_survey'  => null,  // survey_for_section للبعدي (semester=2)
                    'pre_name'     => null,
                    'post_name'    => null,
                ];
            }

            if ((int) $survey->semester === 1) {
                $groupedSurveys[$key]['pre_survey'] = $survey->survey_for_section;
                $groupedSurveys[$key]['pre_name']   = $survey->survey_name;
            } elseif ((int) $survey->semester === 2) {
                $groupedSurveys[$key]['post_survey'] = $survey->survey_for_section;
                $groupedSurveys[$key]['post_name']   = $survey->survey_name;
            }
        }

        // ── 3. تحديد المجموعات (student_groups) المفلترة حسب الدفعة
        $groupQuery = StudentGroup::where('activation', 1);
        if ($this->surveyBatchNo) {
            $groupQuery->where('batch_no', $this->surveyBatchNo);
        }
        $filteredGroups = $groupQuery->get();
        $filteredGroupIds = $filteredGroups->pluck('id')->toArray();

        if (empty($filteredGroupIds)) {
            return [];
        }

        // ── 4. حساب إحصائيات كل مجموعة
        $results = [];

        foreach ($groupedSurveys as $key => $info) {
            $fromAge = $info['from_age'];
            $toAge   = $info['to_age'];

            // ── 4a. إجمالي الطلاب المستهدفين ضمن نطاق العمر في المجموعات المفلترة
            $totalQuery = Student::whereIn('students.student_groups_id', $filteredGroupIds)
                ->join('student_groups', 'students.student_groups_id', '=', 'student_groups.id');

            if ($fromAge !== null || $toAge !== null) {
                if ($isSqlite) {
                    $totalQuery->whereRaw(
                        '(strftime("%Y", student_groups.start_date) - strftime("%Y", students.birth_date)) BETWEEN ? AND ?',
                        [$fromAge ?? 0, $toAge ?? 999]
                    );
                } else {
                    $totalQuery->whereRaw(
                        'TIMESTAMPDIFF(YEAR, students.birth_date, student_groups.start_date) BETWEEN ? AND ?',
                        [$fromAge ?? 0, $toAge ?? 999]
                    );
                }
            }

            $targetCount = $totalQuery->count();

            // ── 4b. دالة مساعدة لحساب المستجيبين لاستبيان معين
            $countRespondents = function (?int $surveySection) use ($filteredGroupIds, $fromAge, $toAge, $isSqlite): int {
                if ($surveySection === null) {
                    return 0;
                }

                $q = SurveyAnswer::where('survey_answers.survey_no', $surveySection)
                    ->join('students', 'survey_answers.account_id', '=', 'students.identity_number')
                    ->join('student_groups', 'students.student_groups_id', '=', 'student_groups.id')
                    ->whereIn('students.student_groups_id', $filteredGroupIds);

                if ($fromAge !== null || $toAge !== null) {
                    if ($isSqlite) {
                        $q->whereRaw(
                            '(strftime("%Y", student_groups.start_date) - strftime("%Y", students.birth_date)) BETWEEN ? AND ?',
                            [$fromAge ?? 0, $toAge ?? 999]
                        );
                    } else {
                        $q->whereRaw(
                            'TIMESTAMPDIFF(YEAR, students.birth_date, student_groups.start_date) BETWEEN ? AND ?',
                            [$fromAge ?? 0, $toAge ?? 999]
                        );
                    }
                }

                return (int) $q->distinct('survey_answers.account_id')->count('survey_answers.account_id');
            };

            $preCount  = $countRespondents($info['pre_survey']);
            $postCount = $countRespondents($info['post_survey']);

            $preRate  = $targetCount > 0 ? round(($preCount  / $targetCount) * 100, 1) : null;
            $postRate = $targetCount > 0 ? round(($postCount / $targetCount) * 100, 1) : null;

            $results[] = [
                'section_name' => $info['section_name'],
                'short_name'   => $this->abbreviateSurveyName($info['section_name']),
                'target_name'  => $info['target_name'],
                'from_age'     => $fromAge,
                'to_age'       => $toAge,
                'target_count' => $targetCount,
                'pre_name'     => $info['pre_name'],
                'pre_count'    => $preCount,
                'pre_rate'     => $preRate,
                'post_name'    => $info['post_name'],
                'post_count'   => $postCount,
                'post_rate'    => $postRate,
            ];
        }

        return $results;
    }

    /**
     * تحويل اسم الاستبيان الطويل إلى تسمية مختصرة بناءً على الكلمات المفتاحية.
     */
    private function abbreviateSurveyName(string $name): string
    {
        if (str_contains($name, 'دعم نفسي')) {
            return 'تقييم دعم نفسي ذاتي';
        }
        if (str_contains($name, 'تعليم')) {
            return 'تقييم تعليمي ذاتي';
        }
        if (str_contains($name, 'قيم تربوية') || str_contains($name, 'مهارات')) {
            return 'تقييم القيم التربوية';
        }
        if (str_contains($name, 'مقياس القوة')) {
            return 'مقياس القوة والصعوبات';
        }
        if (str_contains($name, 'بيانات الطالب') || str_contains($name, 'بيانات طالب')) {
            return 'بيانات الطالب الأساسية';
        }
        return $name;
    }

    public function getSupervisorReportsProperty()
    {
        // Fetch all reports addressed to the director (or visible to all if superadmin)
        $query = DB::table('reports')
            ->leftJoin('employees', 'reports.employee_id', '=', 'employees.id')
            ->select(
                'reports.id',
                'reports.report_name',
                'reports.report_date',
                'reports.date_from',
                'reports.date_to',
                'reports.batch_no',
                'reports.student_group_ids',
                'reports.covered_educational_activities_ids',
                'reports.covered_educational_activity_schedules_ids',
                'reports.is_read',
                'employees.full_name as submitter_name'
            )
            ->orderByDesc('reports.id');

        // Filter by selected group if set
        if ($this->reportSearchGroup) {
            $query->whereJsonContains('reports.student_group_ids', (int) $this->reportSearchGroup);
        }

        // Filter by batch_no if set
        if ($this->reportSearchBatch) {
            $query->where('reports.batch_no', $this->reportSearchBatch);
        }

        $rawReports = $query->get();

        $allRows = [];

        // Enrich and split each report per body
        foreach ($rawReports as $report) {
            // Get report bodies
            $bodies = DB::table('report_body')
                ->where('report_id', $report->id)
                ->orderBy('item_order')
                ->get()
                ->map(function ($body) {
                    // Parse attachments (double-encoded JSON)
                    $rawAtts = $body->attachments;
                    $atts = [];
                    if ($rawAtts) {
                        $decoded = json_decode($rawAtts, true);
                        if (is_array($decoded)) {
                            foreach ($decoded as $item) {
                                if (is_string($item)) {
                                    $inner = json_decode($item, true);
                                    if ($inner) {
                                        $atts[] = $inner;
                                    }
                                } elseif (is_array($item)) {
                                    $atts[] = $item;
                                }
                            }
                        }
                    }
                    $body->parsed_attachments = $atts;
                    return $body;
                });

            // Decode covered activities and schedules
            $activityIds = json_decode($report->covered_educational_activities_ids ?? '[]', true);
            $schedIds = json_decode($report->covered_educational_activity_schedules_ids ?? '[]', true);

            // Fallback for older reports
            if (empty($activityIds) && !empty($schedIds)) {
                $activityIds = DB::table('educational_activity_schedules')
                    ->whereIn('id', $schedIds)
                    ->pluck('activity_name')
                    ->filter()
                    ->unique()
                    ->toArray();
            }

            // Resolve activity names and domains
            $activitiesMap = [];
            if (!empty($activityIds)) {
                $activitiesMap = DB::table('educational_activity_names')
                    ->leftJoin('statuses', 'educational_activity_names.activity_domain', '=', 'statuses.id')
                    ->select('educational_activity_names.id', 'educational_activity_names.activity_name', 'statuses.status_name as domain_name')
                    ->whereIn('educational_activity_names.id', $activityIds)
                    ->get()
                    ->keyBy('id')
                    ->all();
            }

            // Resolve group names from student_group_ids
            $groupIds = json_decode($report->student_group_ids ?? '[]', true);
            $groupNames = [];
            if (!empty($groupIds)) {
                $groupNames = DB::table('student_groups')
                    ->whereIn('id', $groupIds)
                    ->pluck('name')
                    ->toArray();
            }

            if ($bodies->isEmpty()) {
                $row = clone $report;
                $row->bodies = collect();
                $row->activity_name = null;
                $row->domain_name   = null;
                $row->group_names   = $groupNames;
                if (!empty($activitiesMap)) {
                    $firstAct = reset($activitiesMap);
                    $row->activity_name = $firstAct->activity_name;
                    $row->domain_name   = $firstAct->domain_name;
                }
                $allRows[] = $row;
            } else {
                foreach ($bodies as $idx => $body) {
                    $row = clone $report;
                    $row->bodies = collect([$body]);
                    $row->group_names = $groupNames;

                    $actId = $activityIds[$idx] ?? null;
                    $activityName = null;
                    $domainName = null;
                    if ($actId && isset($activitiesMap[$actId])) {
                        $activityName = $activitiesMap[$actId]->activity_name;
                        $domainName   = $activitiesMap[$actId]->domain_name;
                    } else {
                        // Fallback: if we don't have a specific actId for this index, use the first activity
                        if (!empty($activitiesMap)) {
                            $firstAct = reset($activitiesMap);
                            $activityName = $firstAct->activity_name;
                            $domainName   = $firstAct->domain_name;
                        }
                    }

                    $row->activity_name = $activityName;
                    $row->domain_name   = $domainName;
                    $allRows[] = $row;
                }
            }
        }

        return collect($allRows);
    }

    public function render()
    {
        $groups = StudentGroup::where('activation', 1)->orderBy('id', 'desc')->get();
        $batches = StudentGroup::where('activation', 1)
            ->whereNotNull('batch_no')
            ->where('batch_no', '!=', '')
            ->distinct()
            ->orderBy('batch_no', 'asc')
            ->pluck('batch_no');

        // Populate public property so it can be entangled
        $this->chartData = $this->getChartData();

        return view('livewire.org-app.reports.education-director-dashboard', [
            'metrics'               => $this->metrics,
            'surveyMetrics'         => $this->surveyMetrics,
            'groups'                => $groups,
            'batches'               => $batches,
            'supervisorReports'     => $this->supervisorReports,
            'surveyAssessmentStats' => $this->surveyAssessmentStats,
        ]);
    }
}
