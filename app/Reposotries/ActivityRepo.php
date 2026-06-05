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

    public static function activitiesBySector()
    {
        return Cache::rememberForever('activites-by-sector', function () {
            return Activity::select('sector_id', DB::raw('count(*) as total'))
                ->with('statusSpecificSector')
                ->groupBy('sector_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => $item->statusSpecificSector->status_name ?? 'Unknown',
                        'value' => $item->total,
                    ];
                });
        });
    }

    public static function lastEducationalActivity()
    {
        return Cache::rememberForever('lastEducationalActivity', function () {
            return Activity::select('id', 'name')->where('sector_id', 55)->latest()->get();
        });
    }
}
