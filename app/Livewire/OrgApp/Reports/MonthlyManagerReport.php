<?php

namespace App\Livewire\OrgApp\Reports;

use App\Models\Activity;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class MonthlyManagerReport extends Component
{
    public $selectedYear;

    public function mount()
    {
        $this->selectedYear = date('Y');
    }

    public function getReportDataProperty()
    {
        // Fetch all active activities for the selected year
        $activities = Activity::query()
            ->where('activation', 1)
            ->whereIn('status', [25, 26, 27]) // Planned, In Progress, Completed
            ->whereYear('start_date', $this->selectedYear)
            ->with(['statusSpecificSector', 'parcels.parcelType'])
            ->get();

        // Group by Month
        return $activities->groupBy(fn($a) => Carbon::parse($a->start_date)->format('m'))
            ->sortKeys()
            ->map(function ($activitiesInMonth, $monthNum) {
                $monthName = Carbon::create()->month((int)$monthNum)->translatedFormat('F');
                
                // Group by Sector
                $sectors = $activitiesInMonth->groupBy('sector_id')->map(function ($sectorActivities) {
                    return [
                        'name' => $sectorActivities->first()->statusSpecificSector->status_name ?? __('Unknown'),
                        'count' => $sectorActivities->count(),
                        'cost' => $sectorActivities->sum('cost'),
                        'cost_nis' => $sectorActivities->sum('cost_nis'),
                    ];
                });

                // Get Parcels
                $parcels = $activitiesInMonth->flatMap->parcels->groupBy('parcel_type')->map(function ($parcelsOfType) {
                    return [
                        'name' => $parcelsOfType->first()->parcelType->status_name ?? __('Unknown'),
                        'count' => $parcelsOfType->sum('distributed_parcels_count'),
                    ];
                });

                return [
                    'month_name' => $monthName,
                    'total_activities' => $activitiesInMonth->count(),
                    'total_cost' => $activitiesInMonth->sum('cost'),
                    'total_cost_nis' => $activitiesInMonth->sum('cost_nis'),
                    'sectors' => $sectors,
                    'parcels' => $parcels,
                ];
            });
    }

    public function render()
    {
       if (Gate::denies('manager.reports.all') ) {
            abort(403, 'You do not have the necessary permissions.');
        }  
        return view('livewire.org-app.reports.monthly-manager-report', [
            'reportData' => $this->reportData,
        ]);
    }
}
