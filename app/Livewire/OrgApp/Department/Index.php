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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();
        session()->flash('message', __('Department successfully deleted.'));
    }

    public function render()
    {
        if (Gate::denies('department.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        $departments = Department::where('name', 'like', '%' . $this->search . '%')
         
            ->orwhere('location', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.org-app.department.index', [
            'departments' => $departments,
        ]);
    }
}
