<?php

namespace App\Livewire\OrgApp\Employee;

use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        session()->flash('message', __('Employee successfully deleted.'));
    }

    public function render()
    {
        $employees = Employee::with(['department', 'positionStatus'])
            ->where('full_name', 'like', '%' . $this->search . '%')
            ->orWhere('employee_number', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.org-app.employee.index', [
            'employees' => $employees,
        ]);
    }
}
