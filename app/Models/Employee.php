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
}
