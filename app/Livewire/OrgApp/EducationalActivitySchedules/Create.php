<?php

namespace App\Livewire\OrgApp\EducationalActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Concerns\EducationalActivitySchedule\FormTrait;

class Create extends Component
{
    use FormTrait;

    public function save()
    {
        $this->validate();

        ActivitySchedule::create([
          
            'group_id'                    => $this->group_id ?: null,
            'educational_activity_domain' => $this->educational_activity_domain ?: null,
            'target_category'             => $this->target_category ?: null,
            'activity_name'               => $this->activity_name,
            'activity_description'        => $this->activity_description ?: null,
            'period_start'                => $this->period_start,
            'period_end'                  => $this->period_end,
            'educational_period_groups'   => $this->educational_period_groups ?: null,
            'notes'                       => $this->notes ?: null,
            'sort_order'                  => $this->sort_order ?? 0,
            'activation'                  => $this->activation,
            'employee_id'                 => $this->employee_id ?: null,
            'created_by'                  => Auth::id(),
            'updated_by'                  => Auth::id(),
        ]);

        session()->flash('message', __('Schedule successfully created.'));

        return $this->redirect(route('educational-activity-schedules.index'), navigate: true);
    }

    public function render()
    {
        if (Gate::denies('educational-activity-schedules.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        return view('livewire.org-app.educational-activity-schedules.create', [
            'heading' => __('Create Educational Activity Schedule'),
            'type'    => 'save',
        ]);
    }
}
