<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Activity;
use App\Models\ActivityComments;
use App\Models\CurrancyValue;
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
        if (Gate::denies('manager.reports.all') || Gate::denies('activity.index') || Gate::denies('student.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.reports.daily-log-report', [
            'activities' => $this->activities,
            'evaluations' => $this->evaluations,
            'attendanceStats' => $this->attendanceStats,
            'exchangeRate' => $this->exchangeRate,
        ]);
    }
}
