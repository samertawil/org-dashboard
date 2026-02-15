<?php

namespace App\Livewire\OrgApp\Employee;

use Livewire\Component;
use App\Models\Employee;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

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
        if (Gate::denies('employee.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }  
        $employee = Employee::findOrFail($id);
        $employee->delete();
        session()->flash('message', __('Employee successfully deleted.'));
    }

    public function render()
    {
        if (Gate::denies('employee.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $employees = Employee::with(['department', 'positionStatus'])
            ->where(function($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('department', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('employee_number', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.org-app.employee.index', [
            'employees' => $employees,
        ]);
    }
}