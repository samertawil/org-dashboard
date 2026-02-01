<?php

namespace App\Observers;

 
use App\Models\Employee;
use Illuminate\Support\Facades\Cache;

class EmployeeObserver
{
     
    public function created(Employee $employee): void
    {
        Cache::forget('Employee-all');
    }

    /**
     * Handle the Status "updated" event.
     */
    public function updated(Employee $employee): void
    {
        Cache::forget('Employee-all');
    }

    /**
     * Handle the Status "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        Cache::forget('Employee-all');
    }

     
}
