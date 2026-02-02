<?php

namespace App\Livewire\OrgApp\StudentGroups;

use App\Concerns\StudentsGroups\StudentsGroupsTrait;
use Livewire\Component;
use App\Models\StudentGroup;
use App\Reposotries\CityRepo;
use App\Reposotries\RegionRepo;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Validate;
use App\Enums\GlobalSystemConstant;
use Illuminate\Support\Facades\Gate;


class Create extends Component
{
    use StudentsGroupsTrait;
    
    #[Validate('required|string|unique:student_groups,name')]
    public $name = 'Student Group #';


    public function mount()
    {
        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
    }

    public function save()
    {
         $this->validate();
        $count = StudentGroup::count();
       
        StudentGroup::create([
            'name' => ucfirst($this->name).$count+1,
            'max_students' => $this->max_students,
            'min_students' => $this->min_students,
            'region_id' => $this->region_id ?: null,
            'city_id' => $this->city_id ?: null,
            'Moderator' => ucfirst($this->Moderator),
            'Moderator_phone' => $this->Moderator_phone,
            'Moderator_email' => $this->Moderator_email,
            'description' => $this->description,
            'activation' => $this->activation,
            'status_id' => $this->status_id ?: null,
        ]);

        session()->flash('message', __('Student Group successfully created.'));

        return $this->redirect(route('student.group.index'), navigate: true);
    }

    
    public function render()
    {
        return view('livewire.org-app.student-groups.create', [
            'heading' => __('Create Student Group'),
            'type' => 'save',
            'activations' => $this->activations,
            'regions' => $this->regions,
            'cities' => $this->cities,
         
            'statuses' => $this->statuses, 
        ]);
    }
}
