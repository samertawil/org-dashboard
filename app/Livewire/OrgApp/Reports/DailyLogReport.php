<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Activity;
use App\Models\ActivityComments;
use App\Models\CurrancyValue;
use App\Models\EducationalActivityDetail;
use App\Reposotries\EducationalActivityDetailRepo;
use App\Models\StudentDailyAttendance;
use App\Models\SurveyAnswer;
use App\Notifications\MentionInCommentNotification;
use App\Reposotries\employeeRepo;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;



class DailyLogReport extends Component
{
    public $reportDate;
    public $newComments = [];
    public $showComments = [];
    public $mentionableUsers = [];

    public function mount()
    {
        $this->reportDate = date('Y-m-d');
        $this->mentionableUsers =  employeeRepo::mentionEmp();
        // dd($this->mentionableUsers);
    }

    public function getActivitiesProperty()
    {
        return Activity::query()
            ->whereDate('created_at', $this->reportDate)
            ->with([
                'creator',
                'regions',
                'cities',
                'parcels.parcelType',
                'parcels.unit',
                'workTeams.employeeRel',
                'beneficiaries.beneficiaryType',
                'attachments',
                'comments.creator'
            ])
            ->latest()
            ->get();
    }

    public function getEvaluationsProperty()
    {
        // Direct Database Query is much safer for large tables like survey_answers
        return SurveyAnswer::query()
            ->whereDate('created_at', $this->reportDate)
            ->select(
                'survey_no',
                DB::raw('count(distinct account_id) as students_count')
            )
            ->with(['surveyfor'])
            ->groupBy('survey_no')
            ->get();
    }

    public function getAttendanceStatsProperty()
    {
        return StudentDailyAttendance::query()
            ->whereDate('created_at', $this->reportDate)
            ->select('student_group_id', DB::raw('count(*) as total_entries'))
            ->with('studentGroup')
            ->groupBy('student_group_id')
            ->get();
    }

    public function getExchangeRateProperty()
    {
        return CurrancyValue::whereDate('exchange_date', '<=', $this->reportDate)
            ->orderBy('exchange_date', 'desc')
            ->first();
    }

    public function getEducationalActivityDetailsProperty()
    {

        return EducationalActivityDetailRepo::getTeacherDetailsQuery()
            ->whereDate('created_at', $this->reportDate)
            ->with([
                'status',
                'educationalActivity.activityDomain',
                'educationalActivity.periodGroups',
                'educationalActivity.group',
                'educationalActivity.employee',
                'educationalActivity.createdBy',
            ])
            ->latest()
            ->get();
    }

    public function getAttendanceByGroupProperty()
    {
        // Get all unique (group_id, period_start date, educational_period_groups) triples
        $details = $this->educationalActivityDetails;
        $pairs = [];
        foreach ($details as $detail) {
            $schedule = $detail->educationalActivity;
            if ($schedule && $schedule->group_id && $schedule->period_start) {
                $dateStr = $schedule->period_start->format('Y-m-d');
                $periodGroup = $schedule->educational_period_groups;
                $key = $schedule->group_id . '_' . $dateStr . '_' . $periodGroup;
                $pairs[$key] = [
                    'group_id' => $schedule->group_id,
                    'date' => $dateStr,
                    'period_group' => $periodGroup,
                ];
            }
        }

        if (empty($pairs)) {
            return collect();
        }

        // Build a query matching (group_id, attendance_date, students.status_id = educational_period_groups)
        $query = DB::table('student_daily_attendances')
            ->join('students', 'student_daily_attendances.student_id', '=', 'students.id')
            ->join('statuses', 'students.status_id', '=', 'statuses.id')
            ->where(function ($q) use ($pairs) {
                foreach ($pairs as $pair) {
                    $q->orWhere(function ($sub) use ($pair) {
                        $sub->where('student_daily_attendances.student_group_id', $pair['group_id'])
                            ->whereDate('student_daily_attendances.attendance_date', $pair['date'])
                            ->where('students.status_id', $pair['period_group']);
                    });
                }
            })
            ->select(
                'student_daily_attendances.student_group_id',
                DB::raw("DATE(student_daily_attendances.attendance_date) as attendance_date"),
                'students.status_id',
                'statuses.status_name',
                DB::raw("SUM(CASE WHEN student_daily_attendances.status = 'present' THEN 1 ELSE 0 END) as present_count"),
                DB::raw("SUM(CASE WHEN student_daily_attendances.status = 'absent' THEN 1 ELSE 0 END) as absent_count"),
                DB::raw("COUNT(*) as total_count")
            )
            ->groupBy('student_daily_attendances.student_group_id', DB::raw("DATE(student_daily_attendances.attendance_date)"), 'students.status_id', 'statuses.status_name')
            ->get();

        // Key by "groupId_date_statusId" for precise per-card lookup
        return $query->groupBy(fn($row) => $row->student_group_id . '_' . $row->attendance_date . '_' . $row->status_id);
    }

    public function sendToWhatsApp()
    {
        // We can call the command logic directly or via Artisan
        // For simplicity and consistency, we'll use Artisan
        Artisan::call('report:send-daily-whatsapp');

        $this->dispatch(
            'flux-toast',
            variant: 'success',
            title: __('Report Sent'),
            description: __('The daily report has been sent to the manager via WhatsApp.')
        );
    }

    public function addComment($activityId)
    {
        $content = $this->newComments[$activityId] ?? '';

        if (empty(trim($content))) return;

        $comment = ActivityComments::create([
            'activity_id' => $activityId,
            'created_by' => auth()->id(),
            'comment' => $content,
        ]);

        $this->processMentions($comment);

        $this->newComments[$activityId] = '';

        $this->dispatch(
            'flux-toast',
            variant: 'success',
            title: __('Comment Added'),
            description: __('Your comment has been added successfully.')
        );
    }

    protected function processMentions($comment)
    {
        $content = $comment->comment;
        $mentionedUserIds = [];

        // Sort by length descending to match full names before partial names
        $employees = collect($this->mentionableUsers)->sortByDesc(fn($e) => mb_strlen($e['name']));

        foreach ($employees as $employee) {
            $name = $employee['name'];
            // Match the name with or without @ prefix
            if (mb_strpos($content, '@' . $name) !== false || mb_strpos($content, $name) !== false) {
                if (!in_array($employee['id'], $mentionedUserIds)) {
                    $mentionedUserIds[] = $employee['id'];
                }
            }
        }

        foreach ($mentionedUserIds as $userId) {
            if ($userId != auth()->id()) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->notify(new MentionInCommentNotification($comment->activity, $comment, auth()->user()));
                }
            }
        }
    }

    public function toggleComments($activityId)
    {
        $this->showComments[$activityId] = !($this->showComments[$activityId] ?? false);
    }

    public function render()

    {
        if (Gate::allows('manager.reports.all') || Gate::allows('activity.index') || Gate::allows('student.index')) {
            return view('livewire.org-app.reports.daily-log-report', [
                'activities' => $this->activities,
                'evaluations' => $this->evaluations,
                'attendanceStats' => $this->attendanceStats,
                'exchangeRate' => $this->exchangeRate,
                'educationalActivityDetails' => $this->educationalActivityDetails,
                'attendanceByGroup' => $this->attendanceByGroup,
            ]);
        }

        abort(403, 'You do not have the necessary permissions.');
    }
}
