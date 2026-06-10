<?php

namespace App\Reposotries;

use App\Models\EducationalActivityName;
use Illuminate\Support\Facades\Cache;

class EducationalActivityNameRepo
{

    public static function EducationActiviteNames()
    {
        return Cache::rememberForever('education-activites-names-all', function () {
            return EducationalActivityName::get();
        });
    }
}
