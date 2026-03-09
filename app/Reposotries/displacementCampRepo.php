<?php

namespace App\Reposotries;


use App\Models\DisplacementCamp;
use Illuminate\Support\Facades\Cache;

class displacementCampRepo
{
    public static function camps() {

        return  Cache::rememberForever('DisplacementCamp-all', function () {
              return DisplacementCamp::get(); 
          });
        }
}
