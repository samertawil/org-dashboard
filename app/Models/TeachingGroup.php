<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingGroup extends Model
{
    protected $fillable = ['name', 'activity_id', 'Moderator', 'Moderator_phone', 'Moderator_email', 'status', 'activation', 'cost_usd', 'cost_nis', 'partner_id', 'notes', 'created_by', 'updated_by','student_groups_id'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
   
    public function status()
    {
        return $this->belongsTo(Status::class, 'status');
    }
    public function partner()
    {
        return $this->belongsTo(PartnerInstitution::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function studentGroup()
    {
        return $this->belongsTo(StudentGroup::class, 'student_groups_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(FeedBack::class, 'teaching_groups_id');
    }

    public function attachments()
    {
        return $this->hasMany(ActivityAttchment::class, 'teaching_groups_id');
    }

    public function getAverageRatingAttribute()
    {
        return $this->feedbacks()->avg('rating') ?? 0;
    }

    public function getRatingInfoAttribute()
    {
        $avg = $this->average_rating;
        
        if ($avg == 0) {
            return [
                'rating' => 0,
                'text' => __('No Feedback'),
                'color' => 'text-zinc-300 dark:text-zinc-600', // White/Grey look
            ];
        }
 
       
          $color = $avg  < 2.5 ? 'text-yellow-400' : 'text-yellow-600'; // Gold vs Yellow (adjust classes as needed)

        $text = match (true) {
            $avg >= 4.5 => __('Excellent'),
            $avg >= 3.5 => __('Very Good'),
            $avg >= 2.5 => __('Good'),
            $avg >= 1.5 => __('Fair'),
            default => __('Poor'),
        };

        return [
            'rating' => number_format($avg, 1),
            'text' => $text,
            'color' => $color,
        ];
    }
}
