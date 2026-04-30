<?php

namespace App\Livewire\OrgApp\Maps;

use App\Models\DisplacementCamp;
use Livewire\Component;
use Livewire\Attributes\Computed;

class OperationsMap extends Component
{
    public $filterType = 'all'; // all, camps, activities
    public $isDashboard = false;

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
        return DisplacementCamp::orderBy('name')->get(['id', 'name', 'latitude', 'longitudes']);
    }

    #[Computed]
    public function activitiesList()
    {
        return \App\Models\Activity::with(['regions', 'cities'])
            ->orderBy('name')
            ->get(['id', 'name', 'latitude', 'longitudes', 'region', 'city'])
            ->map(function($a) {
                $loc = '';
                if ($a->regions) $loc .= $a->regions->region_name;
                if ($a->cities) $loc .= ($loc ? ' - ' : '') . $a->cities->city_name;
                $a->display_name = $a->name . ($loc ? " ($loc)" : "");
                return $a;
            });
    }

    public function updateActivityCoordinates($activityId, $lat, $lng)
    {
        $activity = \App\Models\Activity::find($activityId);
        if ($activity) {
            $activity->update([
                'latitude' => $lat,
                'longitudes' => $lng
            ]);
            
            // Refresh computed properties
            unset($this->allActivities);
            unset($this->activitiesList);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('Coordinates updated successfully!')
            ]);
        }
    }

    public function render()
    {
        return view('livewire.org-app.maps.operations-map')
            ->layout('layouts.app.land', ['title' => __('Operations Map')]);
    }
}
