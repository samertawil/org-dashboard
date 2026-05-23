<?php

namespace App\Observers;

use App\Models\TeacherStudentGroup;
use App\Models\Employee;
use Illuminate\Support\Facades\Cache;

class TeacherStudentGroupObserver
{
    /**
     * Clear the cache for the teacher/employee.
     */
    private function clearCache(TeacherStudentGroup $mapping): void
    {
        $employee = $mapping->teacher;
        if ($employee) {
            Cache::forget("employee-groups-{$employee->id}");
        }
    }

    /**
     * Handle the TeacherStudentGroup "created" event.
     */
    public function created(TeacherStudentGroup $mapping): void
    {
        $this->clearCache($mapping);
    }

    /**
     * Handle the TeacherStudentGroup "updated" event.
     */
    public function updated(TeacherStudentGroup $mapping): void
    {
        // Clear new teacher cache
        $this->clearCache($mapping);

        // If teacher_id changed, clear old teacher cache
        if ($mapping->isDirty('teacher_id')) {
            $oldTeacherId = $mapping->getOriginal('teacher_id');
            if ($oldTeacherId) {
                $oldEmployee = Employee::where('user_id', $oldTeacherId)->first();
                if ($oldEmployee) {
                    Cache::forget("employee-groups-{$oldEmployee->id}");
                }
            }
        }
    }

    /**
     * Handle the TeacherStudentGroup "deleted" event.
     */
    public function deleted(TeacherStudentGroup $mapping): void
    {
        $this->clearCache($mapping);
    }

    /**
     * Handle the TeacherStudentGroup "restored" event.
     */
    public function restored(TeacherStudentGroup $mapping): void
    {
        $this->clearCache($mapping);
    }

    /**
     * Handle the TeacherStudentGroup "force deleted" event.
     */
    public function forceDeleted(TeacherStudentGroup $mapping): void
    {
        $this->clearCache($mapping);
    }
}
