<?php

namespace App\Livewire\OrgApp\ActivitySector;

use Livewire\Component;
use App\Models\Activity;
use App\Models\ActivityReport;
use App\Models\ActivitiesSector;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    public $search = '';
    public $selectedSectorId = null;
    public $selectedSectorDate = null;

    public function mount()
    {
        $firstSector = ActivitiesSector::latest('activites_date')->first();
        if ($firstSector) {
            $this->selectedSectorId = $firstSector->sector_id;
            $this->selectedSectorDate = $firstSector->activites_date;
        }
         
    }

    public $selectedactivityIdForShowModal;

    public function openShowModal($projectId)
    {
        $this->selectedactivityIdForShowModal = $projectId;
        $this->dispatch('open-modal', name: 'activity-show-' . $projectId);
    }

    public function closeShowModal()
    {
        $this->selectedactivityIdForShowModal = null;
    }

    #[Computed()]
    public function sectors()
    {
        return ActivitiesSector::query()
            ->where(function ($query) {
                $query->where('sector_name', 'like', '%' . $this->search . '%')
                      ->orWhere('activites_date', 'like', '%' . $this->search . '%');
            })
            ->latest('activites_date') // Order by latest
            ->get();
    }

    #[Computed()]
    public function selectedSector()
    {
        return ActivitiesSector::where('sector_id', $this->selectedSectorId)
            ->where('activites_date', $this->selectedSectorDate)
            ->first();
    }

    #[Computed()]
    public function activities()
    {
        if (!$this->selectedSectorId || !$this->selectedSectorDate) {
            return collect();
        }

        // Parse the m/Y date from the view/model
        try {
            $date = \Carbon\Carbon::createFromFormat('!m/Y', $this->selectedSectorDate);
            $month = $date->month;
            $year = $date->year;
        } catch (\Exception $e) {
            return collect();
        }

        return Activity::where('sector_id', $this->selectedSectorId)
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->with(['parcels.parcelType', 'regions', 'activityStatus', 'cities', 'activityNeighbourhood', 'activityLocation'])
            ->latest('start_date')
            ->get();
    }

    public function selectSector($id, $date)
    {
        $this->selectedSectorId = $id;
        $this->selectedSectorDate = $date;
    }

    public function formatDate($date)
    {
        if (!$date) return '-';
        try {
            return \Carbon\Carbon::createFromFormat('!m/Y', $date)->format('F Y');
        } catch (\Exception $e) {
            try {
                return \Carbon\Carbon::parse($date)->format('F Y');
            } catch (\Exception $e) {
                return $date;
            }
        }
    }

    public function render()
    {
        
        if(Gate::denies('activity.index')){
            return abort(403,'You do not have the necessary permissions');
        }

        $activityReport = collect();

        if ($this->selectedSectorId && $this->selectedSectorDate) {
            try {
                $date = \Carbon\Carbon::createFromFormat('!m/Y', $this->selectedSectorDate);
                $month = $date->month;
                $year = $date->year;

                $activityReport = ActivityReport::report()
                    ->where('sector_id', $this->selectedSectorId)
                    ->whereMonth('start_date', $month)
                    ->whereYear('start_date', $year)
                    ->get();
            } catch (\Exception $e) {
                // handle error or leave empty
            }
        }

        return view('livewire.org-app.activity-sector.index',[
            'activityReport' => $activityReport,
        ]);
    }
}
