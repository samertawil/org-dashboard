<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Activity;
use App\Models\Status;
use App\Reposotries\StatusRepo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class FinancialSummary extends Component
{
    public $dateFrom='2023-10-30';
    public $dateTo;
    public $selectedSector;

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
            ->where('activation', 1)
            ->whereBetween('start_date', [$this->dateFrom, $this->dateTo]);

        if ($this->selectedSector) {
            $query->where('sector_id', $this->selectedSector);
        }

        $activities = $query->with('statusSpecificSector')->get();

        // 2. KPIs
        $totalCostUSD = ($activities->sum('cost'));
        $totalCostNIS = $activities->sum('cost_nis');
        $avgCost = $activities->avg('cost') ?? 0;
        
        // Cost per Beneficiary (Approximate - sums all beneficiaries across activities)
        // Note: This is a simple aggregation. Real cost/beneficiary might need more granular calculation per activity.
        $totalBeneficiaries = $activities->sum(function($activity) {
            return $activity->beneficiaries->sum('beneficiaries_count');
        });
        
        $costPerBeneficiary = $totalBeneficiaries > 0 ? ($totalCostUSD / $totalBeneficiaries) : 0;


        // 3. Chart Data
        
        // Chart 1: Cost by Sector
        $sectorCosts = $activities->groupBy(fn($a) => $a->statusSpecificSector->status_name ?? 'Unknown')
            ->map(fn($group) => round($group->sum('cost'))); // Sum USD cost
            
        $sectorChartData = [
            'labels' => $sectorCosts->keys()->toArray(),
            'series' => $sectorCosts->values()->toArray(),
        ];

        // Chart 2: Monthly Spending
        // Group by month and sum cost
        $monthlySpending = $activities->groupBy(fn($a) => \Carbon\Carbon::parse($a->start_date)->format('M Y'))
            ->map(fn($group) => round($group->sum('cost')));
         $monthlyChartData = [
             'labels' => $monthlySpending->keys()->toArray(),
             'series' => $monthlySpending->values()->toArray()
        ];


        return view('livewire.org-app.reports.financial-summary', [
            'sectors' => Status::where('p_id_sub', 13)->get(), // Assuming System Constant for Sectors is under parent 13, inferred from Context or check DB if needed. 
            // Better to fetch sectors dynamically if possible. Let's use unique sectors from activities or a known repo if available.
            // FormTrait had 'sector_id' validation but didn't show source clearly.
            // Let's use filtering from existing activities for safety or just Status::all() if list is small, 
            // OR checks Activity::groupBy('sector_id') -> with('statusSpecificSector')
            'kpis' => compact('totalCostUSD', 'totalCostNIS', 'avgCost', 'totalBeneficiaries', 'costPerBeneficiary'),
            'sectorChartData' => $sectorChartData,
            'monthlyChartData' => $monthlyChartData,
            'availableSectors' => StatusRepo::statuses()->where('p_id_sub', config('appConstant.sectors')) 

        ]);
    }
}
