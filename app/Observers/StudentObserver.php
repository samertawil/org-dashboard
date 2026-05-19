<?php

namespace App\Observers;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;

class StudentObserver
{
    
    public function created(Student $student): void
    {
        Cache::forget('StudentData-all');
        Cache::forget('StudentData-all-with-relations');
        Cache::forget('StudentGroup-activeToday');
    }

    
    public function updated(Student $student): void
    {
        Cache::forget('StudentData-all');
        Cache::forget('StudentData-all-with-relations');
        Cache::forget('StudentGroup-activeToday');
    }

    
    public function deleted(Student $student): void
    {
        Cache::forget('StudentData-all');
        Cache::forget('StudentData-all-with-relations');
        Cache::forget('StudentGroup-activeToday');
    }

   
}
