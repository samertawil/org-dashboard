<?php

namespace App\Livewire\OrgApp\Dashboard;

use App\Reposotries\ActivityBeneficiaryRepo;
use App\Reposotries\ActivityRepo;
use App\Reposotries\ActivitySchedules;
use App\Reposotries\EventAssigneeRepo;
use App\Reposotries\PurchaseRequisitionRepo;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;


class Index extends Component
{

    #[Computed()]
    public function educationalTasksQuery()
    {
        if (Gate::allows('educational-activity-detail.index') || Gate::allows('select.any.student')) {

            return ActivitySchedules::educationalTasksQuery();
        }
        return collect([]);
    }




    #[Title('AFSC Dashboard')]
    public function render()
    {
        $hasActivityAccess = Gate::allows('dashboard.relief.statistics');

        // 1. KPI Cards Data
        $activeActivitiesCount = $hasActivityAccess ? ActivityRepo::activites()->count() : 0;

        $totalBeneficiaries =  $hasActivityAccess ? ActivityBeneficiaryRepo::beneficiaries()->sum('beneficiaries_count') : 0;
        $activitiesBySector = $hasActivityAccess ? ActivityRepo::activitiesBySector() : collect();
        // Budget/Expenses (Mock logic for now as budget structure might vary)
        $totalBudget = $hasActivityAccess ? ActivityRepo::activites()->sum('cost') : 0;
        $totalBudgetNis = $hasActivityAccess ? ActivityRepo::activites()->sum('cost_nis') : 0;

        $pendingRequests = $hasActivityAccess ? PurchaseRequisitionRepo::purchases()->count() : 0;

        // 3. Recent Activity Feed
        $plannedActivities = $hasActivityAccess ? ActivityRepo::activites()->where('start_date', '>', now())->take(5) : collect();

        // 4. My Tasks
        $myTasks = EventAssigneeRepo::eventAssignees();

        // 5. Educational Activity Tasks (Delayed and Required Now)



        return view('livewire.org-app.dashboard.index', [
            'activeActivitiesCount' => $activeActivitiesCount,
            'totalBeneficiaries' => $totalBeneficiaries,
            'totalBudget' => $totalBudget,
            'totalBudgetNis' => $totalBudgetNis,
            'pendingRequests' => $pendingRequests,
            'plannedActivities' => $plannedActivities,
            'myTasks' => $myTasks,
            'hasActivityAccess' => $hasActivityAccess,
            'activitiesBySector' => $activitiesBySector,
        ]);
    }
}
