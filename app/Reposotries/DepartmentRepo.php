<?php

namespace App\Reposotries;


use App\Models\Department;
use Illuminate\Support\Facades\Cache;

class DepartmentRepo
{
    public static function departments()
    {
       return Cache::rememberForever('Department-all', function () {
            return Department::select('id', 'name')->get();
        });
    }
}
