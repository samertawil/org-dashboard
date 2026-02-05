<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDailyAttendance extends Model
{
    protected $fillable = [
        'student_id',
        'student_group_id',
        'attendance_date',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentGroup()
    {
        return $this->belongsTo(StudentGroup::class);
    }
}
