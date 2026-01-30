<?php

namespace App\Livewire\OrgApp\Employee;

use App\Models\User;
use App\Models\Status;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Department;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Enums\GlobalSystemConstant;

class Edit extends Component
{
    public Employee $employee;

    public $employee_number = '';
    public $full_name = '';
    public $gender = 2;
    public $date_of_birth = '';
    public $marital_status = '';
    public $phone = '';
    public $regions = '';
    public $email = '';
    public $department_id = '';
    public $type_of_employee_hire = '';
    public $date_of_joining = '';
    public $position = '';
    public $user_id = '';
    public $activation = 1;

    public function rules()
    {
        return [
            'employee_number' => 'required|string|unique:employees,employee_number,' . $this->employee->id,
            'full_name' => 'required|string|unique:employees,full_name,' . $this->employee->id,
            'gender' => 'required|integer',
            'date_of_birth' => 'nullable|date',
            'marital_status' => 'nullable|exists:statuses,id',
            'phone' => 'nullable|string',
            'regions' => 'nullable|exists:statuses,id',
            'email' => 'nullable|email|unique:employees,email,' . $this->employee->id,
            'department_id' => 'nullable|exists:departments,id',
            'type_of_employee_hire' => 'nullable|exists:statuses,id',
            'date_of_joining' => 'nullable|date',
            'position' => 'nullable|exists:statuses,id',
            'user_id' => 'nullable|exists:users,id',
            'activation' => 'required|integer',
        ];
    }

    public function mount(Employee $employee)
    {
        $this->employee = $employee;
        $this->employee_number = $employee->employee_number;
        $this->full_name = $employee->full_name;
        $this->gender = $employee->gender;
        $this->date_of_birth = $employee->date_of_birth;
        $this->marital_status = $employee->marital_status;
        $this->phone = $employee->phone;
        $this->regions = $employee->regions;
        $this->email = $employee->email;
        $this->department_id = $employee->department_id;
        $this->type_of_employee_hire = $employee->type_of_employee_hire;
        $this->date_of_joining = $employee->date_of_joining;
        $this->position = $employee->position;
        $this->user_id = $employee->user_id;
        $this->activation = $employee->activation;
    }

    public function update()
    {
        $this->validate();

        $this->employee->update([
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

        session()->flash('message', __('Employee successfully updated.'));

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

        return view('livewire.org-app.employee.edit', [
            'heading' => __('Edit Employee'),
            'type' => 'update',
            'departments' => Department::all(),
             'users' => User::all(),
            'genders' => $genders,
            'activations' => $activations,
        ]);
    }
}
