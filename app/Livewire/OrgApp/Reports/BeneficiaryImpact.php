<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Activity;
use App\Models\Region;
use App\Models\Status; // Assuming beneficiary types are stored in statuses or a similar lookup, based on ActivityBeneficiary code check.
use Illuminate\Support\Facades\DB;
// Actually, ActivityBeneficiary has 'beneficiary_type' which relates to Status.
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class BeneficiaryImpact extends Component
{
    public $dateFrom='2023-10-30';
    public $dateTo;
    public $selectedRegion;

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
        // 1. Base Query for Activities
        $query = Activity::query()
            ->where('activation', 1)
            ->whereBetween('start_date', [$this->dateFrom, $this->dateTo]);

        if ($this->selectedRegion) {
            $query->where('region', $this->selectedRegion);
        }

        $activities = $query->with(['beneficiaries.beneficiaryType', 'regions'])->get();

        // 2. KPIs
        $totalBeneficiaries = $activities->sum(fn($a) => $a->beneficiaries->sum('beneficiaries_count'));
        
        // Count distinct activities impacting beneficiaries
        $activitiesWithBeneficiaries = $activities->filter(fn($a) => $a->beneficiaries->count() > 0)->count();
        
        // Calculate average beneficiaries per activity (for those that have any)
        $avgBeneficiariesPerActivity = $activitiesWithBeneficiaries > 0 
            ? round($totalBeneficiaries / $activitiesWithBeneficiaries) 
            : 0;

        // 3. Chart Data
        
        // Chart 1: Beneficiaries by Type
        // We need to flatten the beneficiaries and group by their type name
        $beneficiariesByType = $activities->pluck('beneficiaries')->flatten()
            ->groupBy(fn($b) => $b->beneficiaryType->status_name ?? 'Unknown')
            ->map->sum('beneficiaries_count');

        $typeChartData = [
            'labels' => $beneficiariesByType->keys()->toArray(),
            'series' => $beneficiariesByType->values()->toArray(),
        ];

        // Chart 2: Beneficiaries by Region (Impact Spread)
        // Group activities by region and sum their beneficiaries
        $beneficiariesByRegion = $activities->groupBy(fn($a) => $a->regions->region_name ?? 'Unknown')
             ->map->sum(fn($a) => $a->beneficiaries->sum('beneficiaries_count'));

        $regionChartData = [
            'labels' => $beneficiariesByRegion->keys()->toArray(),
            'series' => $beneficiariesByRegion->values()->toArray(),
        ];

        // Chart 3: Monthly Beneficiary Reach
        // Group activities by month and sum beneficiaries
        // Note: usage of start_date implies when the impact "started".
        $monthlyReach = $activities->groupBy(fn($a) => \Carbon\Carbon::parse($a->start_date)->format('M Y'))
            ->map->sum(fn($a) => $a->beneficiaries->sum('beneficiaries_count'));

        $monthlyChartData = [
             'labels' => $monthlyReach->keys()->toArray(),
             'series' => $monthlyReach->values()->toArray()
        ];


        return view('livewire.org-app.reports.beneficiary-impact', [
            'regions' => Region::all(),
            'kpis' => compact('totalBeneficiaries', 'activitiesWithBeneficiaries', 'avgBeneficiariesPerActivity'),
            'typeChartData' => $typeChartData,
            'regionChartData' => $regionChartData,
            'monthlyChartData' => $monthlyChartData
        ]);
    }
}
