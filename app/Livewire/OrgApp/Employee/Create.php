<?php

namespace App\Livewire\OrgApp\Employee;

use Livewire\Component;
use App\Models\Employee;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;
use App\Concerns\Employee\EmployeeTrait;

class Create extends Component
{
    use EmployeeTrait;

    #[Validate('required|string|unique:employees,employee_number')]
    public $employee_number = '';

    #[Validate('required|string|unique:employees,full_name')]
    public $full_name = '';

    #[Validate('nullable|email|unique:employees,email')]
    public $email = null;

    public function save()
    {
       
        $this->validate();

        Employee::create([
            'employee_number' => $this->employee_number,
            'full_name' => ucfirst($this->full_name),
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
            'employee_in_partner_id'=> $this->employee_in_partner_id ?: null,
            'job_title'=> $this->job_title ?: null,
        ]);

        session()->flash('message', __('Employee successfully created.'));

        return $this->redirect(route('employee.index'), navigate: true);
    }

    public function render()
    {
        
        if (Gate::denies('employee.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.employee.create', [
            'heading' => __('Create Employee'),
            'type' => 'save',
            'departments' => $this->departments,
            'users' =>$this->users,
            'genders' => $this->genders,
            'activations' => $this->activations,
        ]);
    }
}
