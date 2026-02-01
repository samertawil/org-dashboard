<?php

namespace App\Observers;

 

use App\Models\Department;
use Illuminate\Support\Facades\Cache;

class DepartmentObserver
{
     
    public function created(Department $department): void
    {
        Cache::forget('Department-all');
    }

    /**
     * Handle the Status "updated" event.
     */
    public function updated(Department $department): void
    {
        Cache::forget('Department-all');
    }

    /**
     * Handle the Status "deleted" event.
     */
    public function deleted(Department $department): void
    {
        Cache::forget('Department-all');
    }

     
}
