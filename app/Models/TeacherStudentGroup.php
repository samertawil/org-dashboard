<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherStudentGroup extends Model
{
    protected $table = 'teacher_student_group';

    protected $fillable = [
        'teacher_id',
        'student_group_id',
        'job_title',
    ];

    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'teacher_id', 'user_id');
    }

    public function studentGroup()
    {
        return $this->belongsTo(StudentGroup::class);
    }

    public function jobTitle()
    {
        return $this->belongsTo(Status::class, 'job_title');
    }

    public static function isGroupSupervisor($user, $group)
    {
        return self::where('teacher_id', $user->id)
            ->where('student_group_id', $group->id)
            ->where('job_title', 167)
            ->exists();
    }

    public static function supervisorGroupIds($user)
    {
        return self::where('teacher_id', $user->id)
            ->where('job_title', 167)
            ->pluck('student_group_id')
            ->toArray();
    }

    public static function employeesForEducationalTasks()
    {
        return \Illuminate\Support\Facades\Cache::remember('employees-for-educational-tasks', 3600, function () {
            return Employee::whereIn('user_id', function ($query) {
                $query->select('teacher_id')
                    ->from('teacher_student_group')
                    ->whereNotNull('teacher_id');
            })
            ->orderBy('full_name')
            ->get();
        });
    }
}
