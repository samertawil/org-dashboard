<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedBack extends Model
{
    protected $fillable = ['activity_id', 'rating', 'comment', 'client_name','teaching_groups_id','feed_back_type',
    'student_id', 'student_feed_back_time'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function teachingGroup()
    {
        return $this->belongsTo(TeachingGroup::class, 'teaching_groups_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feedbackTypeStatus()
    {
        return $this->belongsTo(Status::class, 'feed_back_type');
    }
    
    public function feedbackTimeStatus()
    {
        return $this->belongsTo(Status::class, 'student_feed_back_time');
    }
}
