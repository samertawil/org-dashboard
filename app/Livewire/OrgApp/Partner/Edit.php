<?php

namespace App\Livewire\OrgApp\Partner;

use Livewire\Component;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Computed;
use App\Models\PartnerInstitution;
use App\Enums\GlobalSystemConstant;
use Illuminate\Support\Facades\Gate;

class Edit extends Component
{
    public PartnerInstitution $partner;

    public $name = '';
    public $manager_name = '';
    public $type_id = '';
    public $location = '';
    public $phone = '';
    public $email = '';
    public $website = '';
    public $description = '';
    public $activation = GlobalSystemConstant::ACTIVE->value;

    public $activations = [];

    public function rules()
    {
        return [
            'name' => 'required|string|unique:partner_institutions,name,' . $this->partner->id,
            'manager_name' => 'nullable|string',
            'type_id' => 'nullable|exists:statuses,id',
            'location' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|string',
            'description' => 'nullable|string',
            'activation' => 'required|integer',
        ];
    }

    public function mount(PartnerInstitution $partner)
    {
        $this->partner = $partner;
        $this->name = $partner->name;
        $this->manager_name = $partner->manager_name;
        $this->type_id = $partner->type_id;
        $this->location = $partner->location;
        $this->phone = $partner->phone;
        $this->email = $partner->email;
        $this->website = $partner->website;
        $this->description = $partner->description;
        $this->activation = $partner->activation;

        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
    }

    public function update()
    {
        $this->validate();

        $this->partner->update([
            'name' => $this->name,
            'manager_name' => $this->manager_name,
            'type_id' => $this->type_id ?: null,
            'location' => $this->location,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'description' => $this->description,
            'activation' => $this->activation,
        ]);

        session()->flash('message', __('Partner Institution successfully updated.'));

        return $this->redirect(route('partner.index'), navigate: true);
    }

    
    public function updated($property, $value)
    {
        if ($property === 'location' || $property === 'name') {
            $this->$property = ucfirst($value);
        }
    }

    #[Computed()]
    public function locations()
    {
        return PartnerInstitution::select('location')->distinct()->get();
    }
    
    public function render()
    {
        // if (Gate::denies('partner_institution.edit')) { abort(403); }

        return view('livewire.org-app.partner.edit', [
            'heading' => __('Edit Partner Institution'),
            'type' => 'update',
            'activations' => $this->activations,
            'partnerTypes' => StatusRepo::statuses(),
        ]);
    }
}
