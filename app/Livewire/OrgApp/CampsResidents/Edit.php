<?php

namespace App\Livewire\OrgApp\CampsResidents;

use App\Concerns\CampsResidents\CampsResidentTrait;
use App\Models\displacementCampResident;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Edit extends Component
{
    use CampsResidentTrait;

    public $identity_number = '';

    public displacementCampResident $campsResident;

    public function mount(displacementCampResident $campsResident)
    {
        $this->campsResident = $campsResident;
        $this->fill([
            'displacement_camp_id' => $campsResident->displacement_camp_id,
            'resident_type' => $campsResident->resident_type,
            'identity_number' => $campsResident->identity_number,
            'full_name' => $campsResident->full_name,
            'birth_date' => $campsResident->birth_date,
            'phone' => $campsResident->phone,
            'gender' => $campsResident->gender,
            'activation' => $campsResident->activation,
        ]);
    }

    public function rules()
    {
        return [
            'identity_number' => 'required|integer|min_digits:9|max_digits:9|unique:displacement_camp_residents,identity_number,' . $this->campsResident->id,
        ];
    }
    public function update()
    {
        $this->validate();

        $this->campsResident->fill([
            'displacement_camp_id' => $this->displacement_camp_id,
            'resident_type' => $this->resident_type,
            'identity_number' => $this->identity_number,
            'full_name' => $this->full_name,
            'birth_date' => $this->birth_date ?: null,
            'phone' => $this->phone,
            'gender' => $this->gender ?: null,
            'activation' => $this->activation,
        ]);


        if ($this->campsResident->isDirty()) {

            $this->campsResident->save();
            session()->flash('type','success');
            session()->flash('message', __('Displacement Camp Resident successfully updated.'));
        } else {
            session()->flash('type','warning');
            session()->flash('message', __('No changes were made!'));     
        }


        return $this->redirect(route('camps.residents.index'), navigate: true);
    }

    public function render()
    {
        if (Gate::denies('displacement.camps.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        return view('livewire.org-app.camps-residents.edit', [
            'heading' => __('Edit Camp Resident'),
            'type' => 'update',
        ]);
    }
}
