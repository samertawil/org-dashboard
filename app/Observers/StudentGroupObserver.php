<?php

namespace App\Observers;

use App\Models\StudentGroup;
use Illuminate\Support\Facades\Cache;

class StudentGroupObserver
{
    /**
     * Handle the StudentGroup "created" event.
     */
    public function created(StudentGroup $studentGroup): void
    {
        Cache::forget('StudentGroup-all');
    }

    /**
     * Handle the StudentGroup "updated" event.
     */
    public function updated(StudentGroup $studentGroup): void
    {
        Cache::forget('StudentGroup-all');
    }

    /**
     * Handle the StudentGroup "deleted" event.
     */
    public function deleted(StudentGroup $studentGroup): void
    {
        Cache::forget('StudentGroup-all');
    }

    /**
     * Handle the StudentGroup "restored" event.
     */
    public function restored(StudentGroup $studentGroup): void
    {
        Cache::forget('StudentGroup-all');
    }

    /**
     * Handle the StudentGroup "force deleted" event.
     */
    public function forceDeleted(StudentGroup $studentGroup): void
    {
        Cache::forget('StudentGroup-all');
    }
}
