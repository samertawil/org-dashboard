<?php

namespace App\Livewire\OrgApp\TeachingGroup;

use App\Concerns\TeachingGroup\TeachingGroupTrait;
use Livewire\Component;
use App\Models\TeachingGroup;
 

class Create extends Component
{
    use TeachingGroupTrait;

    public function mount()
    {
        $this->bootTeachingGroupTrait();
        $this->name = 'Teaching Group #';
    }

    public function save()
    {
       
        $this->validate();

        TeachingGroup::create([
            'name' => ucfirst($this->name),
            'activity_id' => $this->activity_id,
            'student_groups_id' => $this->student_groups_id ?: null,
       
            'Moderator' => ucfirst($this->Moderator)?: null,
            'Moderator_phone' => $this->Moderator_phone?: null,
            'Moderator_email' => $this->Moderator_email?: null,
            'status' => $this->status ?: null,
            'activation' => $this->activation,
            'cost_usd' => $this->cost_usd,
            'cost_nis' => $this->cost_nis,
            'partner_id' => $this->partner_id ?: null,
            'notes' => $this->notes?: null,
            'created_by' => auth()->id(),
        ]);

        session()->flash('message', __('Teaching Group successfully created.'));

        return $this->redirect(route('teaching.group.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.org-app.teaching-group.create', [
            'heading' => __('Create Teaching Group'),
            'type' => 'save',
         
            'activities' => $this->activities,
            'student_groups' => $this->student_groups,
            'partners' => $this->partners,
            'statuses' => $this->statuses,
            'activations' => $this->activations,
        ]);
    }
}
