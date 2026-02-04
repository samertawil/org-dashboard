<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubjectForLearn extends Model
{
    protected $fillable = ['name', 'type_id', 'description', 'activation', 'from_age', 'to_age'];

    public function type()
    {
        return $this->belongsTo(Status::class, 'type_id');
    }
    
    public function subjectsAttchments()
    {
        return $this->hasMany(ActivityAttchment::class, 'subject_learning_id');
    }
}
