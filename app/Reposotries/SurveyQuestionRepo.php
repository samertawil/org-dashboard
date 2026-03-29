<?php

namespace App\Reposotries;

use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\Cache;

class SurveyQuestionRepo
{
    public static function data() {
    return Cache::rememberForever('SurveyQuestion-all', function () {
       return  SurveyQuestion::get();
     });
 }
}




 