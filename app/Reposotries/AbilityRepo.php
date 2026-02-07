<?php

namespace App\Repositories;

use App\Models\Ability;
use Illuminate\Support\Facades\Cache;

class AbilityRepo
{
    public static function Abilities()
    {
        return Cache::rememberForever('Abilities-all', function () {
            return Ability::select('id', 'name', 'title')->get();
        });
    }
}
