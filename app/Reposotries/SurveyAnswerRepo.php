<?php

namespace App\Reposotries;

use App\Models\SurveyAnswer;
use Illuminate\Support\Facades\Cache;

class SurveyAnswerRepo
{  
    public static function data() {
       return Cache::rememberForever('SurveyAnswer-all', function () {
          return  SurveyAnswer::get();
        });
    }
}
