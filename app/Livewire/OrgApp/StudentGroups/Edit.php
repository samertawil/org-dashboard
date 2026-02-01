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

class Edit extends Component
{
    public StudentGroup $group;

    public $name = '';
    #[Validate('required|integer|min:0|gt:min_students', message: ['gt:min_students' => 'Max students must exceed min students.'])]
    public $max_students = 0;
    

    #[Validate('required|integer|min:0|lt:max_students', 
    message: ['lt:max_students' => 'Min students must be less than max students.']
)]
public $min_students = 0;
    public $region_id = '';
    public $status_id = '';
    public $city_id = '';
    public $Moderator = '';
    public $Moderator_phone = '';
    public $Moderator_email = '';
    public $description = '';
    public $activation = GlobalSystemConstant::ACTIVE->value;

    public $activations = [];

    public function rules()
    {
        return [
            'name' => 'required|string|unique:student_groups,name,' . $this->group->id,
           
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'Moderator' => 'nullable|string',
            'Moderator_phone' => 'nullable|string',
            'Moderator_email' => 'nullable|email',
            'description' => 'nullable|string',
            'activation' => 'required|integer',
            'status_id' => 'nullable|exists:statuses,id',
        ];
    }

    public function mount(StudentGroup $group)
    {
       
        $this->group = $group;
        $this->name = $group->name;
        $this->max_students = $group->max_students;
        $this->min_students = $group->min_students;
        $this->region_id = $group->region_id;
        $this->city_id = $group->city_id;
        $this->Moderator = $group->Moderator;
        $this->Moderator_phone = $group->Moderator_phone;
        $this->Moderator_email = $group->Moderator_email;
        $this->description = $group->description;
        $this->activation = $group->activation;
        $this->status_id = $group->status_id;

        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
    }

    public function save()
    {
        $this->validate();

        $this->group->update([
            'name' => ucfirst($this->name),
            'max_students' => $this->max_students,
            'min_students' => $this->min_students,
            'region_id' => $this->region_id ?: null,
            'city_id' => $this->city_id ?: null,
            'status_id' => $this->status_id ?: null,
            'Moderator' => ucfirst($this->Moderator),
            'Moderator_phone' => $this->Moderator_phone,
            'Moderator_email' => $this->Moderator_email,
            'description' => $this->description,
            'activation' => $this->activation,
        ]);

        session()->flash('message', __('Student Group successfully updated.'));

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
        return view('livewire.org-app.student-groups.edit', [
            'heading' => __('Edit Student Group'),
            'type' => 'save', // Using save as method name is save() not update()
            'activations' => $this->activations,
            'regions' => RegionRepo::regions(),
            'cities' => $this->region_id ? CityRepo::cities()->where('region_id', $this->region_id):collect(),
            'statuses' => StatusRepo::statuses(),
        ]);
    }
}
