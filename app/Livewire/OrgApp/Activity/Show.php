<?php

namespace App\Livewire\OrgApp\Activity;

 
use Livewire\Component;
use App\Models\Activity;
use Livewire\Attributes\Lazy;
use Illuminate\Support\Facades\Gate;

#[Lazy]
class Show extends Component
{
    public Activity $activity;

    public function mount(Activity $activity)
    {
        $this->activity = $activity->load(['regions', 'cities', 'activityNeighbourhood', 'activityLocation', 'activityStatus', 'statusSpecificSector', 'creator','parcels', 'beneficiaries', 'workTeams']);
    }

    public function render()
    {
        if(Gate::denies('activity.index')){
            return abort(403,'You do not have the necessary permissions');
        }
        return view('livewire.org-app.activity.show');
    }
}
