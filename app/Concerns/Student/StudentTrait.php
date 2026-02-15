<?php

namespace App\Concerns\Student;

use App\Models\StudentGroup;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Validate;
use App\Enums\GlobalSystemConstant;

trait StudentTrait
{
    public $birth_date = '';
    public $identity_number = '';
    
    #[Validate('required|string')]
    public $full_name = '';

    #[Validate('nullable|exists:student_groups,id')]
    public $student_groups_id = '';

    #[Validate('required')]
    public $gender = ''; 

    #[Validate('required')]
    #[Validate('global_validation:status', message: 'Wrong value')]
    public $activation = GlobalSystemConstant::ACTIVE->value;

    #[Validate('nullable|exists:statuses,id')]
    public $status_id = '';

    #[Validate('nullable|string')]
    public $parent_phone = '';

    #[Validate('nullable|exists:statuses,id')]
    public $living_parent_id = '';

    #[Validate('nullable|string')]
    public $notes = '';

    public $activations = [];
    public $statuses = [];
    public $studentGroups = [];
    public $livingStatuses = [];

    


    public function bootStudentTrait()
    {
       
        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
        $this->statuses = StatusRepo::statuses();
        $this->studentGroups = StudentGroup::all();
        $this->livingStatuses = StatusRepo::statuses();
    }
}
