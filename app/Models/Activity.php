<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];
    

    public function regions()
    {
        return $this->belongsTo(Region::class,  'region');
    }

    public function cities()
    {
        return $this->belongsTo(City::class, 'city');
    }

    public function activityNeighbourhood()
    {
        return $this->belongsTo(Neighbourhood::class, 'neighbourhood');
    }

    public function activityLocation()
    {
        return $this->belongsTo(Location::class, 'location');
    }

    public function activityStatus()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function statusSpecificSector()
    {
        return $this->belongsTo(Status::class, 'sector_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parcels()
    {
        return $this->hasMany(ActivityParcel::class, 'activity_id');
    }

    public function beneficiaries()
    {
        return $this->hasMany(ActivityBeneficiary::class, 'activity_id');
    }

    public function workTeams()
    {
        return $this->hasMany(ActivityWorkTeam::class, 'activity_id');
    }

    public function activityPartners()
    {
        return $this->hasMany(ActivityPartner::class, 'activity_id');
    }

    public function attachments()
    {
        return $this->hasMany(ActivityAttchment::class, 'activity_id');
    }

    public function teachingGroups()
    {
        return $this->hasMany(TeachingGroup::class, 'activity_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(FeedBack::class, 'activity_id');
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
 
       
          $color = $avg  < 2.5 ? 'text-zinc-400' : 'text-yellow-600'; // Gold vs Yellow (adjust classes as needed)

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

    /**
     * Get the virtual status information for display.
     */
    public function getStatusInfoAttribute(): array
    {
        // If status is set in database, use it
        if ($this->status !== null) {
            return [
                'name' => $this->activityStatus->status_name ?? '-',
                'color' => match ((int) $this->status) {
                    27 => 'green',
                    26 => 'yellow',
                    25 => 'indigo',
                    28 => 'red',
                    default => 'zinc',
                },
            ];
        }

        // Virtual status logic when status is NULL
        $today = now()->toDateString();
        $startDate = $this->start_date;

        if ($this->attachments->isNotEmpty()) {
            return [
                'name' => __('Completed'),
            'color' => 'green',
            ];
        }

        if ($startDate > $today && $this->attachments->isEmpty()) {
            return [
                'name' => __('Planned'),
                'color' => 'blue',
            ];
        }

        if ($startDate === $today && $this->attachments->isEmpty()) {
            return [
                'name' => __('In Progress'),
                'color' => 'yellow',
            ];
        }

        // For past dates
        if ($this->attachments->isEmpty()) {
            return [
                'name' => __('Need Procedure / On Hold'),
                'color' => 'orange',
            ];
        }

        return [
            'name' => __('Undefined'),
            'color' => 'indigo',
        ];
    }
}
