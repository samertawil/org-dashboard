<?php

namespace App\Livewire\OrgApp\TeachingGroup;

use App\Concerns\TeachingGroup\TeachingGroupTrait;
use Livewire\Component;
use App\Models\TeachingGroup;

class Edit extends Component
{
    use TeachingGroupTrait;

    public TeachingGroup $group;

    public function rules() 
    {
        return [
            'name' => 'required|string|unique:teaching_groups,name,' . $this->group->id,
        ];
    }

    public function mount(TeachingGroup $group)
    {
        $this->group = $group;
        $this->bootTeachingGroupTrait();

        $this->name = $group->name;
        $this->activity_id = $group->activity_id;
        $this->student_groups_id = $group->student_groups_id;
       
        $this->Moderator = $group->Moderator;
        $this->Moderator_phone = $group->Moderator_phone;
        $this->Moderator_email = $group->Moderator_email;
        $this->status = $group->status; 
        $this->activation = $group->activation;
        $this->cost_usd = $group->cost_usd;
        $this->cost_nis = $group->cost_nis;
        $this->partner_id = $group->partner_id;
        $this->notes = $group->notes;
    }

    public function save()
    {
        $this->validate();

        $this->group->update([
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
            'notes' => $this->notes,
            'updated_by' => auth()->id(),
        ]);

        session()->flash('message', __('Teaching Group successfully updated.'));

        return $this->redirect(route('teaching.group.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.org-app.teaching-group.edit', [
            'heading' => __('Edit Teaching Group'),
            'type' => 'save',

            'activities' => $this->activities,
            'student_groups' => $this->student_groups,
            'partners' => $this->partners,
            'statuses' => $this->statuses,
            'activations' => $this->activations,
        ]);
    }
}
