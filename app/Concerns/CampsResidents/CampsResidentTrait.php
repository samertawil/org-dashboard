<?php

namespace App\Concerns\CampsResidents;

use App\Models\displacementCamp;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Validate;
use App\Enums\GlobalSystemConstant;

trait CampsResidentTrait
{
    #[Validate('nullable|date')]
    public $birth_date = '';
    

    #[Validate('required|string|max:255')]
    public $full_name = '';

    #[Validate('required|exists:displacement_camps,id')]
    public $displacement_camp_id = '';

    #[Validate('nullable|string|max:15')]
    public $phone = '';

    #[Validate('nullable')] 
    #[Validate('global_validation:gender', message: 'Wrong value')]
    public $gender = '';

    #[Validate('required')]
    #[Validate('global_validation:status', message: 'Wrong value')]
    public $activation = GlobalSystemConstant::ACTIVE->value;

    #[Validate('required|exists:statuses,id')]
    public $resident_type = '';

    // Collections
    public $activations = [];
    public $genderData = [];
    public $residentTypes = [];
    public $displacementCamps = [];

    public function bootCampsResidentTrait()
    {
        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
        $this->genderData = GlobalSystemConstant::options()->where('type', 'gender');
        
        // P_id_sub assumes 1 is standard active/inactive, or the specific category for beneficiaries
        // Looking at the migration `Beneficiaries Types` implies it's a specific parent ID from the statuses table.
        // Assuming we just fetch all available or a specific set. For now fetching all or specific 'resident_type' equivalents.
        // Let's assume StatusRepo::statuses() and filter if necessary in the view.
        $this->residentTypes = StatusRepo::statuses()->where('p_id_sub',config('appConstant.beneficiaries_types')); 
        
        $this->displacementCamps = displacementCamp::all();
    }
}
