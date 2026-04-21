<?php

namespace App\Concerns\Employee;

use App\Models\User;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Enums\GlobalSystemConstant;
use App\Reposotries\DepartmentRepo;
use App\Reposotries\PartnersRepo;
use App\Rules\GlobalValidation;

trait EmployeeTrait
{

 
    public $gender = '';

    #[Validate('nullable|date')]
    public $date_of_birth = '';

    #[Validate('nullable|exists:statuses,id')]
    public $marital_status = '';

    #[Validate('nullable', 'regex:/^\d{10,15}$/')]
    public $phone = '';

    #[Validate('nullable|exists:statuses,id')]
    public $regions = '';

    #[Validate('nullable|exists:departments,id')]
    public $department_id = '';

    #[Validate('nullable|exists:statuses,id')]
    public $type_of_employee_hire = '';

    #[Validate('nullable|date')]
    public $date_of_joining = '';

    #[Validate('nullable|exists:statuses,id')]
    public $position = '';

    #[Validate('nullable|exists:users,id')]
    public $user_id = '';

    #[Validate('nullable|exists:partner_institutions,id')]
    public $employee_in_partner_id = null;

    
    #[Validate('nullable|exists:statuses,id')]
    public $job_title = null;
    
    
  
    public $activation = GlobalSystemConstant::ACTIVE->value;

    public $genders = [];
    public $activations = [];
    public $departments = [];
    public $users = [];


    public function bootEmployeeTrait(): void
    {
        $this->genders = GlobalSystemConstant::options()->where('type', 'gender');
        $this->activations = GlobalSystemConstant::options()->where('type', 'status');   
        $this->departments = DepartmentRepo::departments();
        $this->users = User::where('activation', GlobalSystemConstant::ACTIVE->value)->get(); 
    }

    
    public function rules() {
        return [
            'activation' => [
                'required',
                new GlobalValidation('status'),
            ],

            'gender' => [
               'required',
                new GlobalValidation('gender'),
            ],
        ];
    }
 

    #[Computed()]
    public function allStatuses()
    {
        return StatusRepo::statuses();
    }

    #[Computed()]
    public function allPartners()
    {
        return PartnersRepo::partners();
    }

    
}
