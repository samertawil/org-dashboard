<?php

namespace App\Livewire\OrgApp\ActivitySector;

use Livewire\Component;
use App\Models\Activity;
use App\Models\ActivityParcel;
use App\Models\ActivitiesSector;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

class Index extends Component
{

    #[Computed()]
    public function sectors()
    {
        return ActivitiesSector::all();
    }

    #[Computed()]
    public function activities()
    {
        // Group activities by sector_id and month-year of start_date
        return Activity::with(['parcels','regions', 'activityStatus', 'cities', 'activityNeighbourhood', 'activityLocation',])->get()->groupBy(function ($activity) {
            $date = \Carbon\Carbon::parse($activity->start_date);
            return $activity->sector_id . '_' . $date->format('m_Y');
        });
    }



    public function render()
    {

        return view('livewire.org-app.activity-sector.index', [
            'sectors' => $this->sectors,
            'groupedActivities' => $this->activities
        ]);
    }
}
