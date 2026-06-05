<?php

namespace App\Concerns\EducationalActivityDetail;

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

trait FormTrait
{
    public $educational_activity_id = '';

    public $consistent = '';

    public $what_learned = '';

    public $teacher_report_detail = '';

    public $status_id = '';

    public $replaced_activity = '';

    public $replaced_reason = '';

    public $existingAttachments = [];

    #[On('attachments-updated')]
    public function updateAttachments($attachments)
    {
        $this->existingAttachments = $attachments;
    }

    public function bootFormTrait() {}

    public function rules()
    {
        $maxConsistent = $this->presentStudentsCount;

        $consistentRule = 'required|integer|min:1';
        if ($maxConsistent !== null) {
            $consistentRule .= '|max:' . $maxConsistent;
        }

        return [
            'educational_activity_id' => 'required|exists:educational_activity_schedules,id',
            'consistent'              => $consistentRule,
            'what_learned'            => 'required|string',
            'teacher_report_detail'   => 'required|string',
            'status_id'               => 'required|exists:statuses,id',
            'replaced_activity'       => $this->status_id != 193 ? 'required|string' : 'nullable|string',
            'replaced_reason'         => $this->status_id != 193 ? 'required|string' : 'nullable|string',
            'existingAttachments'     => 'required|array|size:2',
        ];
    }

    public function messages(): array
    {
        return [
            'consistent.required'         => 'مطلوب ادخال عدد المنسجمين',
            'what_learned.required'         => 'مطلوب ادخال حقل الاستفادة ',
            'teacher_report_detail.required'    => 'مطلوب ادخال حقل التقرير ',
            'status_id.required'            => 'مطلوب ادخال حقل الحالة ',
            'consistent.integer'            => 'مطلوب ادخال عدد صحيح',
            'consistent.max'                => __('لا يمكن أن تتجاوز قيمة المنسجمين بالنشاط عدد الطلاب الحاضرين للنشاط وهو :max طالب.'),
            'consistent.min'                => __('لا يمكن أن تكون قيمة المنسجمين بالنشاط أقل من صفر.'),
            'existingAttachments.required'  => __('يجب إرفاق مرفقين اثنين بالتقرير.'),
            'existingAttachments.array'    => __('صيغة المرفقات غير صحيحة.'),
            'existingAttachments.size'     => __('يجب أن يكون عدد المرفقات 2 بالضبط، لا أقل ولا أكثر.'),
        ];
    }

    #[Computed()]
    public function activitySchedules()
    {
        return \App\Reposotries\EducationalActivityDetailRepo::getTeacherSchedules();
    }

    #[Computed()]
    public function activityReportStatuses()
    {
        return \App\Reposotries\StatusRepo::statuses()->where('p_id_sub', config('appConstant.activity_report_statuses'));
    }

    #[Computed()]
    public function selectedActivitySchedule()
    {
        if (empty($this->educational_activity_id)) {
            return null;
        }
        return \App\Models\ActivitySchedule::find($this->educational_activity_id);
    }

    /**
     * عدد الطلاب الحاضرين في المجموعة المرتبطة بالنشاط، في يوم النشاط،
     * ممن يحملون نفس status_id المطابق لـ educational_period_groups.
     * يُطبَّق فقط عندما تكون الفئة المستهدفة = 'children'.
     * يُستخدم كحد أقصى لحقل consistent عند التحقق من الصحة.
     */
    #[Computed()]
    public function presentStudentsCount(): ?int
    {
        $schedule = $this->selectedActivitySchedule;

        // تطبيق القيد فقط عندما الفئة المستهدفة هي "الأطفال"
        if (!$schedule || $schedule->target_category !== 'children') {
            return null;
        }

        if (!$schedule->group_id || !$schedule->period_start || !$schedule->educational_period_groups) {
            return null;
        }

        $activityDate = \Carbon\Carbon::parse($schedule->period_start)->format('Y-m-d');
        $periodGroupStatusId = $schedule->educational_period_groups;

        // عد الطلاب الحاضرين الذين يحملون نفس status_id المطابق لـ educational_period_groups
        $count = \App\Models\StudentDailyAttendance::where('student_group_id', $schedule->group_id)
            ->whereDate('attendance_date', $activityDate)
            ->where('status', 'present')
            ->whereHas('student', function ($q) use ($periodGroupStatusId) {
                $q->where('status_id', $periodGroupStatusId);
            })
            ->count();

        return $count;
    }
}
