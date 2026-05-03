<?php

namespace App\Reposotries;


use App\Enums\GlobalSystemConstant;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class employeeRepo
{
    public static function employees()
    {
       return Cache::rememberForever('Employee-all', function () {
            return Employee::select('id', 'full_name', 'user_id', 'activation')->get();
        });
    }

    public static function mentionEmp() {
        return Employee::query()
        ->whereNotNull('user_id')
        ->where('activation', GlobalSystemConstant::ACTIVE->value)
        ->where('user_id', '!=', Auth::id())
        ->with('user:id,name')
        ->get()
        ->filter(fn($e) => $e->user !== null)
        ->map(fn($e) => [
            'id'   => $e->user->id,
            'name' => $e->full_name ?? $e->user->name,
        ])
        ->values()
        ->toArray();
    }
}
