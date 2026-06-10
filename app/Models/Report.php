<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'report_name',
        'report_period_type',
        'report_main_type',
        'report_date',
        'date_from',
        'date_to',
        'batch_no',
        'student_group_ids',
        'employee_id',
        'required_from',
        'addressed_to_dept_types',
        'addressed_to_employees',
        'follow_up_by',
        'covered_educational_activities_ids',
        'covered_educational_activity_schedules_ids',
        'covered_educational_activity_details_ids',
        'note',
        'is_read',
    ];

    protected $casts = [
        'student_group_ids' => 'array',
        'addressed_to_dept_types' => 'array',
        'follow_up_by' => 'array',
        'covered_educational_activities_ids' => 'array',
        'covered_educational_activity_schedules_ids' => 'array',
        'covered_educational_activity_details_ids' => 'array',
        'report_date' => 'date',
        'date_from' => 'date',
        'date_to' => 'date',
        'is_read' => 'boolean',
    ];

    public function bodies()
    {
        return $this->hasMany(ReportBody::class, 'report_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function addressedToEmployee()
    {
        return $this->belongsTo(Employee::class, 'addressed_to_employees');
    }

    public function periodType()
    {
        return $this->belongsTo(Status::class, 'report_period_type');
    }

    public function mainType()
    {
        return $this->belongsTo(Status::class, 'report_main_type');
    }

    public function requiredFrom()
    {
        return $this->belongsTo(Status::class, 'required_from');
    }
}
