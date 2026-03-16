<?php

namespace App\Livewire\OrgApp\CampsResidents;

use App\Concerns\CampsResidents\CampsResidentTrait;
use App\Models\displacementCampResident;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    use CampsResidentTrait;

    #[Validate('required|integer|min_digits:9|max_digits:9|unique:displacement_camp_residents,identity_number')]
    public $identity_number = '';


    public function save()
    {
        $this->validate();

        displacementCampResident::create([
            'displacement_camp_id' => $this->displacement_camp_id,
            'resident_type' => $this->resident_type,
            'identity_number' => $this->identity_number,
            'full_name' => $this->full_name,
            'birth_date' => $this->birth_date ?: null,
            'phone' => $this->phone,
            'gender' => $this->gender ?: null,
            'activation' => $this->activation,
        ]);

        session()->flash('message', __('Displacement Camp Resident successfully created.'));

        return $this->redirect(route('camps.residents.index'), navigate: true);
    }

    public function render()
    {     
        // TODO: Ensure valid gates exist or remove conditionally once configured
        if(Gate::denies('displacement.camps.create')) {
            abort(403, 'You do not have the necessary permissions.');
        } 

        return view('livewire.org-app.camps-residents.create', [
            'heading' => __('Create Camp Resident'),
            'type' => 'save',
        ]);
    }
}
