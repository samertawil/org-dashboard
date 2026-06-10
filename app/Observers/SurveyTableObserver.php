<?php

namespace App\Observers;

use App\Models\SurveyTable;
use Illuminate\Support\Facades\Cache;

class SurveyTableObserver
{
    /**
     * Handle the SurveyTable "created" event.
     */
    public function created(SurveyTable $surveyTable): void
    {
        Cache::forget('survey-table-all');
    }

    /**
     * Handle the SurveyTable "updated" event.
     */
    public function updated(SurveyTable $surveyTable): void
    {
        Cache::forget('survey-table-all');
    }

    /**
     * Handle the SurveyTable "deleted" event.
     */
    public function deleted(SurveyTable $surveyTable): void
    {
        Cache::forget('survey-table-all');
    }
}
