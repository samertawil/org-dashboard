<?php

namespace App\Models;

use App\Enums\GlobalSystemConstant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StudentGroup extends Model
{
    protected $fillable = [
        'name',
        'max_students',
        'min_students',
        'current_student_count',
        'region_id',
        'city_id',
        'Moderator',
        'Moderator_phone',
        'Moderator_email',
        'description',
        'activation',
        'status_id',
        'subject_to_learn_id',
        'neighbourhood_id',
        'location_id',
        'address_details',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'batch_no',
    ];

    public function scopeActiveToday($query)
    {
        return $query->with(['region','city','students'])->where('activation', GlobalSystemConstant::ACTIVE)->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today());
    }


    protected $casts = [
        'subject_to_learn_id' => 'array',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function neighbourhood()
    {
        return $this->belongsTo(Neighbourhood::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }



    public function studentGroupSchedules()
    {
        return $this->hasMany(StudentGroupSchedule::class, 'student_group_id');
    }

    /**
     * Get the subjects tailored for this group from the JSON column.
     * Note: This is not a standard relationship and cannot be eager loaded with with().
     */
    public function getSubjectsAttribute()
    {
        $ids = $this->subject_to_learn_id ?? [];
        if (empty($ids)) {
            return collect();
        }
        return StudentSubjectForLearn::whereIn('id', $ids)->get();
    }

    public function dailyAttendances()
    {
        return $this->hasMany(StudentDailyAttendance::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Employee::class, 'teacher_student_group', 'student_group_id', 'teacher_id', 'id', 'user_id');
    }

    //  that means  
    //    student_groups
    // JOIN teacher_student_group
    // ON student_groups.id = teacher_student_group.student_group_id

    // JOIN employees
    // ON employees.id = teacher_student_group.teacher_id

    public function students()
    {
        return $this->hasMany(Student::class, 'student_groups_id');
    }
}
