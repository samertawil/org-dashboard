<?php

namespace App\Observers;

use App\Models\StudentGroup;

class StudentGroupSchedules
{
    /**
     * Handle the StudentGroup "created" event.
     */
    public function created(StudentGroup $studentGroup): void
    {
        // dd($studentGroup);
    }

    /**
     * Handle the StudentGroup "updated" event.
     */
    public function updated(StudentGroup $studentGroup): void
    {
        //
    }

    /**
     * Handle the StudentGroup "deleted" event.
     */
    public function deleted(StudentGroup $studentGroup): void
    {
        //
    }

    /**
     * Handle the StudentGroup "restored" event.
     */
    public function restored(StudentGroup $studentGroup): void
    {
        //
    }

    /**
     * Handle the StudentGroup "force deleted" event.
     */
    public function forceDeleted(StudentGroup $studentGroup): void
    {
        //
    }
}
