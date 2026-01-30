<?php

namespace App\Livewire\OrgApp\Department;

use Livewire\Component;
use App\Models\Department;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public Department $department;

    public $name = '';

    #[Validate('required|string|max:255')]
    public $location = '';

    #[Validate('nullable|string')]
    public $description = '';

    public function mount(Department $department)
    {
        $this->department = $department;
        $this->name = $department->name;
        $this->location = $department->location;
        $this->description = $department->description;
    }

    public function rules() {
        return [
            'name' =>  'required|string|max:255|unique:departments,name,' . $this->department->id ,     
        ];
       
    }

    public function update()
    {
        $this->validate();

        $this->department->update([
            'name' => $this->name,
            'location' => $this->location,
            'description' => $this->description,
        ]);

        session()->flash('message', __('Department successfully updated.'));

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
        return view('livewire.org-app.department.edit', [
            'heading' => __('Edit Department'),
            'type' => 'update',
        ]);
    }
}
