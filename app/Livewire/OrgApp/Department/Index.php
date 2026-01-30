<?php

namespace App\Livewire\OrgApp\Department;

use App\Models\Department;
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
        $department = Department::findOrFail($id);
        $department->delete();
        session()->flash('message', __('Department successfully deleted.'));
    }

    public function render()
    {
        $departments = Department::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.org-app.department.index', [
            'departments' => $departments,
        ]);
    }
}
