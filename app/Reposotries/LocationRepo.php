<?php

namespace App\Reposotries;


use App\Models\Location;
use Illuminate\Support\Facades\Cache;

class LocationRepo
{
    public static function locations()
    {
       return Cache::rememberForever('Location-all', function () {
            return Location::select('id', 'location_name', 'neighbourhood_id')->get();
        });
    }
}
