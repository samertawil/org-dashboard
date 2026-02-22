<?php

use App\Livewire\AppSetting\Ability\Create as AbilityCreate;
use App\Livewire\AppSetting\Ability\Edit as AbilityEdit;
use App\Livewire\AppSetting\Ability\Index as AbilityIndex;
use App\Livewire\AppSetting\Role\Create as RoleCreate;
use App\Livewire\AppSetting\Role\Edit as RoleEdit;
use App\Livewire\AppSetting\Role\GrantUserRole;
use App\Livewire\AppSetting\Role\Index as RoleIndex;
use App\Livewire\AppSetting\RoleModuleName\Create as RoleModuleNameCreate;
use App\Livewire\AppSetting\Status\Create as StatusCreate;
use App\Livewire\AppSetting\Status\Edit as StatusEdit;
use App\Livewire\AppSetting\Status\Index as StatusIndex;
use App\Livewire\AppSetting\SystemNames\Create as SystemNamesCreate;

use App\Livewire\AppSetting\SystemNames\Index as SystemNamesIndex;
use App\Livewire\AppSetting\Users\Create as  UsersCreate;
use App\Livewire\AppSetting\Users\Index as  UsersIndex;
use App\Livewire\OrgApp\Activity\Create as ActivityCreate;
use App\Livewire\OrgApp\Activity\Edit as ActivityEdit;
use App\Livewire\OrgApp\Activity\Index as ActivityIndex;
use App\Livewire\OrgApp\Activity\Show as ActivityShow;
use App\Livewire\OrgApp\ActivitySector\Index;
use App\Livewire\OrgApp\Currency\Create as CurrencyCreate;
use App\Livewire\OrgApp\Currency\Edit as CurrencyEdit;
use App\Livewire\OrgApp\Currency\Index as CurrencyIndex;
use App\Livewire\OrgApp\Dashboard\MyTasks;
use App\Livewire\OrgApp\Department\Create as DepartmentCreate;
use App\Livewire\OrgApp\Department\Edit as DepartmentEdit;
use App\Livewire\OrgApp\Department\Index as DepartmentIndex;
use App\Livewire\OrgApp\Employee\Create as EmployeeCreate;
use App\Livewire\OrgApp\Employee\Edit as EmployeeEdit;
use App\Livewire\OrgApp\Employee\Index as EmployeeIndex;
use App\Livewire\OrgApp\Partner\Create as PartnerCreate;
use App\Livewire\OrgApp\Partner\Edit as PartnerEdit;
use App\Livewire\OrgApp\Partner\Index as PartnerIndex;
use App\Livewire\OrgApp\Student\Create as StudentCreat;
use App\Livewire\OrgApp\Student\Edit as StudentEdit;
use App\Livewire\OrgApp\Student\ImportedFiles;
use App\Livewire\OrgApp\Student\Index as StudentIndex;
use App\Livewire\OrgApp\StudentGroups\Create as StudentGroupCreate;

use App\Livewire\OrgApp\StudentGroups\DailyStudents;
use App\Livewire\OrgApp\StudentGroups\Edit as StudentGroupEdit;
use App\Livewire\OrgApp\StudentGroups\Index as StudentGroupIndex;

use App\Livewire\OrgApp\StudentGroups\ShowSchedule as StudentGroupSchedule;
use App\Livewire\OrgApp\SubjectForLearn\Create as SubjectCreate;
use App\Livewire\OrgApp\SubjectForLearn\Edit as SubjectEdit;
use App\Livewire\OrgApp\SubjectForLearn\Index as SubjectIndex;
use App\Livewire\OrgApp\TeachingGroup\Create as TeachingGroupCreate;
use App\Livewire\OrgApp\TeachingGroup\Edit as TeachingGroupEdit;
use App\Livewire\OrgApp\TeachingGroup\Index as TeachingGroupIndex;
use App\Livewire\SocialLogin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/features', 'features')->name('features');

