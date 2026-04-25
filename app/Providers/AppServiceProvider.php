<?php

namespace App\Providers;

use App\Enums\GlobalSystemConstant;
use App\Models\Ability;
use App\Models\Activity;
use App\Models\ActivityBeneficiary;
use App\Models\Department;
use App\Models\DisplacementCamp;
use App\Models\Employee;
use App\Models\EventAssignee;
use App\Models\PartnerInstitution;
use App\Models\PurchaseRequisition;
use App\Models\Status;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\User;
use App\Observers\AbilitieObserver;
use App\Observers\ActivityObserver;
use App\Observers\DepartmentObserver;
use App\Observers\DisplacementCampObserver;
use App\Observers\EmployeeObserver;
use App\Observers\EventAssigneeObserver;
use App\Observers\PartnersObserver;
use App\Observers\PurchaseRequisitionObserver;
use App\Observers\StatusObserver;
use App\Observers\StudentGroupObserver;
use App\Observers\StudentObserver;
use App\Observers\SurveyAnswerObserver;
use App\Observers\SurveyQuestionObserver;
use App\Observers\UserObserver;
use App\Reposotries\ActivityBeneficiaryRepo;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
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

        Validator::extend('global_validation', function ($attribute, $value, $parameters, $validator) {
            $type = $parameters[0] ?? 'status'; 
            $enum = GlobalSystemConstant::tryFrom((int)$value);
            return $enum && $enum->getType() === $type;
        });

        Status::observe(StatusObserver::class);
        Employee::observe(EmployeeObserver::class);
        Department::observe(DepartmentObserver::class);
        Ability::observe(AbilitieObserver::class);
        PartnerInstitution::observe(PartnersObserver::class);
        StudentGroup::observe(StudentGroupObserver::class);
        EventAssignee::observe(EventAssigneeObserver::class);
        Activity::observe(ActivityObserver::class);
        ActivityBeneficiary::observe(ActivityBeneficiaryRepo::class);
        PurchaseRequisition::observe(PurchaseRequisitionObserver::class);
        DisplacementCamp::observe(DisplacementCampObserver::class);
        SurveyQuestion::observe(SurveyQuestionObserver::class);
        SurveyAnswer::observe(SurveyAnswerObserver::class);
        Student::observe(StudentObserver::class);
        User::observe(UserObserver::class);
        


        date_default_timezone_set('Asia/Gaza');

        
        Gate::before(function ($user, $ability) {
            if (!$user) return null;

            // 1. Super Admin Check
            if ($user->isSuperAdmin()) {
                return true;
            }

            // 2. Check Activation
            if ($user->activation != 1) {
                return false;
            }

            // 3. Load Abilities Definition (Cached names only for performance)
            static $cachedAbilityNames = null;
            if ($cachedAbilityNames === null) {
                $cachedAbilityNames = Cache::rememberForever('abilities_names_list', function () {
                    return Ability::pluck('ability_name')->all();
                });
            }

            // If the requested capability is NOT in our dynamic abilities list, ignore it (return null)
            // This allows Policy classes to work for other things.
            if (!in_array($ability, $cachedAbilityNames)) {
                return null;
            }

            // 4. Flatten User Permissions (Runtime Cache/Optimization)
            // We use a static variable to hold the permissions for the *current request*
            // so we don't loop through roles for every single @can check on the page.
            static $userPermissions = [];
            
            // Check if user permissions are already loaded for the current user ID to avoid conflict if user changes (rare but safe)
            static $lastUserId = null;

            if ($lastUserId !== $user->id) {
                $userPermissions = [];
                $lastUserId = $user->id;
                
                // Eager load roles if possible, or access relation
                foreach ($user->rolesRelation as $role) {
                     // Assuming 'abilities' is a JSON casted array on the Role model
                    if (!empty($role->abilities) && is_array($role->abilities)) {
                        $userPermissions = array_merge($userPermissions, $role->abilities);
                    }
                }
                $userPermissions = array_unique($userPermissions);
            }

            return in_array($ability, $userPermissions);
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
