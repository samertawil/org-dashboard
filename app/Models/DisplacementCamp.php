<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisplacementCamp extends Model
{
    protected $fillable = ['name', 'region_id', 'city_id', 'neighbourhood_id', 'location_id', 'address_details', 'longitudes', 'latitude', 'number_of_families', 'number_of_individuals', 'Moderator', 'Moderator_phone', 'camp_main_needs', 'attchments', 'notes'];

    protected $casts = [
        'camp_main_needs' => 'array',
        'attchments' => 'array',
    ];

     public function region()
    {
        return $this->belongsTo(Region::class);
    }

     public function city()
    {
        return $this->belongsTo(City::class);
    }

     public function neighbourhood()
    {
        return $this->belongsTo(Neighbourhood::class);
    }

     public function location()
    {
        return $this->belongsTo(Location::class);
    }

    
}
