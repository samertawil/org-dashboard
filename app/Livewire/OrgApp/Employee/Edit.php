<?php

namespace App\Livewire\OrgApp\Employee;


use Livewire\Component;
use App\Models\Employee;
use Illuminate\Support\Facades\Gate;
use App\Concerns\Employee\EmployeeTrait;

class Edit extends Component
{
    use EmployeeTrait;
    public Employee $employee;

    public $employee_number = '';
    public $full_name = '';
    public $email = null;

    public function rules()
    {
        return [
            'employee_number' => 'required|string|unique:employees,employee_number,' . $this->employee->id,
            'full_name' => 'required|string|unique:employees,full_name,' . $this->employee->id,
            'email' => 'nullable|email|unique:employees,email,' . $this->employee->id,
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

        $this->employee->fill([
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

        if ($this->employee->isDirty()) {
            $this->employee->save();
            session()->flash('message', __('Employee successfully updated.'));
        } else {
            session()->flash('message', __('No changes were made!'));
            session()->flash('type', 'warning');
        }

        return $this->redirect(route('employee.index'), navigate: true);
    }

    public function render()
    { 
        if (Gate::denies('employee.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }  
        return view('livewire.org-app.employee.edit', [
            'heading' => __('Edit Employee'),
            'type' => 'update',
            'departments' => $this->departments,
            'users' =>$this->users,
            'genders' => $this->genders,
            'activations' =>$this->activations,
        ]);
    }
}
