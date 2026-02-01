<?php

namespace App\Livewire\OrgApp\StudentGroups;

use App\Models\City;
use App\Models\Region;
use Livewire\Component;
use App\Models\StudentGroup;
use App\Reposotries\CityRepo;
use App\Reposotries\RegionRepo;
use App\Reposotries\StatusRepo;
use Livewire\Attributes\Validate;
use App\Enums\GlobalSystemConstant;
use Illuminate\Support\Facades\Gate;
use App\Reposotries\NeighbourhoodRepo;

class Create extends Component
{
    #[Validate('required|string|unique:student_groups,name')]
    public $name = 'Student Group #';

    #[Validate('required|integer|min:0|gt:min_students', message: ['gt:min_students' => 'Max students must exceed min students.'])]
    public $max_students = 0;
    

    #[Validate('required|integer|min:0|lt:max_students', 
    message: ['lt:max_students' => 'Min students must be less than max students.']
)]
public $min_students = 0;

    #[Validate('nullable|exists:regions,id')]
    public $region_id = '';

    #[Validate('nullable|exists:cities,id')]
    public $city_id = '';

    #[Validate('nullable|string')]
    public $Moderator = '';

    #[Validate('nullable|string')]
    public $Moderator_phone = '';

    #[Validate('nullable|email')]
    public $Moderator_email = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|integer')]
    public $activation = GlobalSystemConstant::ACTIVE->value;

    #[Validate('nullable|exists:statuses,id')]
    public $status_id = '';

    public $activations = [];

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

    public function updated($property, $value)
    {
        if ($property === 'name' || $property === 'Moderator') {
            $this->$property = ucfirst($value);
        }
    }

    public function updatedRegionId()
    {
        $this->city_id = '';
       
    }
    public function render()
    {
        return view('livewire.org-app.student-groups.create', [
            'heading' => __('Create Student Group'),
            'type' => 'save',
            'activations' => $this->activations,
            'regions' => RegionRepo::regions(),
            'cities' => $this->region_id ? CityRepo::cities()->where('region_id', $this->region_id):collect(),
         
            'statuses' => StatusRepo::statuses(), 
        ]);
    }
}
