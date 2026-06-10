<?php

namespace App\Concerns\EducationalActivitySchedule;

use App\Models\Employee;
use App\Models\TeacherStudentGroup;
use App\Reposotries\StatusRepo;
use App\Reposotries\StudentGroupRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

trait FormTrait
{
    // =====================================================
    // الحقول المشتركة بين Create و Edit
    // =====================================================


    #[Validate('required|exists:student_groups,id')]
    public $group_id = '';

    // مجال النشاط (تدريب / استقبال الأطفال / تهيئة المكان / التعليم / الدعم النفسي)
    #[Validate('required|exists:statuses,id')]
    public $educational_activity_domain = '';

    // الفئة المستهدفة (فريق العمل / الأطفال)
    #[Validate('required|string|max:100')]
    public $target_category = '';

    #[Validate('required|exists:educational_activity_names,id')]
    public $activity_name = '';

    #[Validate('nullable|string')]
    public $activity_description = '';

    #[Validate([
        'required',
        'date_format:Y-m-d\TH:i',
        new \App\Rules\TimeBetween('08:00', '16:00')
    ])]
    public $period_start = '';

    #[Validate([
        'required',
        'date_format:Y-m-d\TH:i',
        'after_or_equal:period_start',
        new \App\Rules\TimeBetween('08:00', '16:00')
    ])]
    public $period_end = '';

    // المجموعات الزمنية (A, B, C...)
    #[Validate('nullable|exists:statuses,id')]
    public $educational_period_groups = '';

    #[Validate('nullable|string')]
    public $notes = '';

    #[Validate('nullable|integer|min:0')]
    public $sort_order = 0;

    #[Validate('required|integer')]
    public $activation = 1;

    #[Validate('nullable|exists:employees,id')]
    public $employee_id = '';

    // =====================================================
    // بيانات مساعدة للـ dropdowns
    // =====================================================
    public $activities    = [];
    public $studentGroups = [];
    // employees is a #[Computed] that reacts to group_id — see below

    // =====================================================
    // الفئات المستهدفة
    // =====================================================
    public const TARGET_CATEGORIES = [
        'work_team' => 'فريق العمل',
        'children'  => 'الأطفال',
        'parents'  => 'أولياء الأمور',
    ];

    // =====================================================
    // Boot: تحميل البيانات عند بدء الـ Component
    // =====================================================
    public function bootFormTrait(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get all active student groups today
        $allActiveGroups = StudentGroupRepo::activateEducationPointsWithEmployee();


        $this->studentGroups = $allActiveGroups;
    } // end bootFormTrait

    // =====================================================
    // Computed: قوائم الـ Statuses المصفّاة
    // =====================================================

    #[Computed()]
    public function allStatuses()
    {
        return StatusRepo::statuses();
    }

    #[Computed()]
    public function assignedGroups()
    {
        return StatusRepo::statuses();
    }


    /**
     * Dynamically returns teachers assigned to the selected student group.
     * Recomputes whenever $this->group_id changes.
     */
    #[Computed()]
    public function employees()
    {
        if (!$this->group_id) {
            return collect();
        }

        // Get teacher user_ids for the selected group
        $teacherUserIds = TeacherStudentGroup::where('student_group_id', $this->group_id)
            ->pluck('teacher_id')
            ->unique();

        // Return Employee models for those user_ids
        return Employee::whereIn('user_id', $teacherUserIds)
            ->where('activation', 1)
            ->get(['id', 'full_name', 'user_id']);
    }



    /**
     * مجالات النشاط التعليمي من جدول الحالات
     * p_id_sub = appConstant.educational_activity_domains
     */
    #[Computed()]
    public function activityDomains()
    {
        return $this->allStatuses()
            ->where('p_id_sub', config('appConstant.educational_activity_domains'));
    }

    /**
     * أسماء الأنشطة التعليمية من جدول الحالات
     * p_id_sub = appConstant.educational_activity_names
     */
    #[Computed()]
    public function activityNames()
    {
        return \App\Models\EducationalActivityName::where('activation', 1)
            ->orderBy('activity_name')
            ->get();
    }

    /**
     * المجموعات الزمنية (A, B, C...) من جدول الحالات
     * p_id_sub = appConstant.educational_period_groups
     */
    #[Computed()]
    public function periodGroups()
    {

        return $this->allStatuses()
            ->where('p_id_sub', config('appConstant.educational_period_groups'));
    }

    public function saveStateToSession($periodStart = null): void
    {
        $groupId = $this->group_id;
        $group = \App\Models\StudentGroup::find($groupId);
        if ($group) {
            session([
                'eas_filterBatch' => $group->batch_no,
                'eas_filterGroup' => $group->id,
            ]);
        }

        $dateStr = $periodStart ?: $this->period_start;
        if ($dateStr) {
            try {
                $periodStartCarbon = \Carbon\Carbon::parse($dateStr);
                session([
                    'eas_last_group_id' => $groupId,
                    'eas_last_month'    => $periodStartCarbon->format('Y-m'),
                    'eas_last_date'     => $periodStartCarbon->format('Y-m-d'),
                ]);
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }
        session(['eas_is_returning' => true]);
    }

    public function backToEducationalActivitySchedules($periodStart = null)
    {
        $this->saveStateToSession($periodStart);

        return $this->redirect(route('educational-activity-schedules.index'), navigate: true);
    }

    public function checkAttendanceSchedule(): bool // فحص اذا كان يوجد حضور وغياب لليوم المراد عمل جدوله له
    {
        if ($this->target_category != 'children') {
            return true;
        }
        $scheduleDate = \Carbon\Carbon::parse($this->period_start)->toDateString();
        $hasGroupSchedule = \App\Models\StudentGroupSchedule::where('student_group_id', $this->group_id)
            ->whereDate('schedule_date', $scheduleDate)
            ->exists();

        if (!$hasGroupSchedule) {
            $this->addError('period_start', __('Cannot add an educational schedule without a student attendance schedule for this day. لا يمكن اضافة هذا البيانات بسبب عدم وجود جدولة حضور وغياب بنفس التاريخ'));
            return false;
        }

        return true;
    }

    public function checkDuplicateSchedule(): bool // فحص إذا كان الجدول موجود مسبقاً لنفس المجموعة والوقت
    {
        $query =  \App\Models\ActivitySchedule::where('group_id', $this->group_id)
            ->where('period_start', $this->period_start)
            ->where('educational_period_groups', $this->educational_period_groups);

        // إذا كان المتغير schedule موجوداً وهو مسجل في قاعدة البيانات (في حالة التعديل Edit)
        if (property_exists($this, 'schedule') && isset($this->schedule) && $this->schedule->exists) {
            $query->where('id', '!=', $this->schedule->id);
        }

        $exists = $query->exists();

        if ($exists) {
            $this->addError('group_id', __('This schedule already exists for the selected group, period, and time.'));
            return false;
        }

        return true;
    }
}
