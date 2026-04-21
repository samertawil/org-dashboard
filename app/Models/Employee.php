<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function maritalStatus()
    {
        return $this->belongsTo(Status::class, 'marital_status');
    }

    public function region()
    {
        return $this->belongsTo(Status::class, 'regions');
    }

    public function hiringType()
    {
        return $this->belongsTo(Status::class, 'type_of_employee_hire');
    }

    public function positionStatus()
    {
        return $this->belongsTo(Status::class, 'position');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentGroups()
    {
        return $this->belongsToMany(StudentGroup::class, 'teacher_student_group', 'teacher_id', 'student_group_id', 'user_id', 'id');
    }

    public function jobTitle()
    {
        return $this->belongsTo(Status::class, 'job_title');
    }

    public function partner()
    {
        return $this->belongsTo(PartnerInstitution::class, 'employee_in_partner_id');
    }
}
