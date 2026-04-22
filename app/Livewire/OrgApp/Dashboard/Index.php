<?php

namespace App\Livewire\OrgApp\Dashboard;

use App\Models\Activity;
use App\Reposotries\ActivityBeneficiaryRepo;
use App\Reposotries\ActivityRepo;
use App\Reposotries\EventAssigneeRepo;
use App\Reposotries\PurchaseRequisitionRepo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{

    #[Computed()]
    public function activities() {
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

    
    #[Title('Dashboard')]
    public function render()
    {
        
        // 1. KPI Cards Data
        $activeActivitiesCount = ActivityRepo::activites()->count();

        $totalBeneficiaries =  ActivityBeneficiaryRepo::beneficiaries()->sum('beneficiaries_count');

        // Budget/Expenses (Mock logic for now as budget structure might vary)
        $totalBudget = ActivityRepo::activites()->sum('cost');
        $totalBudgetNis = ActivityRepo::activites()->sum('cost_nis');

        $pendingRequests = PurchaseRequisitionRepo::purchases()->count();
      
        // 3. Recent Activity Feed
        $plannedActivities = ActivityRepo::activites()->where('start_date', '>', now())->take(5);

        // 4. My Tasks
        $myTasks = EventAssigneeRepo::eventAssignees();

        return view('livewire.org-app.dashboard.index', [
            'activeActivitiesCount' => $activeActivitiesCount,
            'totalBeneficiaries' => $totalBeneficiaries,
            'totalBudget' => $totalBudget,
            'totalBudgetNis' => $totalBudgetNis,
            'pendingRequests' => $pendingRequests,
            'plannedActivities' => $plannedActivities,
            'myTasks' => $myTasks
        ]);
    }
}
