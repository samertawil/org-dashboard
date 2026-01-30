<?php

namespace App\Livewire\OrgApp\Employee;

use App\Enums\GlobalSystemConstant;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Status;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|string|unique:employees,employee_number')]
    public $employee_number = '';

    #[Validate('required|string|unique:employees,full_name')]
    public $full_name = '';

    #[Validate('required|integer')]
    public $gender = ''; // Default to MALE

    #[Validate('nullable|date')]
    public $date_of_birth = '';

    #[Validate('nullable|exists:statuses,id')]
    public $marital_status = '';

    #[Validate('nullable|string')]
    public $phone = '';

    #[Validate('nullable|exists:statuses,id')]
    public $regions = '';

    #[Validate('nullable|email|unique:employees,email')]
    public $email = '';

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

    #[Validate('required|integer')]
    public $activation = 1; // Default to ACTIVE

    public function save()
    {
        $this->validate();

        Employee::create([
            'employee_number' => $this->employee_number,
            'full_name' => $this->full_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth ?: null,
            'marital_status' => $this->marital_status ?: null,
            'phone' => $this->phone,
            'regions' => $this->regions ?: null,
            'email' => $this->email,
            'department_id' => $this->department_id ?: null,
            'type_of_employee_hire' => $this->type_of_employee_hire ?: null,
            'date_of_joining' => $this->date_of_joining ?: null,
            'position' => $this->position ?: null,
            'user_id' => $this->user_id ?: null,
            'activation' => $this->activation,
        ]);

        session()->flash('message', __('Employee successfully created.'));

        return $this->redirect(route('employee.index'), navigate: true);
    }

    #[Computed()]
    public function allStatuses()
    {
        return Status::get();
    }

    public function render()
    {
        $genders = GlobalSystemConstant::options()->where('type', 'gender');
        $activations = GlobalSystemConstant::options()->where('type', 'status');
        
        return view('livewire.org-app.employee.create', [
            'heading' => __('Create Employee'),
            'type' => 'save',
            'departments' => Department::get(),
            'users' => User::where('activation', 1)->get(),
            'genders' => $genders,
            'activations' => $activations,
        ]);
    }
}
