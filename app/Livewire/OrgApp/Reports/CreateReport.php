<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Employee;
use App\Models\Report;
use App\Models\ReportBody;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class CreateReport extends Component
{
    // Source context (e.g. 'supervisor_activities' | '')
    public string $source = '';

    // Covered IDs populated when source is 'supervisor_activities'
    public array $coveredScheduleIds  = [];
    public array $coveredDetailIds    = [];
    public array $coveredActivityIds  = [];
    public array $coveredGroupIds     = [];
    public string $batchNo            = '';

    // Report metadata
    public string $report_name              = '';
    public        $report_period_type       = '';
    public        $report_main_type         = '';
    public string $report_date              = '';
    public string $date_from               = '';
    public string $date_to                 = '';
    public array  $addressed_to_dept_types = [];
    public        $addressed_to_employees  = '';
    public array  $follow_up_by            = [];
    public string $ccSearch                = '';
    public string $note                    = '';

    /**
     * Report body items.
     * Each item is an associative array:
     * [
     *   'title'                => string,
     *   'content'              => string,   (required)
     *   'observation'          => string,
     *   'attachments_pool'     => array,    (selectable attachments from source)
     *   'selected_attachments' => array,
     * ]
     */
    public array $reportItems = [];

    public function mount(): void
    {
        // Read draft from session (set by SupervisorActivitiesReport::openCreateReport())
        $draft = session()->pull('report_draft');

        if ($draft) {
            $this->source             = $draft['source'] ?? '';
            $this->date_from          = $draft['date_from'] ?? '';
            $this->date_to            = $draft['date_to'] ?? '';
            $this->batchNo            = $draft['batch_no'] ?? '';
            $this->coveredGroupIds    = $draft['student_group_ids'] ?? [];
            $this->coveredActivityIds = $draft['covered_educational_activities_ids'] ?? [];
            $this->coveredScheduleIds = $draft['covered_educational_activity_schedules_ids'] ?? [];
            $this->coveredDetailIds   = $draft['covered_educational_activity_details_ids'] ?? [];

            foreach ($draft['items'] ?? [] as $item) {
                $this->reportItems[] = [
                    'title'                => $item['title'] ?? '',
                    'content'              => $item['content'] ?? '',
                    'observation'          => $item['observation'] ?? '',
                    'attachments_pool'     => $item['attachments_pool'] ?? [],
                    'selected_attachments' => $item['selected_attachments'] ?? [],
                ];
            }
        }

        // Ensure at least one empty item when opened without a source
        if (empty($this->reportItems)) {
            $this->reportItems[] = [
                'title'                => '',
                'content'              => '',
                'observation'          => '',
                'attachments_pool'     => [],
                'selected_attachments' => [],
            ];
        }

        // Default date values
        $this->report_date = Carbon::now()->format('Y-m-d');
        if (empty($this->date_from)) {
            $this->date_from = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (empty($this->date_to)) {
            $this->date_to = Carbon::now()->format('Y-m-d');
        }

        // Default report name
        if (empty($this->report_name)) {
            $this->report_name = __('Report') . ' — ' . Carbon::now()->format('Y-m-d');
        }

        // Default period type status
        $periodTypeRoot = DB::table('statuses')->where('route_system_name', 'report_period_type')->first();
        if ($periodTypeRoot) {
            $firstPeriod = DB::table('statuses')->where('p_id_sub', $periodTypeRoot->id)->first();
            $this->report_period_type = $firstPeriod ? $firstPeriod->id : '';
        }

        // Default main type status
        $mainTypeRoot = DB::table('statuses')->where('route_system_name', 'report_main_type')->first();
        if ($mainTypeRoot) {
            $firstMain = DB::table('statuses')->where('p_id_sub', $mainTypeRoot->id)->first();
            $this->report_main_type = $firstMain ? $firstMain->id : '';
        }
    }

    // ──────────────────────────────────────────
    // Report Item Management
    // ──────────────────────────────────────────

    public function addItem(): void
    {
        $this->reportItems[] = [
            'title'                => '',
            'content'              => '',
            'observation'          => '',
            'attachments_pool'     => [],
            'selected_attachments' => [],
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->reportItems) > 1) {
            array_splice($this->reportItems, $index, 1);
            $this->reportItems = array_values($this->reportItems);
        }
    }

    // ──────────────────────────────────────────
    // CC / Follow-up Management
    // ──────────────────────────────────────────

    public function addCcEmployee($id): void
    {
        $idStr = (string) $id;
        if (!in_array($idStr, $this->follow_up_by)) {
            $this->follow_up_by[] = $idStr;
        }
        $this->ccSearch = '';
    }

    public function removeCcEmployee($id): void
    {
        $idStr          = (string) $id;
        $this->follow_up_by = array_values(array_diff($this->follow_up_by, [$idStr]));
    }

    // ──────────────────────────────────────────
    // Save Report
    // ──────────────────────────────────────────

    public function saveReport(): void
    {
        $this->validate([
            'report_name'              => 'required|string|max:255',
            'report_period_type'       => 'required|exists:statuses,id',
            'report_main_type'         => 'required|exists:statuses,id',
            'report_date'              => 'required|date',
            'date_from'                => 'required|date',
            'date_to'                  => 'required|date|after_or_equal:date_from',
            'addressed_to_employees'   => 'required|exists:employees,id',
            'addressed_to_dept_types'  => 'required|array|min:1',
            'follow_up_by'             => 'nullable|array',
            'follow_up_by.*'           => 'exists:employees,id',
            'reportItems'              => 'required|array|min:1',
            'reportItems.*.content'    => 'required|string|min:1',
            'reportItems.*.title'      => 'nullable|string|max:500',
            'reportItems.*.observation' => 'nullable|string',
        ]);

        $user     = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            session()->flash('message', __('Only employees can create reports.'));
            session()->flash('type', 'error');
            return;
        }

        DB::beginTransaction();
        try {
            $report = Report::create([
                'report_name'                                => $this->report_name,
                'report_period_type'                         => $this->report_period_type,
                'report_main_type'                           => $this->report_main_type,
                'report_date'                                => $this->report_date,
                'date_from'                                  => $this->date_from,
                'date_to'                                    => $this->date_to,
                'batch_no'                                   => $this->batchNo ?: null,
                'student_group_ids'                          => array_values(array_unique($this->coveredGroupIds)),
                'employee_id'                                => $employee->id,
                'required_from'                              => null,
                'addressed_to_dept_types'                    => $this->addressed_to_dept_types,
                'addressed_to_employees'                     => $this->addressed_to_employees,
                'follow_up_by'                               => $this->follow_up_by ?: [],
                'covered_educational_activities_ids'         => array_values(array_unique($this->coveredActivityIds)),
                'covered_educational_activity_schedules_ids' => array_values(array_unique($this->coveredScheduleIds)),
                'covered_educational_activity_details_ids'   => array_values(array_unique($this->coveredDetailIds)),
                'note'                                       => $this->note,
            ]);

            foreach ($this->reportItems as $index => $item) {
                ReportBody::create([
                    'report_id'               => $report->id,
                    'item_order'              => $index + 1,
                    'content'                 => $item['content'],
                    'observation'             => $item['observation'] ?: null,
                    'status_id'               => null,
                    'attachments'             => $item['selected_attachments'] ?: [],
                    'report_body_attachments' => null,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('message', __('Error creating report: ') . $e->getMessage());
            session()->flash('type', 'error');
            return;
        }

        // ── Email Notification ──────────────────────────────
        try {
            $addressedEmployee = Employee::find($this->addressed_to_employees);
            $ccEmails          = [];

            if (!empty($this->follow_up_by)) {
                $ccEmails = Employee::whereIn('id', $this->follow_up_by)
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->toArray();
            }

            if ($addressedEmployee && $addressedEmployee->email) {
                $mail = Mail::to($addressedEmployee->email);
                if (!empty($ccEmails)) {
                    $mail->cc($ccEmails);
                }
                $mail->send(new \App\Mail\SendReportMail($report));
            }
        } catch (\Exception $e) {
            logger()->error('Failed to send report email: ' . $e->getMessage());
        }

        session()->flash('message', __('Report successfully generated and emailed.'));
        session()->flash('type', 'success');

        // Return to the appropriate page after saving
        $redirectRoute = route('reports.saved-reports');

        $this->redirect($redirectRoute, navigate: true);
    }

    public function summarizeItemWithAI(int $index): void
    {
        $content = $this->reportItems[$index]['content'] ?? '';
        if (trim($content) === '') {
            return;
        }

        try {
            $aiService = app(\App\Services\AIService::class);
            $prompt = "قم بتلخيص محتوى النقاط التالية المتعلقة بـ (ما تعلمه الطلاب وملاحظات المعلمين) في تقرير صفي إلى فقرة واحدة متماسكة، رسمية، واحترافية باللغة العربية موجهة للإدارة:\n\n" . $content;

            $summary = $aiService->generateContent($prompt);

            if ($summary) {
                $this->reportItems[$index]['content'] = trim($summary);
                session()->flash('message', __('Content summarized successfully using AI.'));
                session()->flash('type', 'success');
            } else {
                session()->flash('message', __('AI failed to generate a summary.'));
                session()->flash('type', 'error');
            }
        } catch (\Exception $e) {
            session()->flash('message', __('AI Error: ') . $e->getMessage());
            session()->flash('type', 'error');
        }
    }

    // ──────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────

    public function render()
    {
        $allEmployees = Employee::where('activation', 1)->orderBy('full_name')->get();

        $selectedCcEmployees = [];
        if (!empty($this->follow_up_by)) {
            $selectedCcEmployees = Employee::whereIn('id', $this->follow_up_by)->get();
        }

        $filteredCcEmployees = [];
        if (trim($this->ccSearch) !== '') {
            $filteredCcEmployees = Employee::where('activation', 1)
                ->where('full_name', 'like', '%' . trim($this->ccSearch) . '%')
                ->whereNotIn('id', $this->follow_up_by)
                ->limit(8)
                ->get();
        }

        $periodTypeRoot = DB::table('statuses')->where('route_system_name', 'report_period_type')->first();
        $periodTypes    = $periodTypeRoot
            ? DB::table('statuses')->where('p_id_sub', $periodTypeRoot->id)->get()
            : collect();

        $mainTypeRoot = DB::table('statuses')->where('route_system_name', 'report_main_type')->first();
        $mainTypes    = $mainTypeRoot
            ? DB::table('statuses')->where('p_id_sub', $mainTypeRoot->id)->get()
            : collect();

        return view('livewire.org-app.reports.create-report', [
            'allEmployees'        => $allEmployees,
            'selectedCcEmployees' => $selectedCcEmployees,
            'filteredCcEmployees' => $filteredCcEmployees,
            'periodTypes'         => $periodTypes,
            'mainTypes'           => $mainTypes,
        ]);
    }
}
