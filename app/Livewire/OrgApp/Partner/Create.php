<?php

namespace App\Livewire\OrgApp\Partner;

use Livewire\Component;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Models\PartnerInstitution;
use App\Enums\GlobalSystemConstant;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    #[Validate('required|string|unique:partner_institutions,name')]
    public $name = '';

    #[Validate('nullable|string')]
    public $manager_name = '';

    #[Validate('nullable|exists:statuses,id')]
    public $type_id = ''; // Changed to empty string to avoid null issues in select placeholders

    #[Validate('nullable|string')]
    public $location = '';

    #[Validate('nullable|string')]
    public $phone = '';

    #[Validate('nullable|email')]
    public $email = '';

    #[Validate('nullable|string')]
    public $website = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|integer')]
    public $activation = GlobalSystemConstant::ACTIVE->value;

    public $activations = [];

    public function mount()
    {
        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
    }

    public function save()
    {
        $this->validate();

        PartnerInstitution::create([
            'name' => ucfirst($this->name),
            'manager_name' => ucfirst($this->manager_name),
            'type_id' => $this->type_id ?: null,
            'location' => ($this->location),
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'description' => $this->description,
            'activation' => $this->activation,
        ]);

        

        session()->flash('message', __('Partner Institution successfully created.'));

        return $this->redirect(route('partner.index'), navigate: true);
    }

    #[Computed()]
    public function locations()
    {
        return PartnerInstitution::select('location')->distinct()->get();
    }
    
    public function updated($property, $value)
    {
        if ($property === 'location' || $property === 'name') {
            $this->$property = ucfirst($value);
        }
    }

    public function render()
    {
        if (Gate::denies('partner.create')) 
        { 
            abort(403, 'You do not have the necessary permissions');
        }

        return view('livewire.org-app.partner.create', [
            'heading' => __('Create Partner Institution'),
            'type' => 'save',
            'activations' => $this->activations,
            'partnerTypes' => StatusRepo::statuses()->where('p_id_sub', config('appConstant.partner_institutions'))  ,
        ]);
    }
}
