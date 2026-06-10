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

class SupervisorActivitiesReport extends Component
{
    public $dateFrom;
    public $dateTo;
    public $selectedBatch = '';
    public $selectedGroup = '';
    public $selectedSupervisorId = '';
    public $selectedActivityName = '';
    public $canSelectSupervisor = false;

    public function mount()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');

        $user = auth()->user();
        $isAdmin = $user->isSuperAdmin() || Gate::allows('reports.all') || Gate::allows('reports.groups.attendance');

        if ($isAdmin) {
            $this->canSelectSupervisor = true;
        } else {
            $this->selectedSupervisorId = $user->id;
            $this->canSelectSupervisor = false;
        }
    }

    public function updatedSelectedBatch()
    {
        $this->selectedGroup = '';
    }

    public function render()
    {
        $user = auth()->user();
        $isSupervisor = TeacherStudentGroup::where('teacher_id', $user->id)
            ->where('job_title', 167)
            ->exists();

        $isAdmin = $user->isSuperAdmin() || Gate::allows('reports.all') || Gate::allows('reports.groups.attendance');

        if (!$isAdmin && !$isSupervisor) {
            abort(403, 'You do not have the necessary permissions.');
        }

        // 1. Get allowed groups (Temporarily bypassed job_title = 167 constraint for testing/preview)
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
            ->with(['activityNameStatus', 'activityDomain', 'activityDetail', 'group.teacherAssignments.teacher']);

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

        $schedules = $schedulesQuery->get();

        // 3. Batch fetch student attendance counts to avoid N+1 queries
        $pairs = [];
        foreach ($schedules as $schedule) {
            if ($schedule->group_id && $schedule->period_start && $schedule->educational_period_groups) {
                $dateStr = Carbon::parse($schedule->period_start)->format('Y-m-d');
                $periodGroup = $schedule->educational_period_groups;
                $key = $schedule->group_id . '_' . $dateStr . '_' . $periodGroup;
                $pairs[$key] = [
                    'group_id' => $schedule->group_id,
                    'date' => $dateStr,
                    'period_group' => $periodGroup,
                ];
            }
        }

        $attendanceCounts = [];
        if (!empty($pairs)) {
            $attendanceRows = DB::table('student_daily_attendances')
                ->join('students', 'student_daily_attendances.student_id', '=', 'students.id')
                ->where(function ($q) use ($pairs) {
                    foreach ($pairs as $pair) {
                        $q->orWhere(function ($sub) use ($pair) {
                            $sub->where('student_daily_attendances.student_group_id', $pair['group_id'])
                                ->whereDate('student_daily_attendances.attendance_date', $pair['date'])
                                ->where('students.status_id', $pair['period_group']);
                        });
                    }
                })
                ->where('student_daily_attendances.status', 'present')
                ->select(
                    'student_daily_attendances.student_group_id',
                    DB::raw("DATE(student_daily_attendances.attendance_date) as attendance_date"),
                    'students.status_id',
                    DB::raw("COUNT(*) as present_count")
                )
                ->groupBy('student_daily_attendances.student_group_id', DB::raw("DATE(student_daily_attendances.attendance_date)"), 'students.status_id')
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

            $groupId = $group->id;
            $activityNameId = $schedule->activity_name;
            $compoundKey = $groupId . '_' . $activityNameId;

            $activityNameKey = $schedule->activityNameStatus ? $schedule->activityNameStatus->activity_name : ($schedule->activity_name ?: __('Unknown Activity'));
            $domainName = $schedule->activityDomain ? $schedule->activityDomain->status_name : ($schedule->educational_activity_domain ?: '-');

            // Find supervisor teacher for this group (job_title == 167)
            $supervisorName = '-';
            $supervisorAssignment = $group->teacherAssignments
                ? $group->teacherAssignments->where('job_title', 167)->first()
                : null;
            if ($supervisorAssignment && $supervisorAssignment->teacher) {
                $supervisorName = $supervisorAssignment->teacher->full_name;
            }

            // Find attendance count for this schedule
            $presentCount = 0;
            if ($schedule->group_id && $schedule->period_start && $schedule->educational_period_groups) {
                $dateStr = Carbon::parse($schedule->period_start)->format('Y-m-d');
                $key = $schedule->group_id . '_' . $dateStr . '_' . $schedule->educational_period_groups;
                $presentCount = $attendanceCounts[$key] ?? 0;
            }

            if (!isset($groupedActivities[$compoundKey])) {
                $groupedActivities[$compoundKey] = [
                    'batch_no' => $group->batch_no ?: '-',
                    'group_name' => $group->name ?: '-',
                    'activity_name' => $activityNameKey,
                    'domain_name' => $domainName,
                    'supervisor_name' => $supervisorName,
                    'total_attendance' => 0,
                    'total_consistent' => 0,
                    'what_learned' => [],
                    'teacher_report_detail' => [],
                ];
            }

            $groupedActivities[$compoundKey]['total_attendance'] += $presentCount;

            if ($schedule->activityDetail) {
                $groupedActivities[$compoundKey]['total_consistent'] += (int) $schedule->activityDetail->consistent;
                if (!empty(trim($schedule->activityDetail->what_learned))) {
                    $groupedActivities[$compoundKey]['what_learned'][] = trim($schedule->activityDetail->what_learned);
                }
                if (!empty(trim($schedule->activityDetail->teacher_report_detail))) {
                    $groupedActivities[$compoundKey]['teacher_report_detail'][] = trim($schedule->activityDetail->teacher_report_detail);
                }
            }
        }

        // Clean up array lists
        foreach ($groupedActivities as &$act) {
            $act['what_learned'] = array_unique($act['what_learned']);
            $act['teacher_report_detail'] = array_unique($act['teacher_report_detail']);
        }
        unset($act);

        // Sort grouped activities by batch_no and group_name
        usort($groupedActivities, function($a, $b) {
            if ($a['batch_no'] !== $b['batch_no']) {
                return strcmp($a['batch_no'], $b['batch_no']);
            }
            if ($a['group_name'] !== $b['group_name']) {
                return strcmp($a['group_name'], $b['group_name']);
            }
            return strcmp($a['activity_name'], $b['activity_name']);
        });

        // 5. Fetch options lists for filters
        $batches = StudentGroup::where('activation', 1)
            ->whereNotNull('batch_no')
            ->distinct()
            ->pluck('batch_no')
            ->sort()
            ->values();

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
            'activities' => $groupedActivities,
            'batches' => $batches,
            'groups' => $groups,
            'supervisors' => $supervisors,
            'activityNamesList' => $activityNamesList,
        ]);
    }
}
