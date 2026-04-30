<?php

namespace App\Livewire\OrgApp\Maps;

use App\Models\DisplacementCamp;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Computed;

class OperationsMap extends Component
{
    public $filterType = 'all'; // all, camps, activities

    #[Computed]
    public function allCamps()
    {
        if ($this->filterType === 'activities') {
            return collect();
        }
        
        return DisplacementCamp::whereNotNull('latitude')
            ->whereNotNull('longitudes')
            ->get(['id', 'name', 'latitude', 'longitudes', 'address_details', 'Moderator', 'Moderator_phone']);
    }

    #[Computed]
    public function allActivities()
    {
        if ($this->filterType === 'camps') {
            return collect();
        }

        return \App\Models\Activity::whereNotNull('latitude')
            ->whereNotNull('longitudes')
            ->get(['id', 'name', 'latitude', 'longitudes', 'address_details', 'start_date', 'sector_id']);
    }

 
    #[Computed]
    public function campsList()
    {
        return \App\Models\DisplacementCamp::orderBy('name')->get(['id', 'name', 'latitude', 'longitudes'])->toArray();
    }

    #[Computed]
    public function activitiesList()
    {
        return \App\Models\Activity::with(['regions', 'cities'])
            ->orderBy('name')
            ->get(['id', 'name', 'latitude', 'longitudes', 'region', 'city'])
            ->map(function($activity) {
                $location = '';
                if ($activity->regions) $location .= $activity->regions->region_name;
                if ($activity->cities) $location .= ($location ? ' - ' : '') . $activity->cities->city_name;
                
                return [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'latitude' => $activity->latitude,
                    'longitudes' => $activity->longitudes,
                    'display_name' => $activity->name . ($location ? " ($location)" : "")
                ];
            })->toArray();
    }

    public function render()
    {
        return view('livewire.org-app.maps.operations-map')
            ->layout('layouts.app.land', ['title' => __('Operations Map')]);
    }
}
