<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\ActivitySchedule;
use App\Models\Employee;
use App\Models\StudentGroup;
use App\Models\TeacherStudentGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Concerns\AccessibleGroupsTrait;


class SupervisorActivitiesReport extends Component
{
    use AccessibleGroupsTrait;

    public $dateFrom;
    public $dateTo;
    public $selectedBatch = '';
    public $selectedGroup = '';
    public $selectedSupervisorId = '';
    public $selectedActivityName = '';
    public $selectedReportStatus = '';
    public $canSelectSupervisor = false;

    // Selection state
    public array $selectedActivities = [];

    public function mount()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');

        $user = auth()->user();
        $isAdmin = $user->isSuperAdmin() || Gate::allows('reports.all') || Gate::allows('select.any.student');

        if ($isAdmin) {
            $this->canSelectSupervisor = true;
        } else {
            $this->selectedSupervisorId = $user->id;
            $this->canSelectSupervisor = false;

            $groupIds = $this->accessibleGroupIds;
            $this->selectedBatch = StudentGroup::whereIn('id', is_array($groupIds) ? $groupIds : [])
                ->whereNotNull('batch_no')
                ->orderByDesc('batch_no')
                ->value('batch_no') ?? '';
            $this->selectedGroup = is_array($groupIds) && count($groupIds) === 1 ? (string) $groupIds[0] : '';
        }
    }

    public function updatedSelectedBatch()
    {
        $this->selectedGroup = '';
    }

    private function getGroupedActivities(?array $restrictToKeys = null)
    {
        $user = auth()->user();
        $isSupervisor = TeacherStudentGroup::where('teacher_id', $user->id)
            ->where('job_title', 167)
            ->exists();

        $isAdmin = $user->isSuperAdmin() || Gate::allows('reports.all') || Gate::allows('select.any.student');

        if (!$isAdmin && !$isSupervisor) {
            abort(403, 'You do not have the necessary permissions.');
        }

        // 1. Get allowed groups
        if ($isAdmin) {
            if ($this->selectedSupervisorId) {
                $allowedGroupIds = TeacherStudentGroup::where('teacher_id', $this->selectedSupervisorId)
                    ->pluck('student_group_id')
                    ->unique()
                    ->toArray();
            } else {
                $allowedGroupIds = StudentGroup::where('activation', 1)->pluck('id')->toArray();
            }
        } else {
            $allowedGroupIds = TeacherStudentGroup::where('teacher_id', $user->id)
                ->pluck('student_group_id')
                ->unique()
                ->toArray();
        }

        // 2. Fetch ActivitySchedules for these groups
        $schedulesQuery = ActivitySchedule::query()
            ->whereIn('group_id', $allowedGroupIds)
            ->with(['activityNameStatus', 'activityDomain', 'activityDetail', 'group.teacherAssignments.teacher', 'employee', 'periodGroups']);

        if ($restrictToKeys !== null) {
            // When restricting to specific compound keys, skip all date/filter conditions
            // because the compound key (group_id + activity_name) already uniquely identifies the set
            $schedulesQuery->where(function ($q) use ($restrictToKeys) {
                foreach ($restrictToKeys as $key) {
                    $parts = explode('_', $key, 2);
                    if (count($parts) === 2) {
                        $q->orWhere(function ($sub) use ($parts) {
                            $sub->where('group_id', $parts[0])
                                ->where('activity_name', $parts[1]);
                        });
                    }
                }
            });
        } else {
            // Apply standard filters only when not restricting to specific keys.
            // Use whereDate() with plain date strings to avoid timezone shifting issues.
            // Carbon::parse()->startOfDay() sends UTC timestamps that can mismatch local DB values.
            if ($this->dateFrom) {
                $schedulesQuery->whereDate('period_start', '>=', $this->dateFrom);
            }

            if ($this->dateTo) {
                $schedulesQuery->whereDate('period_start', '<=', $this->dateTo);
            }

            if ($this->selectedBatch) {
                $schedulesQuery->whereHas('group', function ($q) {
                    $q->where('batch_no', $this->selectedBatch);
                });
            }

            if ($this->selectedGroup) {
                $schedulesQuery->where('group_id', $this->selectedGroup);
            }

            if ($this->selectedActivityName) {
                $schedulesQuery->where('activity_name', $this->selectedActivityName);
            }
        }

        $schedules = $schedulesQuery->get();

        // 3. Batch fetch student attendance counts to avoid N+1 queries
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

        $attendanceCounts = [];
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

            foreach ($attendanceRows as $row) {
                $key = $row->student_group_id . '_' . $row->attendance_date . '_' . $row->status_id;
                $attendanceCounts[$key] = $row->present_count;
            }
        }

        // 4. Group by group_id and activity_name and aggregate
        $groupedActivities = [];
        foreach ($schedules as $schedule) {
            $group = $schedule->group;
            if (!$group) continue;

            $groupId         = $group->id;
            $activityNameId  = $schedule->activity_name;
            $compoundKey     = $groupId . '_' . $activityNameId;

            $activityNameKey = $schedule->activityNameStatus ? $schedule->activityNameStatus->activity_name : ($schedule->activity_name ?: __('Unknown Activity'));
            $domainName      = $schedule->activityDomain ? $schedule->activityDomain->status_name : ($schedule->educational_activity_domain ?: '-');

            // Find supervisor teacher for this group (job_title == 167)
            $supervisorName       = '-';
            $supervisorAssignment = $group->teacherAssignments
                ? $group->teacherAssignments->where('job_title', 167)->first()
                : null;
            if ($supervisorAssignment && $supervisorAssignment->teacher) {
                $supervisorName = $supervisorAssignment->teacher->full_name;
            }

            // Find attendance count for this schedule
            $presentCount = 0;
            if ($schedule->group_id && $schedule->period_start && $schedule->educational_period_groups) {
                $dateStr      = Carbon::parse($schedule->period_start)->format('Y-m-d');
                $key          = $schedule->group_id . '_' . $dateStr . '_' . $schedule->educational_period_groups;
                $presentCount = $attendanceCounts[$key] ?? 0;
            }

            if (!isset($groupedActivities[$compoundKey])) {
                $groupedActivities[$compoundKey] = [
                    'compound_key'      => $compoundKey,
                    'group_id'          => $groupId,
                    'activity_name_id'  => $activityNameId,
                    'batch_no'          => $group->batch_no ?: '-',
                    'group_name'        => $group->name ?: '-',
                    'activity_name'     => $activityNameKey,
                    'domain_name'       => $domainName,
                    'supervisor_name'   => $supervisorName,
                    'total_attendance'  => 0,
                    'total_consistent'  => 0,
                    'what_learned'      => [],
                    'teacher_report_detail' => [],
                    'schedule_ids'      => [],
                    'detail_ids'        => [],
                    'attachments'       => [],
                ];
            }

            $groupedActivities[$compoundKey]['total_attendance'] += $presentCount;
            $groupedActivities[$compoundKey]['schedule_ids'][]    = $schedule->id;

            if ($schedule->activityDetail) {
                $groupedActivities[$compoundKey]['total_consistent'] += (int) $schedule->activityDetail->consistent;
                $groupedActivities[$compoundKey]['detail_ids'][]      = $schedule->activityDetail->id;

                $teacherName = $schedule->employee ? $schedule->employee->full_name : '-';
                $periodGroupName = $schedule->periodGroups ? $schedule->periodGroups->status_name : '-';

                if (!empty(trim($schedule->activityDetail->what_learned))) {
                    $groupedActivities[$compoundKey]['what_learned'][] = [
                        'text' => trim($schedule->activityDetail->what_learned),
                        'teacher' => $teacherName,
                        'period_group' => $periodGroupName,
                    ];
                }
                if (!empty(trim($schedule->activityDetail->teacher_report_detail))) {
                    $groupedActivities[$compoundKey]['teacher_report_detail'][] = [
                        'text' => trim($schedule->activityDetail->teacher_report_detail),
                        'teacher' => $teacherName,
                        'period_group' => $periodGroupName,
                    ];
                }
                if (!empty($schedule->activityDetail->attchments) && is_array($schedule->activityDetail->attchments)) {
                    foreach ($schedule->activityDetail->attchments as $attachment) {
                        $groupedActivities[$compoundKey]['attachments'][] = $attachment;
                    }
                }
            }
        }

        // Fetch all reported schedule IDs from the database to check which activities are already reported
        $reportedScheduleIds = DB::table('reports')
            ->whereNotNull('covered_educational_activity_schedules_ids')
            ->pluck('covered_educational_activity_schedules_ids')
            ->flatMap(function ($item) {
                $decoded = is_string($item) ? json_decode($item, true) : $item;
                return is_array($decoded) ? $decoded : [];
            })
            ->unique()
            ->toArray();

        // Clean up array lists
        foreach ($groupedActivities as &$act) {
            $act['what_learned']           = collect($act['what_learned'])->unique('text')->values()->toArray();
            $act['teacher_report_detail']  = collect($act['teacher_report_detail'])->unique('text')->values()->toArray();
            $act['schedule_ids']           = array_unique($act['schedule_ids']);
            $act['detail_ids']             = array_unique($act['detail_ids']);
            if (!empty($act['attachments'])) {
                $act['attachments'] = collect($act['attachments'])->unique(function ($item) {
                    return is_array($item) ? ($item['url'] ?? $item['path'] ?? json_encode($item)) : $item;
                })->values()->toArray();
            }

            // A grouped activity is reported if any of its schedules have been included in a report
            $act['is_reported'] = !empty($act['schedule_ids']) && collect($act['schedule_ids'])->contains(function ($id) use ($reportedScheduleIds) {
                return in_array($id, $reportedScheduleIds);
            });
        }
        unset($act);

        // Apply Report Status Filter (only when not restricting to specific keys)
        if ($restrictToKeys === null) {
            if ($this->selectedReportStatus === 'reported') {
                $groupedActivities = array_filter($groupedActivities, fn($act) => $act['is_reported']);
            } elseif ($this->selectedReportStatus === 'unreported') {
                $groupedActivities = array_filter($groupedActivities, fn($act) => !$act['is_reported']);
            }
        }

        return $groupedActivities;
    }

    public function openCreateReport(): void
    {
        if (empty($this->selectedActivities)) {
            return;
        }

        // Fetch data for selected activities only, bypassing date/batch/group filters
        // (restrictToKeys path skips all date filters to avoid timezone issues)
        $grouped = $this->getGroupedActivities($this->selectedActivities);

        $draftItems           = [];
        $allScheduleIds       = [];
        $allDetailIds         = [];
        $allActivityIds       = [];
        $allGroupIds          = [];

        foreach ($this->selectedActivities as $key) {
            if (!isset($grouped[$key])) {
                continue;
            }

            $item = $grouped[$key];

            // Safeguard: skip if already reported
            if (!empty($item['is_reported'])) {
                continue;
            }

            $whatLearnedJoined = implode("\n", array_map(function ($t) {
                if (is_array($t)) {
                    $details = [];
                    if (!empty($t['teacher']) && $t['teacher'] !== '-') {
                        $details[] = 'المعلم: ' . $t['teacher'];
                    }
                    if (!empty($t['period_group']) && $t['period_group'] !== '-') {
                        $details[] = 'الفترة: ' . $t['period_group'];
                    }
                    $detailsStr = !empty($details) ? ' (' . implode(' - ', $details) . ')' : '';
                    return '- ' . $t['text'] . $detailsStr;
                }
                return '- ' . $t;
            }, $item['what_learned']));

            $notesJoined = implode("\n", array_map(function ($t) {
                if (is_array($t)) {
                    $details = [];
                    if (!empty($t['teacher']) && $t['teacher'] !== '-') {
                        $details[] = 'المعلم: ' . $t['teacher'];
                    }
                    if (!empty($t['period_group']) && $t['period_group'] !== '-') {
                        $details[] = 'الفترة: ' . $t['period_group'];
                    }
                    $detailsStr = !empty($details) ? ' (' . implode(' - ', $details) . ')' : '';
                    return '- ' . $t['text'] . $detailsStr;
                }
                return '- ' . $t;
            }, $item['teacher_report_detail']));

            $draftContent      = "ما تعلمه الطلاب:\n" . $whatLearnedJoined . "\n\nملاحظات المعلمين:\n" . $notesJoined . "\n\nعدد الحضور للنشاط هو " . $item['total_attendance'] . " وعدد الاطفال المنسجمين بالنشاط هو " . $item['total_consistent'];

            $draftItems[] = [
                'title'                   => $item['activity_name'] . ' — ' . $item['group_name'],
                'content'                 => $draftContent,
                'observation'             => '',
                'attachments_pool'        => $item['attachments'],
                'selected_attachments'    => [],
                'source_schedule_ids'     => $item['schedule_ids'],
                'source_detail_ids'       => $item['detail_ids'],
                'source_activity_name_id' => $item['activity_name_id'],
                'source_group_id'         => $item['group_id'],
            ];

            $allScheduleIds  = array_merge($allScheduleIds, $item['schedule_ids']);
            $allDetailIds    = array_merge($allDetailIds, $item['detail_ids']);
            $allActivityIds[] = $item['activity_name_id'];
            $allGroupIds[]    = $item['group_id'];
        }

        // Store draft in session and redirect to the general report creation page
        session()->put('report_draft', [
            'source'                                       => 'supervisor_activities',
            'items'                                        => $draftItems,
            'date_from'                                    => $this->dateFrom,
            'date_to'                                      => $this->dateTo,
            'batch_no'                                     => $this->selectedBatch ?: null,
            'student_group_ids'                            => array_values(array_unique($allGroupIds)),
            'covered_educational_activities_ids'           => array_values(array_unique($allActivityIds)),
            'covered_educational_activity_schedules_ids'   => array_values(array_unique($allScheduleIds)),
            'covered_educational_activity_details_ids'     => array_values(array_unique($allDetailIds)),
        ]);

        $this->redirect(route('reports.create'), navigate: true);
    }

    public function render()
    {
        if (Gate::denies('reports.supervisor.activities.report')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $groupedActivities = $this->getGroupedActivities();

        // Sort grouped activities by batch_no and group_name
        usort($groupedActivities, function ($a, $b) {
            if ($a['batch_no'] !== $b['batch_no']) {
                return strcmp($a['batch_no'], $b['batch_no']);
            }
            if ($a['group_name'] !== $b['group_name']) {
                return strcmp($a['group_name'], $b['group_name']);
            }
            return strcmp($a['activity_name'], $b['activity_name']);
        });

        $user    = auth()->user();
        $isAdmin = $user->isSuperAdmin() || Gate::allows('reports.all') || Gate::allows('select.any.student');

        if ($isAdmin) {
            if ($this->selectedSupervisorId) {
                $allowedGroupIds = TeacherStudentGroup::where('teacher_id', $this->selectedSupervisorId)
                    ->pluck('student_group_id')
                    ->unique()
                    ->toArray();
            } else {
                $allowedGroupIds = StudentGroup::where('activation', 1)->pluck('id')->toArray();
            }
        } else {
            $allowedGroupIds = TeacherStudentGroup::where('teacher_id', $user->id)
                ->pluck('student_group_id')
                ->unique()
                ->toArray();
        }

        // Build distinct batch_no list for the filter dropdown from groups visible to this user
        $batchGroupQuery = StudentGroup::where('activation', 1)->whereNotNull('batch_no');
        if (!$isAdmin && !empty($allowedGroupIds)) {
            $batchGroupQuery->whereIn('id', $allowedGroupIds);
        }
        $batches = $batchGroupQuery->distinct()->orderBy('batch_no')->pluck('batch_no');

        $groupsQuery = StudentGroup::whereIn('id', $allowedGroupIds)->where('activation', 1);
        if ($this->selectedBatch) {
            $groupsQuery->where('batch_no', $this->selectedBatch);
        }
        $groups = $groupsQuery->orderBy('name')->get();


        $supervisors = [];
        if ($this->canSelectSupervisor) {
            $supervisors = Employee::whereIn('user_id', function ($query) {
                $query->select('teacher_id')
                    ->from('teacher_student_group')
                    ->where('job_title', 167);
            })->orderBy('full_name')->get();
        }

        $activityNamesList = \App\Models\EducationalActivityName::where('activation', 1)
            ->orderBy('activity_name')
            ->get();

        return view('livewire.org-app.reports.supervisor-activities-report', [
            'activities'       => $groupedActivities,
            'batches'          => $batches,
            'groups'           => $groups,
            'supervisors'      => $supervisors,
            'activityNamesList' => $activityNamesList,
        ]);
    }
}
