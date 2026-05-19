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

    // ========================
    // Accessors (محولات القراءة)
    // ========================

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
