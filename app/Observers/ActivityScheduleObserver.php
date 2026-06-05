<?php

namespace App\Observers;

use App\Models\ActivitySchedule;
use Illuminate\Support\Facades\Cache;

class ActivityScheduleObserver
{
    /**
     * Handle the ActivitySchedule "created" event.
     */
    public function created(ActivitySchedule $activitySchedule): void
    {
        \App\Reposotries\ActivitySchedules::clearCache();
    }

    /**
     * Handle the ActivitySchedule "updated" event.
     */
    public function updated(ActivitySchedule $activitySchedule): void
    {
        \App\Reposotries\ActivitySchedules::clearCache();
    }

    /**
     * Handle the ActivitySchedule "deleted" event.
     */
    public function deleted(ActivitySchedule $activitySchedule): void
    {
        \App\Reposotries\ActivitySchedules::clearCache();
    }
}