Route::get('dashboard', \App\Livewire\OrgApp\Dashboard\Index::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';

Route::get('{provider}/social-redirect', [SocialLogin::class, 'socialRedirect'])->name('social.redirect');
Route::get('{provider}/social-callback', [SocialLogin::class, 'socialCallback'])->name('social.callback');

// Users
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
   
    Route::get('/users/create',UsersCreate::class)->name('user.create');
    Route::get('/users/index',UsersIndex::class)->name('user.index');
    Route::get('/grant-role-to/{userID?}',GrantUserRole::class)->name('grant.role.user');
    
    // Status
    Route::get('/system-names/create',SystemNamesCreate::class)->name('system.names.create');
    Route::get('/system-names',SystemNamesIndex::class)->name('system.names.index');
    Route::get('/status/create',StatusCreate::class)->name('status.create');
    Route::get('/status',StatusIndex::class)->name('status.index');
    Route::get('/{status}/edit',StatusEdit::class)->name('status.edit');
    
    // Ability
    Route::get('/ability/create',AbilityCreate::class)->name('ability.create');
    Route::get('/ability/{ability}/edit',AbilityEdit::class)->name('ability.edit');
    Route::get('/ability',AbilityIndex::class)->name('ability.index');
    
    // Role
    Route::get('/role-module/create',RoleModuleNameCreate::class)->name('role.module.create');
    Route::get('/role/create',RoleCreate::class)->name('role.create');
    Route::get('/role',RoleIndex::class)->name('role.index');
    Route::get('/role/{id?}/edit',RoleEdit::class)->name('role.edit');
    
    
    
    
    // OrgApp - Department
    Route::get('/department', DepartmentIndex::class)->name('department.index');
    Route::get('/department/create', DepartmentCreate::class)->name('department.create');
    Route::get('/department/{department}/edit', DepartmentEdit::class)->name('department.edit');
    
    // OrgApp - Employee
    Route::get('/employee', EmployeeIndex::class)->name('employee.index');
    Route::get('/employee/create', EmployeeCreate::class)->name('employee.create');
    Route::get('/employee/{employee}/edit', EmployeeEdit::class)->name('employee.edit');
    
    // OrgApp - activity
    Route::get('/activity', ActivityIndex::class)->name('activity.index');
    Route::get('/activity/create', ActivityCreate::class)->name('activity.create');
    Route::get('/activity/{activity}/edit', ActivityEdit::class)->name('activity.edit');
    Route::get('/activity/{activity}/show', ActivityShow::class)->name('activity.show');
    Route::get('/activity/{activity}/gallery', \App\Livewire\OrgApp\Activity\Gallery::class)->name('activity.gallery');
    
    Route::get('/sectors', Index::class)->name('sector.show');
    
    Route::get('/partner/create', PartnerCreate::class)->name('partner.create');
    Route::get('/partner/{partner}/edit', PartnerEdit::class)->name('partner.edit');
    Route::get('/partner', PartnerIndex::class)->name('partner.index');
    
    Route::get('/student-group', StudentGroupIndex::class)->name('student.group.index');
    Route::get('/student-group/create', StudentGroupCreate::class)->name('student.group.create');
    Route::get('/student-group/{group}/edit', StudentGroupEdit::class)->name('student.group.edit');
    Route::get('/student-group/{group}/schedule', StudentGroupSchedule::class)->name('student.group.schedule');
    Route::get('/student-group/{group}/schedule/{date}/students', DailyStudents::class)->name('student.group.date.students');
    Route::get('/student-group/{group}/report', \App\Livewire\OrgApp\StudentGroups\Report::class)->name('student.group.report');
    Route::get('/reports/groups-attendance', \App\Livewire\OrgApp\Reports\GroupsAttendance::class)->name('reports.groups.attendance');
    Route::get('/reports/activity-overview', \App\Livewire\OrgApp\Reports\ActivityOverview::class)->name('reports.activity.overview');
    Route::get('/reports/financial-summary', \App\Livewire\OrgApp\Reports\FinancialSummary::class)->name('reports.financial.summary');
    Route::get('/reports/beneficiary-impact', \App\Livewire\OrgApp\Reports\BeneficiaryImpact::class)->name('reports.beneficiary.impact');
    Route::get('/reports/educational-progress', \App\Livewire\OrgApp\Reports\EducationalProgress::class)->name('reports.educational.progress');
    Route::get('/reports/feedback-analysis', \App\Livewire\OrgApp\Reports\FeedbackAnalysis::class)->name('reports.feedback.analysis');
    Route::get('/calendar', \App\Livewire\OrgApp\Calendar\Index::class)->name('calendar.index');
    
    
    
    
    
    
    Route::get('/student',StudentIndex::class)->name('student.index');
    Route::get('/student/imported-files',ImportedFiles::class)->name('student.imported-files');
    Route::get('/student/create',StudentCreat::class)->name('student.create');
    Route::get('/student/{student}/edit',StudentEdit::class)->name('student.edit');
     
    Route::get('/teaching-group',TeachingGroupIndex::class)->name('teaching.group.index');
    Route::get('/teaching-group/create',TeachingGroupCreate::class)->name('teaching.group.create');
    Route::get('/teaching-group/{group}/edit',TeachingGroupEdit::class)->name('teaching.group.edit');
    
    Route::get('/currency', CurrencyIndex::class)->name('currency.index');
    Route::get('/currency/create', CurrencyCreate::class)->name('currency.create');
    Route::get('/currency/{currency}/edit', CurrencyEdit::class)->name('currency.edit');
    
    Route::get('/learnin-subject/create',SubjectCreate::class)->name('subject.create');
    Route::get('/learnin-subject/{subject}/edit',SubjectEdit::class)->name('subject.edit');
    Route::get('/learnin-subject',SubjectIndex::class)->name('subject.index');
     
    Route::get('/gallery', \App\Livewire\OrgApp\Gallery\Index::class)->name('gallery.index');
     
    
    Route::get('/my-tasks', MyTasks::class)->name('my.tasks');
    
    // OrgApp - Purchase Request
    Route::get('/purchase-request', \App\Livewire\OrgApp\PurchaseRequest\Index::class)->name('purchase_request.index');
    Route::get('/purchase-request/create', \App\Livewire\OrgApp\PurchaseRequest\Create::class)->name('purchase_request.create');
    Route::get('/purchase-request/{purchaseRequisition}/edit', \App\Livewire\OrgApp\PurchaseRequest\Edit::class)->name('purchase_request.edit');
    Route::get('/purchase-request/{purchaseRequisition}/show', \App\Livewire\OrgApp\PurchaseRequest\Show::class)->name('purchase_request.show');
    Route::get('/purchase-request/{purchaseRequisition}/gallery', \App\Livewire\OrgApp\PurchaseRequest\Gallery::class)->name('purchase_request.gallery');
    
    });
    