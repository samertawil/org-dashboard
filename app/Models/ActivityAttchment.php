<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityAttchment extends Model
{
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Activity::class);
    }

    public function attachmentType()
    {
        return $this->belongsTo(Status::class, 'attchment_type');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function teachingGroup()
    {
        return $this->belongsTo(TeachingGroup::class, 'teaching_groups_id');
    }

    public function subjectLearning()
    {
        return $this->belongsTo(StudentSubjectForLearn::class, 'subject_learning_id');
    }
}
