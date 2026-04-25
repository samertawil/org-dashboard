<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public function getCachedCreatorAttribute()
    {
        return \Illuminate\Support\Facades\Cache::remember("user_basic_{$this->created_by}", 86400, function() {
            return $this->creator;
        });
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
        if (array_key_exists('feedbacks_avg_rating', $this->attributes)) {
            return $this->attributes['feedbacks_avg_rating'] ?? 0;
        }
        return $this->feedbacks()->avg('rating') ?? 0;
    }

    public function getRatingInfoAttribute()
    {
        $cacheKey = "activity_rating_info_{$this->id}_{$this->updated_at?->timestamp}";
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function() {
            $avg = $this->average_rating;
            
            if ($avg == 0) {
                return [
                    'rating' => 0,
                    'text' => __('No Feedback'),
                    'color' => 'text-zinc-300 dark:text-zinc-600',
                ];
            }

            $color = $avg < 2.5 ? 'text-zinc-400' : 'text-yellow-600';

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
        });
    }

    public function summary()
    {
        return $this->hasOne(ActivitySummary::class, 'activity_id');
    }

    /**
     * Get the virtual status information for display.
     */
    public function getStatusInfoAttribute(): array
    {
        $cacheKey = "activity_status_info_{$this->id}_{$this->updated_at?->timestamp}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function() {
            // If status is set in database, use it
            if ($this->status !== null) {
                $name = $this->activityStatus->status_name ?? '-';
                return [
                    'name' => __($name),
                    'raw_name' => $name,
                    'color' => match ((int) $this->status) {
                        27 => 'green',
                        26 => 'yellow',
                        25 => 'blue',
                        28 => 'red',
                        default => 'zinc',
                    },
                ];
            }

            // Virtual status logic when status is NULL
            $today = now()->toDateString();
            $startDate = $this->start_date;
            $endDate = $this->end_date;

            if ($this->attachments->isNotEmpty()) {
                return [
                    'name' => __('Completed'),
                    'raw_name' => 'Completed',
                    'color' => 'green',
                ];
            }

            if (($startDate > $today && $endDate > $today) && $this->attachments->isEmpty()) {
                return [
                    'name' => __('Planned'),
                    'raw_name' => 'Planned',
                    'color' => 'blue',
                ];
            }

            if (($startDate < $today && $endDate > $today) && $this->attachments->isEmpty()) {
                return [
                    'name' => __('In Progress'),
                    'raw_name' => 'In Progress',
                    'color' => 'yellow',
                ];
            }

            if ($startDate > $today  && $this->attachments->isEmpty()) {
                return [
                    'name' => __('Planned'),
                    'raw_name' => 'Planned',
                    'color' => 'blue',
                ];
            }

            if ($startDate === $today && $this->attachments->isEmpty()) {
                return [
                    'name' => __('In Progress'),
                    'raw_name' => 'In Progress',
                    'color' => 'yellow',
                ];
            }

            // For past dates
            if ($this->attachments->isEmpty()) {
                return [
                    'name' => __('On Hold'),
                    'raw_name' => 'On Hold',
                    'color' => 'red',
                ];
            }

            return [
                'name' => __('Undefined'),
                'raw_name' => 'Undefined',
                'color' => 'indigo',
            ];
        });
    }
    public function comments()
    {
        return $this->hasMany(ActivityComments::class, 'activity_id')->orderBy('created_at', 'desc');
    }

    public function beneficiaryNames()
    {
        return $this->hasMany(activityBeneficiaryName::class, 'activity_id');
    }
}
