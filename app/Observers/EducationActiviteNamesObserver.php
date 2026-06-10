<?php

namespace App\Observers;

use App\Models\EducationalActivityName;
use Illuminate\Support\Facades\Cache;

class EducationActiviteNamesObserver
{
    public function created(EducationalActivityName $educationalActivityName): void
    {
        Cache::forget('education-activites-names-all');
    }

    public function updated(EducationalActivityName $educationalActivityName): void
    {
        Cache::forget('education-activites-names-all');
    }


    public function deleted(EducationalActivityName $educationalActivityName): void
    {
        Cache::forget('education-activites-names-all');
    }
}
