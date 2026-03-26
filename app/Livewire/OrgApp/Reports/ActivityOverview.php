<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Activity;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;


class ActivityOverview extends Component
{
    public $dateFrom='2023-10-30';
    public $dateTo;
    public $selectedRegion;
    public $selectedStatus;

    public function mount()
    {
        // $this->dateFrom = now()->startOfYear()->format('Y-m-d');
        $this->dateTo = now()->endOfYear()->format('Y-m-d');
    }

    public function render()
    {
           
        if (Gate::denies('reports.all')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        // 1. Base Query
        $query = Activity::query()
            ->select('id', 'name', 'status', 'region', 'cost', 'cost_nis', 'start_date', 'end_date', 'activation')
            ->with(['regions:id,region_name', 'activityStatus', 'attachments'])
            ->where('activation', 1)
            ->whereBetween('start_date', [$this->dateFrom, $this->dateTo]);

        if ($this->selectedRegion) {
            $query->where('region', $this->selectedRegion);
        }

        // if ($this->selectedStatus) {
        //      // Logic to filter by virtual status or db status
        //      // For now, let's filter by DB status if integer, or handle virtual logic later if needed
        //      // Simple integer match for now
        //      if(is_numeric($this->selectedStatus)){
        //          $query->where('status', $this->selectedStatus);
        //      }
        // }

        $activities = $query->get();
       
        // 2. KPIs
        $totalActivities = $activities->count();
        $completedActivities = $activities->filter(fn($a) => $a->status_info['raw_name'] === 'Completed')->count();

        $ongoingActivities = $activities->filter(fn($a) => $a->status_info['raw_name'] === 'In Progress')->count();
        $PlannedActivities = $activities->filter(fn($a) => $a->status_info['raw_name'] === 'Planned')->count();

        $OnHoldActivities = $activities->filter(fn($a) => $a->status_info['raw_name'] === 'On Hold')->count();

        $totalBudget = $activities->sum('cost');

        // 3. Chart Data Preparation

        // Chart 1: Status Distribution
        $statusCounts = $activities->groupBy(fn($a) => $a->status_info['name'])->map->count();
        $statusChartData = [
            'labels' => $statusCounts->keys()->toArray(),
            'series' => $statusCounts->values()->toArray(),
        ];

        // Chart 2: Geographic Spread (Top 5 Regions)
        $regionCounts = $activities->groupBy(fn($a) => $a->regions->region_name ?? 'Unknown')
            ->map->count()
            ->sortDesc()
            ->take(5);
        $geoChartData = [
            'labels' => $regionCounts->keys()->toArray(),
            'series' => $regionCounts->values()->toArray(),
        ];

        // Chart 3: Monthly Progress
        $monthlyCounts = $activities->groupBy(fn($a) => \Carbon\Carbon::parse($a->start_date)->format('M Y'))
             ->map->count();
        
        // Ensure chronological order could be complex, for simplified V1 we just take the grouping
        $monthlyChartData = [
             'labels' => $monthlyCounts->keys()->toArray(),
             'series' => $monthlyCounts->values()->toArray()
        ];


        return view('livewire.org-app.reports.activity-overview', [
            'activities' => $activities,
            'regions' => \App\Reposotries\RegionRepo::regions(),
            'kpis' => compact('totalActivities', 'completedActivities', 'ongoingActivities', 'totalBudget', 'PlannedActivities','OnHoldActivities'),
            'statusChartData' => $statusChartData,
            'geoChartData' => $geoChartData,
            'monthlyChartData' => $monthlyChartData
        ]);
    }
}
