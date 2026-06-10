<?php

namespace App\Reposotries;

use App\Models\SurveyTable;
use Illuminate\Support\Facades\Cache;

class SurveyTableRepo
{
    public static function surveys()
    {
        return Cache::rememberForever('survey-table-all', function () {
            return SurveyTable::get();
        });
    }
}
