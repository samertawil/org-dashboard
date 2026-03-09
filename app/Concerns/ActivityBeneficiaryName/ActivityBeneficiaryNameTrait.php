<?php

namespace App\Concerns\ActivityBeneficiaryName;
use App\Reposotries\displacementCampRepo;
use App\Reposotries\ActivityRepo;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Validate;

trait ActivityBeneficiaryNameTrait
{
    #[Validate('required|exists:activities,id')]
    public $activity_id = '';

    public $identity_number = '';

    #[Validate('nullable|exists:displacement_camps,id')]
    public $displacement_camps_id = '';

    #[Validate('required|string|max:255')]
    public $full_name = '';

    #[Validate('nullable|string|max:15')]
    public $phone = '';

    #[Validate('required|date')]
    public $receipt_date = '';

    #[Validate('nullable|exists:statuses,id')]
    public $receive_method = '';

    #[Validate('nullable|string|max:255')]
    public $receive_by_name = '';

    // Collections
    public $activities = [];
    public $displacementCamps = [];
    public $receiptMethods = [];

    public function bootActivityBeneficiaryNameTrait()
    {
        $this->activities = ActivityRepo::activites();
        $this->displacementCamps = displacementCampRepo::camps();
        // Since there is no specific predefined appConstant, we fetch all for now, or you can filter later if needed.
        $this->receiptMethods = StatusRepo::statuses()->where('p_id_sub', config('appConstant.receive_method')); 
    }
}
