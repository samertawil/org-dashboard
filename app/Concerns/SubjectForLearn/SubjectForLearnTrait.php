<?php

namespace App\Concerns\SubjectForLearn;

use App\Reposotries\StatusRepo;
use Livewire\Attributes\Validate;
use App\Enums\GlobalSystemConstant;

trait SubjectForLearnTrait
{
    #[Validate('nullable|exists:statuses,id')]
    public $type_id = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|integer')]
    public $activation = GlobalSystemConstant::ACTIVE->value;

    public $statuses = [];
    public $activations = [];

    public function bootSubjectForLearnTrait()
    {
        $this->statuses = StatusRepo::statuses()->where('p_id_sub',63);
        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
    }
}
