<?php

namespace App\Livewire\OrgApp\Department;

use Livewire\Component;
use App\Models\Department;
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
        if (Gate::denies('department.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        $department = Department::findOrFail($id);
        $department->delete();
        session()->flash('message', __('Department successfully deleted.'));
    }

    #[\Livewire\Attributes\Computed]
    public function departments()
    {
        return Department::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('location', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        if (Gate::denies('department.index')) {
            abort(403, 'You do not have the necessary permissions');
        }

        return view('livewire.org-app.department.index');
    }
}
