<?php

namespace App\Livewire\OrgApp\Dashboard;

use App\Models\Activity;
use App\Reposotries\ActivityBeneficiaryRepo;
use App\Reposotries\ActivityRepo;
use App\Reposotries\EventAssigneeRepo;
use App\Reposotries\PurchaseRequisitionRepo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{

    #[Computed()]
    public function activities() {
        if (!Gate::allows('activity.index')) {
            return collect();
        }

        $activitiesBySector =

        Activity::select('sector_id', DB::raw('count(*) as total'))
        ->with('statusSpecificSector')
        ->groupBy('sector_id')
        ->get()
        ->map(function ($item) {
            return [
                'label' => $item->statusSpecificSector->status_name ?? 'Unknown',
                'value' => $item->total,
            ];
        });

        return  $activitiesBySector;
    }

    
    #[Title('AFSC Dashboard')]
    public function render()
    {
        $hasActivityAccess = Gate::allows('activity.index');

        // 1. KPI Cards Data
        $activeActivitiesCount = $hasActivityAccess ? ActivityRepo::activites()->count() : 0;

        $totalBeneficiaries =  $hasActivityAccess ? ActivityBeneficiaryRepo::beneficiaries()->sum('beneficiaries_count') : 0;

        // Budget/Expenses (Mock logic for now as budget structure might vary)
        $totalBudget = $hasActivityAccess ? ActivityRepo::activites()->sum('cost') : 0;
        $totalBudgetNis = $hasActivityAccess ? ActivityRepo::activites()->sum('cost_nis') : 0;

        $pendingRequests = $hasActivityAccess ? PurchaseRequisitionRepo::purchases()->count() : 0;
      
        // 3. Recent Activity Feed
        $plannedActivities = $hasActivityAccess ? ActivityRepo::activites()->where('start_date', '>', now())->take(5) : collect();

        // 4. My Tasks
        $myTasks = EventAssigneeRepo::eventAssignees();

        // 5. Educational Activity Tasks (Delayed and Required Now)
        $employeeId = auth()->user()->employee?->id;
        $educationalTasksQuery = \App\Models\ActivitySchedule::query()
            ->with(['activityDetail', 'employee', 'activityDomain', 'periodGroups', 'group'])
            ->active()
            ->where(function ($q) {
                $q->delayed()->orWhere(fn($sub) => $sub->requiredNow());
            });

        if (!(auth()->user()->isSuperAdmin() || Gate::allows('select.any.educational-activity-detail') || Gate::allows('select.any.student'))) {
            $educationalTasksQuery->where('employee_id', $employeeId);
        }

        $educationalTasks = $educationalTasksQuery->ordered()->take(5)->get();
 
        return view('livewire.org-app.dashboard.index', [
            'activeActivitiesCount' => $activeActivitiesCount,
            'totalBeneficiaries' => $totalBeneficiaries,
            'totalBudget' => $totalBudget,
            'totalBudgetNis' => $totalBudgetNis,
            'pendingRequests' => $pendingRequests,
            'plannedActivities' => $plannedActivities,
            'myTasks' => $myTasks,
            'educationalTasks' => $educationalTasks,
            'hasActivityAccess' => $hasActivityAccess,
        ]);
    }
}
