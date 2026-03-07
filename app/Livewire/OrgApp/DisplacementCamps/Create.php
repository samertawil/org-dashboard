<?php

namespace App\Livewire\OrgApp\DisplacementCamps;


use App\Concerns\DisplacementCamps\DisplacementCampTrait;
use App\Models\DisplacementCamp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    use DisplacementCampTrait;

    #[Validate('required|string|max:255|unique:displacement_camps,name')]
    public $name;

    public function mount()
    {
        $this->bootDisplacementCampTrait();
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            DisplacementCamp::create([
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
                'attchments' => [], // Start empty
            ]);

            DB::commit();

            session()->flash('message', __('Displacement Camp created successfully.'));
            return $this->redirect(route('displacement.camps.index'), navigate: true);


        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __('An error occurred while creating the Displacement Camp: ') . $e->getMessage());
            return redirect()->back();
        }


      
    }

    #[Title('Displacement Camp')]
    public function render()
    {
        if (Gate::denies('displacement.camps.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.displacement-camps.create', [
            'heading' => __('Create Displacement Camp'),
           

        ]);
    }
}
