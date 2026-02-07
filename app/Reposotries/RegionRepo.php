<?php

namespace App\Reposotries;

use App\Models\Region;
use Illuminate\Support\Facades\Cache;

class RegionRepo
{
    public static function regions()
    {
        return Cache::rememberForever('Region-all', function () {
            return Region::select('id', 'region_name')->get();
        });
    }
}
