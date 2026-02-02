<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\AppSetting\Role\Edit as RoleEdit;
use App\Livewire\AppSetting\Role\Index as RoleIndex;
use App\Livewire\AppSetting\Role\Create as RoleCreate;
use App\Livewire\AppSetting\Role\GrantUserRole;
use App\Livewire\AppSetting\Status\Edit as StatusEdit;
use App\Livewire\AppSetting\Ability\Edit as AbilityEdit;
use App\Livewire\AppSetting\Status\Index as StatusIndex;
use App\Livewire\AppSetting\Users\Create as  UsersCreate;
use App\Livewire\AppSetting\Users\Index as  UsersIndex;
use App\Livewire\AppSetting\Ability\Index as AbilityIndex;
use App\Livewire\AppSetting\Status\Create as StatusCreate;

use App\Livewire\AppSetting\Ability\Create as AbilityCreate;
use App\Livewire\AppSetting\SystemNames\Index as SystemNamesIndex;
use App\Livewire\AppSetting\SystemNames\Create as SystemNamesCreate;
use App\Livewire\Appsetting\RoleModuleName\Create as RoleModuleNameCreate;
use App\Livewire\OrgApp\Department\Index as DepartmentIndex;
use App\Livewire\OrgApp\Department\Create as DepartmentCreate;
use App\Livewire\OrgApp\Department\Edit as DepartmentEdit;
use App\Livewire\OrgApp\Employee\Index as EmployeeIndex;
use App\Livewire\OrgApp\Employee\Create as EmployeeCreate;
use App\Livewire\OrgApp\Employee\Edit as EmployeeEdit;
use App\Livewire\OrgApp\Activity\Index as ActivityIndex;
use App\Livewire\OrgApp\Activity\Create as ActivityCreate;
use App\Livewire\OrgApp\Activity\Edit as ActivityEdit;
use App\Livewire\OrgApp\Activity\Show as ActivityShow;
use App\Livewire\OrgApp\ActivitySector\Index;
use App\Livewire\OrgApp\Partner\Create as PartnerCreate;
use App\Livewire\OrgApp\Partner\Edit as PartnerEdit;
use App\Livewire\OrgApp\Partner\Index as PartnerIndex;
use App\Livewire\OrgApp\Student\Create as StudentCreat;
use App\Livewire\OrgApp\Student\Edit as StudentEdit;
use App\Livewire\OrgApp\Student\Index as StudentIndex;
use App\Livewire\OrgApp\Student\ImportedFiles;
use App\Livewire\OrgApp\StudentGroups\Create as StudentGroupCreate;
use App\Livewire\OrgApp\StudentGroups\Edit as StudentGroupEdit;
use App\Livewire\OrgApp\StudentGroups\Index as StudentGroupIndex;





Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';


// Users
Route::middleware(['auth'])->group(function () {
   
Route::get('/dashboard/users/create',UsersCreate::class)->name('user.create');
Route::get('/dashboard/users/index',UsersIndex::class)->name('user.index');
Route::get('dashboard/grant-role-to/{userID?}',GrantUserRole::class)->name('grant.role.user');

// Status
Route::get('/dashboard/system-names/create',SystemNamesCreate::class)->name('system.names.create');
Route::get('/dashboard/system-names',SystemNamesIndex::class)->name('system.names.index');
Route::get('/dashboard/status/create',StatusCreate::class)->name('status.create');
Route::get('/dashboard/status',StatusIndex::class)->name('status.index');
Route::get('/dashboard/{status}/edit',StatusEdit::class)->name('status.edit');

// Ability
Route::get('/dashboard/ability/create',AbilityCreate::class)->name('ability.create');
Route::get('/dashboard/ability/{ability}/edit',AbilityEdit::class)->name('ability.edit');
Route::get('/dashboard/ability/',AbilityIndex::class)->name('ability.index');

// Role
Route::get('/dashboard/role-module/create',RoleModuleNameCreate::class)->name('role.module.create');
Route::get('/dashboard/role/create',RoleCreate::class)->name('role.create');
Route::get('/dashboard/role',RoleIndex::class)->name('role.index');
Route::get('dashboard/role/{id?}/edit',RoleEdit::class)->name('role.edit');


// OrgApp - Department
Route::get('/dashboard/department', DepartmentIndex::class)->name('department.index');
Route::get('/dashboard/department/create', DepartmentCreate::class)->name('department.create');
Route::get('/dashboard/department/{department}/edit', DepartmentEdit::class)->name('department.edit');

// OrgApp - Employee
Route::get('/dashboard/employee', EmployeeIndex::class)->name('employee.index');
Route::get('/dashboard/employee/create', EmployeeCreate::class)->name('employee.create');
Route::get('/dashboard/employee/{employee}/edit', EmployeeEdit::class)->name('employee.edit');

// OrgApp - activity
Route::get('/dashboard/activity', ActivityIndex::class)->name('activity.index');
Route::get('/dashboard/activity/create', ActivityCreate::class)->name('activity.create');
Route::get('/dashboard/activity/{activity}/edit', ActivityEdit::class)->name('activity.edit');
Route::get('/dashboard/activity/{activity}/show', ActivityShow::class)->name('activity.show');

Route::get('/dashboard/sectors', Index::class)->name('sector.show');

Route::get('/dashboard/partner/create', PartnerCreate::class)->name('partner.create');
Route::get('/dashboard/partner/{partner}/edit', PartnerEdit::class)->name('partner.edit');
Route::get('/dashboard/partner', PartnerIndex::class)->name('partner.index');

Route::get('/dashboard/student-group', StudentGroupIndex::class)->name('student.group.index');
Route::get('/dashboard/student-group/create', StudentGroupCreate::class)->name('student.group.create');
Route::get('/dashboard/student-group/{group}/edit', StudentGroupEdit::class)->name('student.group.edit');

Route::get('dashboard/student',StudentIndex::class)->name('student.index');
Route::get('dashboard/student/imported-files',ImportedFiles::class)->name('student.imported-files');
Route::get('dashboard/student/create',StudentCreat::class)->name('student.create');
Route::get('dashboard/student/{student}/edit',StudentEdit::class)->name('student.edit');
 

});
