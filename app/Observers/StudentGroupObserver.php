<?php

namespace App\Observers;

use App\Models\StudentGroup;
use Illuminate\Support\Facades\Cache;

class StudentGroupObserver
{
    /**
     * Clear all related student group and employee/teacher caches.
     */
    private function clearCaches(StudentGroup $studentGroup): void
    {
        Cache::forget('StudentGroup-all');
        Cache::forget('StudentGroup-activeToday');

        // Clear cache for all teachers assigned to this group
        $studentGroup->teachers()->pluck('employees.id')->each(function ($employeeId) {
            Cache::forget("employee-groups-{$employeeId}");
        });
    }

    /**
     * Handle the StudentGroup "created" event.
     */
    public function created(StudentGroup $studentGroup): void
    {
        $this->clearCaches($studentGroup);
    }

    /**
     * Handle the StudentGroup "updated" event.
     */
    public function updated(StudentGroup $studentGroup): void
    {
        $this->clearCaches($studentGroup);
    }

    /**
     * Handle the StudentGroup "deleted" event.
     */
    public function deleted(StudentGroup $studentGroup): void
    {
        $this->clearCaches($studentGroup);
    }

    /**
     * Handle the StudentGroup "restored" event.
     */
    public function restored(StudentGroup $studentGroup): void
    {
        $this->clearCaches($studentGroup);
    }

    /**
     * Handle the StudentGroup "force deleted" event.
     */
    public function forceDeleted(StudentGroup $studentGroup): void
    {
        $this->clearCaches($studentGroup);
    }
}
