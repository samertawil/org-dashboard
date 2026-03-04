<?php

namespace App\Reposotries;

use App\Models\Activity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ActivityRepo
{
    public static function activites()
    {
        return Cache::rememberForever('activites-all', function () {
            return Activity::with(['activityStatus', 'attachments'])->latest()->get();
        });
    }

    
}
