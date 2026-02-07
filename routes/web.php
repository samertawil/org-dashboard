<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\OrgApp\Dashboard\MyTasks;
use App\Livewire\OrgApp\ActivitySector\Index;
use App\Livewire\OrgApp\Student\ImportedFiles;
use App\Livewire\AppSetting\Role\GrantUserRole;
use App\Livewire\AppSetting\Role\Edit as RoleEdit;
use App\Livewire\AppSetting\Role\Index as RoleIndex;
use App\Livewire\OrgApp\Partner\Edit as PartnerEdit;
use App\Livewire\OrgApp\Student\Edit as StudentEdit;
use App\Livewire\OrgApp\StudentGroups\DailyStudents;
use App\Livewire\AppSetting\Role\Create as RoleCreate;
use App\Livewire\AppSetting\Status\Edit as StatusEdit;

use App\Livewire\OrgApp\Activity\Edit as ActivityEdit;
use App\Livewire\OrgApp\Activity\Show as ActivityShow;
use App\Livewire\OrgApp\Currency\Edit as CurrencyEdit;
use App\Livewire\OrgApp\Employee\Edit as EmployeeEdit;
use App\Livewire\OrgApp\Partner\Index as PartnerIndex;
use App\Livewire\OrgApp\Student\Index as StudentIndex;
use App\Livewire\AppSetting\Users\Index as  UsersIndex;
use App\Livewire\OrgApp\Student\Create as StudentCreat;
use App\Livewire\AppSetting\Ability\Edit as AbilityEdit;
use App\Livewire\AppSetting\Status\Index as StatusIndex;
use App\Livewire\OrgApp\Activity\Index as ActivityIndex;
use App\Livewire\OrgApp\Currency\Index as CurrencyIndex;
use App\Livewire\OrgApp\Employee\Index as EmployeeIndex;
use App\Livewire\OrgApp\Partner\Create as PartnerCreate;
use App\Livewire\AppSetting\Users\Create as  UsersCreate;
use App\Livewire\AppSetting\Ability\Index as AbilityIndex;
use App\Livewire\AppSetting\Status\Create as StatusCreate;
use App\Livewire\OrgApp\Activity\Create as ActivityCreate;
use App\Livewire\OrgApp\Currency\Create as CurrencyCreate;
use App\Livewire\OrgApp\Department\Edit as DepartmentEdit;
use App\Livewire\OrgApp\Employee\Create as EmployeeCreate;
use App\Livewire\AppSetting\Ability\Create as AbilityCreate;
use App\Livewire\OrgApp\Department\Index as DepartmentIndex;
use App\Livewire\OrgApp\SubjectForLearn\Edit as SubjectEdit;
use App\Livewire\OrgApp\Department\Create as DepartmentCreate;
use App\Livewire\OrgApp\SubjectForLearn\Index as SubjectIndex;

use App\Livewire\OrgApp\StudentGroups\Edit as StudentGroupEdit;
use App\Livewire\OrgApp\SubjectForLearn\Create as SubjectCreate;
use App\Livewire\OrgApp\TeachingGroup\Edit as TeachingGroupEdit;

use App\Livewire\OrgApp\StudentGroups\Index as StudentGroupIndex;
use App\Livewire\AppSetting\SystemNames\Index as SystemNamesIndex;
use App\Livewire\OrgApp\TeachingGroup\Index as TeachingGroupIndex;
use App\Livewire\OrgApp\StudentGroups\Create as StudentGroupCreate;
use App\Livewire\AppSetting\SystemNames\Create as SystemNamesCreate;
use App\Livewire\OrgApp\TeachingGroup\Create as TeachingGroupCreate;
use App\Livewire\AppSetting\RoleModuleName\Create as RoleModuleNameCreate;
use App\Livewire\OrgApp\StudentGroups\ShowSchedule as StudentGroupSchedule;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/features', 'features')->name('features');

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
Route::get('/dashboard/student-group/{group}/schedule', StudentGroupSchedule::class)->name('student.group.schedule');
Route::get('/dashboard/student-group/{group}/schedule/{date}/students', DailyStudents::class)->name('student.group.date.students');
Route::get('/dashboard/student-group/{group}/report', \App\Livewire\OrgApp\StudentGroups\Report::class)->name('student.group.report');
Route::get('/dashboard/reports/groups-attendance', \App\Livewire\OrgApp\Reports\GroupsAttendance::class)->name('reports.groups.attendance');
Route::get('/dashboard/reports/activity-overview', \App\Livewire\OrgApp\Reports\ActivityOverview::class)->name('reports.activity.overview');
Route::get('/dashboard/reports/financial-summary', \App\Livewire\OrgApp\Reports\FinancialSummary::class)->name('reports.financial.summary');
Route::get('/dashboard/reports/beneficiary-impact', \App\Livewire\OrgApp\Reports\BeneficiaryImpact::class)->name('reports.beneficiary.impact');
Route::get('/dashboard/reports/educational-progress', \App\Livewire\OrgApp\Reports\EducationalProgress::class)->name('reports.educational.progress');
Route::get('/dashboard/reports/feedback-analysis', \App\Livewire\OrgApp\Reports\FeedbackAnalysis::class)->name('reports.feedback.analysis');
Route::get('/dashboard/calendar', \App\Livewire\OrgApp\Calendar\Index::class)->name('calendar.index');






Route::get('dashboard/student',StudentIndex::class)->name('student.index');
Route::get('dashboard/student/imported-files',ImportedFiles::class)->name('student.imported-files');
Route::get('dashboard/student/create',StudentCreat::class)->name('student.create');
Route::get('dashboard/student/{student}/edit',StudentEdit::class)->name('student.edit');
 
Route::get('dashboard/teaching-group',TeachingGroupIndex::class)->name('teaching.group.index');
Route::get('dashboard/teaching-group/create',TeachingGroupCreate::class)->name('teaching.group.create');
Route::get('dashboard/teaching-group/{group}/edit',TeachingGroupEdit::class)->name('teaching.group.edit');

Route::get('dashboard/currency', CurrencyIndex::class)->name('currency.index');
Route::get('dashboard/currency/create', CurrencyCreate::class)->name('currency.create');
Route::get('dashboard/currency/{currency}/edit', CurrencyEdit::class)->name('currency.edit');

Route::get('dashboard/learnin-subject/create',SubjectCreate::class)->name('subject.create');
Route::get('dashboard/learnin-subject/{subject}/edit',SubjectEdit::class)->name('subject.edit');
Route::get('dashboard/learnin-subject',SubjectIndex::class)->name('subject.index');
 
Route::get('dashboard/gallery', \App\Livewire\OrgApp\Gallery\Index::class)->name('gallery.index');
 

Route::get('dashboard/my-tasks', MyTasks::class)->name('my.tasks');
});
