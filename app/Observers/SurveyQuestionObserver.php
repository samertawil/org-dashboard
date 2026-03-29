<?php

namespace App\Observers;

use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\Cache;

class SurveyQuestionObserver
{
    
    public function created(SurveyQuestion $surveyQuestion): void
    {
        Cache::forget('SurveyQuestion-all');
    }

    
    public function updated(SurveyQuestion $surveyQuestion): void
    {
        Cache::forget('SurveyQuestion-all');
    }

    
    public function deleted(SurveyQuestion $surveyQuestion): void
    {
        Cache::forget('SurveyQuestion-all');
    }

    
}
