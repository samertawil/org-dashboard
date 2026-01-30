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

    public function attachments()
    {
        return $this->hasMany(ActivityAttchment::class, 'activity_id');
    }
}
