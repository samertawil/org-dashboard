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
            'region_id' => $this->region_id ?: null,
            'city_id' => $this->city_id ?: null,
            'neighbourhood_id' => $this->neighbourhood_id ?: null,
            'location_id' => $this->location_id ?: null,
            'address_details' => $this->address_details,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'Moderator' => ucfirst($this->Moderator),
            'Moderator_phone' => $this->Moderator_phone,
            'Moderator_email' => $this->Moderator_email,
            'status' => $this->status ?: null,
            'activation' => $this->activation,
            'cost_usd' => $this->cost_usd,
            'cost_nis' => $this->cost_nis,
            'partner_id' => $this->partner_id ?: null,
            'notes' => $this->notes,
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
            'regions' => $this->regions,
            'cities' => $this->cities,
            'neighbourhoods' => $this->neighbourhoods,
            'locations' => $this->locations,
            'activities' => $this->activities,
            'student_groups' => $this->student_groups,
            'partners' => $this->partners,
            'statuses' => $this->statuses,
            'activations' => $this->activations,
        ]);
    }
}
