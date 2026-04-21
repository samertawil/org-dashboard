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
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
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

    #[\Livewire\Attributes\Computed]
    public function employees()
    {
        return Employee::with(['department', 'positionStatus', 'partner', 'jobTitle'])
            ->where(function($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('department', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('partner', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('jobTitle', function($q) {
                        $q->where('status_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('employee_number', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        if (Gate::denies('employee.index')) {
            abort(403, 'You do not have the necessary permissions.');
        }

        return view('livewire.org-app.employee.index');
    }
}