<?php

namespace App\Livewire\OrgApp\Department;

use Livewire\Component;
use App\Models\Department;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

class Create extends Component
{
    #[Validate('required|string|max:255|unique:departments,name')]
    public $name = '';

    #[Validate('nullable|string|max:255')]
    public $location = '';

    #[Validate('nullable|string')]
    public $description = '';

    public function save()
    {
        $this->validate();

        Department::create([
            'name' => $this->name,
            'location' => $this->location,
            'description' => $this->description,
        ]);

        session()->flash('message', __('Department successfully created.'));

        return $this->redirect(route('department.index'), navigate: true);
    }

    public function updated($property, $value)
    {
        if ($property === 'location' || $property === 'name') {
            $this->$property = ucfirst($value);
        }
    }

    #[Computed()]
    public function locations()
    {
        return Department::select('location')->distinct()->get();
    }
    public function render()
    {
        return view('livewire.org-app.department.create', [
            'heading' => __('Create Department'),
            'type' => 'save',
        ]);
    }
}
