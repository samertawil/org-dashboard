<?php

namespace App\Livewire\OrgApp\Activity;

 
use Livewire\Component;
use App\Models\Activity;
use Livewire\Attributes\Lazy;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class Show extends Component
{
    public Activity $activity;

    public function mount(Activity $activity)
    {
        $this->activity = $activity->load(['regions', 'cities', 'activityNeighbourhood', 'activityLocation', 'activityStatus', 'statusSpecificSector', 'creator','parcels', 'beneficiaries', 'workTeams',]);
    }

    public function downloadPdf()
    {
        if(Gate::denies('activity.index')){
            return abort(403,'You do not have the necessary permissions');
        }

        $pdf = Pdf::loadView('livewire.org-app.activity.pdf', ['activity' => $this->activity]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'activity-report-' . $this->activity->id . '.pdf');
    }

    public function render()
    {
        if(Gate::denies('activity.index')){
            return abort(403,'You do not have the necessary permissions');
        }
        return view('livewire.org-app.activity.show');
    }
}
