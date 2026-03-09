<?php

namespace App\Livewire\OrgApp\DisplacementCamps;


use App\Concerns\DisplacementCamps\DisplacementCampTrait;
use App\Models\DisplacementCamp;
use App\Reposotries\CityRepo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Edit extends Component
{
    use DisplacementCampTrait;
    public $name;
    public DisplacementCamp $displacementCamp;

    public function mount(DisplacementCamp $displacementCamp)
    {
        $this->bootDisplacementCampTrait();
        $this->displacementCamp = $displacementCamp;

        $this->name = $displacementCamp->name;
        $this->region_id = $displacementCamp->region_id;
        $this->city_id = $displacementCamp->city_id;
        $this->neighbourhood_id = $displacementCamp->neighbourhood_id;
        $this->location_id = $displacementCamp->location_id;
        $this->address_details = $displacementCamp->address_details;
        $this->longitudes = $displacementCamp->longitudes;
        $this->latitude = $displacementCamp->latitude;
        $this->number_of_families = $displacementCamp->number_of_families;
        $this->number_of_individuals = $displacementCamp->number_of_individuals;
        $this->Moderator = $displacementCamp->Moderator;
        $this->Moderator_phone = $displacementCamp->Moderator_phone;
        $this->camp_main_needs = $displacementCamp->camp_main_needs ?? [];
        $this->notes = $displacementCamp->notes;

        $this->cities =  CityRepo::cities();
        $this->neighbourhoods = $this->city_id ? \App\Reposotries\NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city_id) : collect();
        $this->locations = $this->neighbourhood_id ? \App\Reposotries\LocationRepo::locations()->where('neighbourhood_id', $this->neighbourhood_id) : collect();
    }

    public function rules() {
        return [
            'name' => 'required|string|max:255|unique:displacement_camps,name,'.$this->displacementCamp->id,
        ];
    }

    public function update()
    {
        $this->validate();
 
        DB::transaction(function () {
            $this->displacementCamp->fill([
                'name' => $this->name,
                'region_id' => $this->region_id,
                'city_id' => $this->city_id,
                'neighbourhood_id' => $this->neighbourhood_id,
                'location_id' => $this->location_id,
                'address_details' => $this->address_details,
                'longitudes' => $this->longitudes,
                'latitude' => $this->latitude,
                'number_of_families' => $this->number_of_families,
                'number_of_individuals' => $this->number_of_individuals,
                'Moderator' => $this->Moderator,
                'Moderator_phone' => $this->Moderator_phone,
                'camp_main_needs' => $this->camp_main_needs,
                'notes' => $this->notes,
            ]);

           

            if ($this->displacementCamp->isDirty()) {
          
                $this->displacementCamp->save();
              
                session()->flash('message', __('Displacement Camp updated successfully.'));
            } else {
                session()->flash('message', __('No changes were made!'));         
                session()->flash('type','warning');
            }

        });

     
        return $this->redirect(route('displacement.camps.index'), navigate: true);
    }

    public function render()
    {
        if(Gate::denies('displacement.camps.create')) { // assuming same logic as purchase request using .create for edit
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.displacement-camps.edit', [
            'heading' => __('Edit Displacement Camp'),
        ]);
    }
}
