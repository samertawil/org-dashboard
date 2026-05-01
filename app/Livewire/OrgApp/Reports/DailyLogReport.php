<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Activity;
use App\Models\CurrancyValue;
use App\Models\SurveyAnswer;
use App\Models\StudentDailyAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Services\WhatsAppService;
use App\Console\Commands\SendDailyLogReport;
use Illuminate\Support\Facades\Artisan;



class DailyLogReport extends Component
{
    public $reportDate;

    public function mount()
    {
        $this->reportDate = date('Y-m-d');
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
                'attachments'
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
        
        $this->dispatch('flux-toast', 
            variant: 'success', 
            title: __('Report Sent'), 
            description: __('The daily report has been sent to the manager via WhatsApp.')
        );
    }

    public function render()

    {
        if(Gate::denies('manager.reports.all')||Gate::denies('activity.index')||Gate::denies('student.index')){
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
