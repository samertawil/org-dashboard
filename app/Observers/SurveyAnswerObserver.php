<?php

namespace App\Observers;

use App\Models\SurveyAnswer;
use Illuminate\Support\Facades\Cache;

class SurveyAnswerObserver
{
     
    public function created(SurveyAnswer $surveyAnswer): void
    {
        Cache::forget('StudentData-all-with-relations');
        Cache::forget('StudentData-all');
        Cache::forget('SurveyAnswer-all');
   
    }

    
    public function updated(SurveyAnswer $surveyAnswer): void
    {
        Cache::forget('StudentData-all-with-relations');
        Cache::forget('StudentData-all');
        Cache::forget('SurveyAnswer-all');
    }

    
    public function deleted(SurveyAnswer $surveyAnswer): void
    {
        Cache::forget('StudentData-all-with-relations');
        Cache::forget('StudentData-all');
        Cache::forget('SurveyAnswer-all');
    }

    
}
