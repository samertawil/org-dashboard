<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivitySchedule extends Model
{
    use HasFactory;

    /**
     * اسم الجدول في قاعدة البيانات
     */
    protected $table = 'educational_activity_schedules';

    protected $fillable = [
        'activity_id',
        'group_id',
        'educational_activity_domain',
        'target_category',
        'activity_name',
        'activity_description',
        'period_start',
        'period_end',
        'educational_period_groups',
        'notes',
        'sort_order',
        'activation',
        'employee_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end'   => 'datetime',
        'activation'   => 'integer',
        'sort_order'   => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function ($schedule) {
            if (isset($schedule->activity_name) && !is_numeric($schedule->activity_name) && !empty($schedule->activity_name)) {
                $name = trim($schedule->activity_name);

                $status = \App\Models\Status::firstOrCreate([
                    'status_name' => $name,
                    'p_id_sub' => 197,
                ], [
                    'used_in_system_id' => 1,
                ]);

                $schedule->activity_name = $status->id;
            }
        });
    }





    /**
     * الفئات المستهدفة
     */
    public const TARGET_CATEGORIES = [
        'work_team' => 'فريق العمل',
        'children'  => 'الأطفال',
        'parents'  => 'أولياء الأمور',
    ];

    // ========================
    // العلاقات (Relations)
    // ========================

    /**
     * النشاط الرئيسي المرتبط
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }



    /**
     * تفاصيل التقرير للنشاط
     */
    public function activityDetail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EducationalActivityDetail::class, 'educational_activity_id');
    }

    /**
     * تفاصيل التقرير للنشاط (باستخدام العلاقة educationalActivity)
     */
    public function educationalActivity(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EducationalActivityDetail::class, 'educational_activity_id');
    }

    /**
     * المجموعة الدراسية المرتبطة
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(StudentGroup::class, 'group_id');
    }

    /**
     * مجال النشاط (من جدول الحالات)
     */
    public function activityDomain(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'educational_activity_domain');
    }

    /**
     * اسم النشاط من جدول الثوابت
     */
    public function activityNameStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'activity_name');
    }

    /**
     * مجموعات الفترة الزمنية (من جدول الحالات)
     */
    public function periodGroups(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'educational_period_groups');
    }

    /**
     * الموظف المسؤول
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * المستخدم المنشئ
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * المستخدم المحدِّث
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ========================
    // Scopes (نطاقات البحث)
    // ========================

    /**
     * تصفية النشاطات المنجزة (التي لها تفاصيل تقرير)
     */
    public function scopeCompleted($query)
    {
        return $query->whereHas('activityDetail');
    }

    /**
     * تصفية النشاطات غير المكتملة
     */
    public function scopePending($query)
    {
        return $query->whereDoesntHave('activityDetail');
    }

    /**
     * تصفية الأنشطة التي تحدث الآن
     */
    public function scopeHappenNow($query)
    {
        return $query->pending()
            ->where('period_start', '<=', now())
            ->where('period_end', '>=', now());
    }

    /**
     * تصفية النشاطات المتأخرة
     */
    public function scopeDelayed($query)
    {
        return $query->pending()->where('period_end', '<', now()->startOfDay());
    }

    /**
     * تصفية الأنشطة المطلوبة اليوم
     */
    public function scopeRequireToday($query)
    {
        return $query->pending()
            ->where('period_start', '>=', now()->startOfDay())
            ->where('period_end', '<=', now()->endOfDay())
            ->where(function ($q) {
                $q->where('period_start', '>', now())
                    ->orWhere('period_end', '<', now());
            });
    }

    /**
     * تصفية الأنشطة المخطط لها مستقبلاً
     */
    public function scopeUpcoming($query)
    {
        return $query->pending()
            ->where('period_start', '>', now()->endOfDay());
    }

    /**
     * تصفية حسب الفترة الزمنية
     */
    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('period_start', [$start, $end]);
    }

    /**
     * تصفية حسب مجال النشاط
     */
    public function scopeByDomain($query, int $domainId)
    {
        return $query->where('educational_activity_domain', $domainId);
    }

    /**
     * تصفية حسب الفئة المستهدفة
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('target_category', $category);
    }

    /**
     * تصفية حسب المجموعة
     */
    public function scopeByGroup($query, int $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * النشاطات المفعّلة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('activation', 1);
    }

    /**
     * ترتيب حسب وقت البداية والترتيب
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('period_start')->orderBy('sort_order');
    }

    /**
     * تحميل العلاقات اللازمة لعرض تفاصيل النشاط
     */
    public function scopeWithShowDetails($query)
    {
        return $query->with([
            'activity',
            'group',
            'activityDomain',
            'activityNameStatus',
            'periodGroups',
            'employee',
            'createdBy',
            'updatedBy',
            'educationalActivity.status',
        ]);
    }

    // ========================
    // Accessors (محولات القراءة)
    // ========================

    /**
     * تصنيف حالة المهمة ديناميكيًا
     */
    public function getTaskStatusAttribute(): string
    {
        $hasDetail = $this->relationLoaded('activityDetail')
            ? $this->activityDetail !== null
            : $this->activityDetail()->exists();

        if ($hasDetail) {
            return 'completed'; // منجزة
        }

        $now = now();

        if ($now->greaterThanOrEqualTo($this->period_start) && $now->lessThanOrEqualTo($this->period_end)) {
            return 'happen_now'; // يحدث الآن
        }

        if ($this->period_start->greaterThanOrEqualTo($now->startOfDay()) && $this->period_end->lessThanOrEqualTo($now->endOfDay())) {
            return 'require_today'; // مطلوبة اليوم
        }

        if ($now->greaterThan($this->period_end)) {
            return 'delayed'; // متأخرة
        }

        return 'upcoming'; // مخطط لها / قادمة
    }

    /**
     * لون الحالة
     */
    public function getTaskStatusColorAttribute(): string
    {
        return match ($this->task_status) {
            'completed' => 'green',
            'happen_now' => 'purple',
            'require_today' => 'amber',
            'delayed' => 'red',
            default => 'zinc',
        };
    }

    /**
     * الاسم المعرب للحالة
     */
    public function getTaskStatusLabelAttribute(): string
    {
        return match ($this->task_status) {
            'completed' => __('Completed'),
            'happen_now' => __('Happen Now'),
            'require_today' => __('Required Today'),
            'delayed' => __('Delayed'),
            default => __('Upcoming'),
        };
    }

    /**
     * اسم الفئة المستهدفة المعروض
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::TARGET_CATEGORIES[$this->target_category] ?? $this->target_category ?? '';
    }

    /**
     * وقت البداية بصيغة مقروءة
     */
    public function getPeriodStartFormattedAttribute(): string
    {
        return $this->period_start
            ? $this->period_start->format('h:i A')
            : '';
    }

    /**
     * وقت النهاية بصيغة مقروءة
     */
    public function getPeriodEndFormattedAttribute(): string
    {
        return $this->period_end
            ? $this->period_end->format('h:i A')
            : '';
    }

    /**
     * اليوم بالعربي من وقت البداية
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            'Sunday'    => 'الأحد',
            'Monday'    => 'الاثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
            'Saturday'  => 'السبت',
        ];

        return $this->period_start
            ? ($days[$this->period_start->format('l')] ?? $this->period_start->format('l'))
            : '';
    }
}
