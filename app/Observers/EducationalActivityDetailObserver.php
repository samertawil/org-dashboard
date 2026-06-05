<?php

namespace App\Observers;

use App\Models\EducationalActivityDetail;
use Illuminate\Support\Facades\Cache;

class EducationalActivityDetailObserver
{
    /**
     * Handle the EducationalActivityDetail "created" event.
     */
    public function created(EducationalActivityDetail $educationalActivityDetail): void
    {
        \App\Reposotries\ActivitySchedules::clearCache();
    }

    /**
     * Handle the EducationalActivityDetail "updated" event.
     */
    public function updated(EducationalActivityDetail $educationalActivityDetail): void
    {
        \App\Reposotries\ActivitySchedules::clearCache();
    }

    /**
     * Handle the EducationalActivityDetail "deleted" event.
     */
    public function deleted(EducationalActivityDetail $educationalActivityDetail): void
    {
        \App\Reposotries\ActivitySchedules::clearCache();
    }
}
