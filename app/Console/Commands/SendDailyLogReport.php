<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\CurrancyValue;
use App\Models\SurveyAnswer;
use App\Models\StudentDailyAttendance;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SendDailyLogReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send-daily-whatsapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send the daily log report to the manager via WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsApp)
    {
        $this->info('Generating Daily Log Report...');
        
        $date = now()->format('Y-m-d');
        $managerPhone = config('services.whatsapp.manager_phone');

        if (!$managerPhone) {
            $this->error('Manager phone number not configured.');
            return 1;
        }

        // Fetch Data (similar to DailyLogReport component)
        $activities = Activity::query()
            ->whereDate('created_at', $date)
            ->with(['creator', 'regions', 'cities', 'parcels.parcelType', 'parcels.unit', 'beneficiaries.beneficiaryType'])
            ->latest()
            ->get();

        $evaluations = SurveyAnswer::query()
            ->whereDate('created_at', $date)
            ->select('survey_no', DB::raw('count(distinct account_id) as students_count'))
            ->with(['surveyfor'])
            ->groupBy('survey_no')
            ->get();

        $attendanceStats = StudentDailyAttendance::query()
            ->whereDate('created_at', $date)
            ->select('student_group_id', DB::raw('count(*) as total_entries'))
            ->with('studentGroup')
            ->groupBy('student_group_id')
            ->get();

        $exchangeRate = CurrancyValue::whereDate('exchange_date', '<=', $date)
            ->orderBy('exchange_date', 'desc')
            ->first();

        // Generate PDF
        $pdf = Pdf::loadView('pdfs.daily-log-report', [
            'date' => $date,
            'activities' => $activities,
            'evaluations' => $evaluations,
            'attendanceStats' => $attendanceStats,
            'exchangeRate' => $exchangeRate,
        ]);

        $fileName = "Daily_Report_{$date}.pdf";
        $filePath = "reports/{$fileName}";
        
        // Save to storage
        Storage::disk('public')->put($filePath, $pdf->output());
        $fullUrl = Storage::disk('public')->url($filePath);

        // Prepare Message
        $summary = "📊 *التقرير اليومي للمؤسسة - {$date}*\n\n";
        $summary .= "✅ عدد الأنشطة المنفذة: " . $activities->count() . "\n";
        $summary .= "💰 إجمالي التكلفة: " . number_format($activities->sum('cost'), 2) . " $\n";
        $summary .= "📝 عدد التقييمات: " . $evaluations->sum('students_count') . "\n";
        $summary .= "👥 إدخالات الحضور: " . $attendanceStats->sum('total_entries') . "\n\n";
        $summary .= "📎 التقرير التفصيلي (PDF) مرفق طيه.";

        $this->info("Sending to {$managerPhone}...");

        // Send Text Summary
        $whatsApp->sendMessage($managerPhone, $summary);

        // Send PDF Document
        // Note: Some APIs require a public URL, others require base64 or a path.
        // We'll pass the URL as a fallback.
        $sent = $whatsApp->sendDocument($managerPhone, $fullUrl, $fileName, "التقرير اليومي {$date}");

        if ($sent) {
            $this->info('Report sent successfully!');
        } else {
            $this->error('Failed to send report via WhatsApp.');
            Log::error('Daily Log Report failed to send via WhatsApp.');
        }

        return 0;
    }
}
