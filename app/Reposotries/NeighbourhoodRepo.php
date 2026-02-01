<?php

namespace App\Reposotries;


use App\Models\Neighbourhood;
use Illuminate\Support\Facades\Cache;

class NeighbourhoodRepo
{
    public static function neighbourhoods()
    {
       return Cache::rememberForever('Neighbourhood-all', function () {
            return Neighbourhood::get();
        });
    }
}
