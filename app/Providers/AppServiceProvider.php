<?php

namespace App\Providers;

use App\Models\Status;
use App\Models\Ability;
use App\Models\Employee;
use App\Models\Department;
use Carbon\CarbonImmutable;
use App\Observers\StatusObserver;
use App\Repositories\AbilityRepo;
use Illuminate\Support\Facades\DB;
use App\Observers\EmployeeObserver;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;
use App\Observers\DepartmentObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Status::observe(StatusObserver::class);
        Employee::observe(EmployeeObserver::class);
        Department::observe(DepartmentObserver::class);


        date_default_timezone_set('Asia/Gaza');

        
        Gate::before(function ($user, $ability) {
            if (!$user) return null;

            $userIdsWithFullAccess = [1];
            if (in_array($user->id, $userIdsWithFullAccess)) {
                return true;
            }

            // Dynamically load abilities and check permissions
            $abilities = Cache::rememberForever('abilities_list', function () {
                return Ability::all();
            });

            if ($data = $abilities->firstWhere('ability_name', $ability)) {
                if ($user->activation != 1) {
                    return false;
                }

                foreach ($user->rolesRelation as $role) {
                    if (in_array($data->ability_name, $role->abilities)) {
                        return true;
                    }
                }
                return false;
            }

            return null; // Let other gates handle it if not an 'Ability'
        });
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null
        );
    }
}
