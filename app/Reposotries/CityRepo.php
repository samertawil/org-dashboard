<?php

namespace App\Reposotries;

use App\Models\City;
use Illuminate\Support\Facades\Cache;

class CityRepo
{
    public static function cities()
    {
        return Cache::rememberForever('city-all', function () {
            return City::select('id', 'city_name', 'region_id')->get();
        });
    }
}
