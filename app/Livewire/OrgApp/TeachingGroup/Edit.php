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
        $this->region_id = $group->region_id;
        $this->updatedRegionId(); 
        $this->city_id = $group->city_id;
        $this->updatedCityId(); 
        $this->neighbourhood_id = $group->neighbourhood_id;
        $this->location_id = $group->location_id;
        $this->address_details = $group->address_details;
        $this->start_date = $group->start_date;
        $this->end_date = $group->end_date;
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
