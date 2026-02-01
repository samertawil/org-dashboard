<?php

namespace App\Reposotries;


use App\Models\Employee;
use Illuminate\Support\Facades\Cache;

class employeeRepo
{
    public static function employees()
    {
       return Cache::rememberForever('Employee-all', function () {
            return Employee::get();
        });
    }
}
