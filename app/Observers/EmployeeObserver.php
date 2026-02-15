<?php

namespace App\Observers;

 
use App\Models\Employee;
use Illuminate\Support\Facades\Cache;

class EmployeeObserver
{
     
    public function created(Employee $employee): void
    {
        if($employee->activation == 0){
            $employee->user->update(['activation' => 0]);
        }
        Cache::forget('Employee-all');
    }

    /**
     * Handle the Status "updated" event.
     */
    public function updated(Employee $employee): void
    {
        if ($employee->isDirty('activation') && $employee->user) {
            $employee->user->update(['activation' => $employee->activation]);
        }
        
        Cache::forget('Employee-all');
    }

    /**
     * Handle the Status "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        if($employee->user) {

            $employee->user->update(['activation' => 0]);
        }
        Cache::forget('Employee-all');

    }

     
}
