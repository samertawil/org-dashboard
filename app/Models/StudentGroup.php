<?php

namespace App\Models;

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
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
