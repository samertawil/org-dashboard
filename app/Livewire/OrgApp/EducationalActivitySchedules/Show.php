<?php

namespace App\Livewire\OrgApp\EducationalActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;


class Show extends Component
{
    public ActivitySchedule $schedule;

    public function mount(ActivitySchedule $schedule)
    {

        Gate::authorize('view', $schedule);

        $this->schedule = $schedule->load([
            'activity',
            'group',
            'activityDomain',
            'periodGroups',
            'employee',
            'createdBy',
            'updatedBy',
        ]);
    }

    public function render()
    {

        return view('livewire.org-app.educational-activity-schedules.show', [
            'heading' => __('Schedule Details'),
        ]);
    }
}
