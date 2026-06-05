<?php

namespace App\Observers;

use App\Models\Activity;
use Illuminate\Support\Facades\Cache;

class ActivityObserver
{
    /**
     * Handle the Activity "created" event.
     */
    public function created(Activity $activity): void
    {
        Cache::forget('activites-all');
        Cache::forget('lastEducationalActivity');
        Cache::forget('activites-by-sector');
    }

    /**
     * Handle the Activity "updated" event.
     */
    public function updated(Activity $activity): void
    {
        Cache::forget('activites-all');
        Cache::forget('lastEducationalActivity');
        Cache::forget('activites-by-sector');
    }

    /**
     * Handle the Activity "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        Cache::forget('activites-all');
        Cache::forget('lastEducationalActivity');
        Cache::forget('activites-by-sector');
    }
}
