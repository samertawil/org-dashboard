<?php

namespace App\Livewire\OrgApp\Activity;

use App\Models\Project;
use Livewire\Component;
use App\Models\Activity;
use Livewire\Attributes\Lazy;

class Show extends Component
{
    public Activity $activity;
    public $readyToLoad = false;

    public function mount(Activity $activity)
    {
        $this->activity =

        $activity->load(['regions', 'cities', 'activityNeighbourhood', 'activityLocation', 'activityStatus', 'statusSpecificSector', 'creator']);
    }
    #[Lazy]
    public function render()
    {
        if ($this->readyToLoad) {
            $this->project->load(['regions', 'cities', 'activityNeighbourhood', 'activityLocation', 'activityStatus', 'statusSpecificSector', 'creator']);
        }
        return view('livewire.org-app.activity.show', [
            'activity' => $this->readyToLoad ? $this->project : null
        ]);
    }
}
