<?php

namespace App\Observers;

use App\Models\DisplacementCamp;
use Illuminate\Support\Facades\Cache;

class DisplacementCampObserver
{
    
    public function created(DisplacementCamp $displacementCamp): void
    {
      Cache::forget('DisplacementCamp-all');
    }
 
    public function updated(DisplacementCamp $displacementCamp): void
    {
        Cache::forget('DisplacementCamp-all');
    }

    
    public function deleted(DisplacementCamp $displacementCamp): void
    {
        Cache::forget('DisplacementCamp-all');
    }

    
}
