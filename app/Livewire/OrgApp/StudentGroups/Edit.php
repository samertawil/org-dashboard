<?php

namespace App\Livewire\OrgApp\StudentGroups;


use Livewire\Component;
use App\Models\StudentGroup;
use App\Reposotries\CityRepo;
use App\Reposotries\LocationRepo;
use App\Reposotries\NeighbourhoodRepo;
use App\Enums\GlobalSystemConstant;
use Illuminate\Support\Facades\Gate;

use App\Concerns\StudentsGroups\StudentsGroupsTrait;

class Edit extends Component
{
    use StudentsGroupsTrait;
    
    public StudentGroup $group;

    public $name = '';
    
    public $activations = [];

    public function rules()
    {
        return [
            'name' => 'required|string|unique:student_groups,name,' . $this->group->id,
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
        $this->activation = $group->activation;
        $this->status_id = $group->status_id;
        $this->subject_to_learn_id = $group->subject_to_learn_id ?? [];

        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
        $this->cities = $this->region_id ? CityRepo::cities()->where('region_id', $this->region_id) : collect();

        $this->address_details = $group->address_details;
        $this->start_date = $group->start_date;
        $this->end_date = $group->end_date;
        $this->start_time = $group->start_time ? $group->start_time->format('H:i') : null;
        $this->end_time = $group->end_time ? $group->end_time->format('H:i') : null;
        $this->neighbourhood_id = $group->neighbourhood_id;
        $this->location_id = $group->location_id;

        $this->cities = $this->region_id ? CityRepo::cities()->where('region_id', $this->region_id) : collect();
        $this->neighbourhoods = $this->city_id ? NeighbourhoodRepo::neighbourhoods()->where('city_id', $this->city_id) : collect();

        $this->locations = $this->neighbourhood_id ? LocationRepo::locations()->where('neighbourhood_id', $this->neighbourhood_id) : collect();
        
 
        
      
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
            'description' => $this->description,
            'activation' => $this->activation,
            'subject_to_learn_id' => $this->subject_to_learn_id,
            'neighbourhood_id' => $this->neighbourhood_id ?: null,
            'location_id' => $this->location_id ?: null,
            'address_details' => $this->address_details?: null ,
            'start_date' => $this->start_date?: null,
            'end_date' => $this->end_date?: null,
            'start_time' => $this->start_time?: null,
            'end_time' => $this->end_time?: null,
        ]);

        session()->flash('message', __('Student Group successfully updated.'));

        return $this->redirect(route('student.group.index'), navigate: true);
    }
    

    public function render()
    {
        return view('livewire.org-app.student-groups.edit', [
            'heading' => __('Edit Student Group'),
            'type' => 'save', // Using save as method name is save() not update()
            'regions' => $this->regions,
            'cities' => $this->cities,    
            'neighbourhoods' => $this->neighbourhoods,    
            'locations' => $this->locations,    
            'statuses' => $this->statuses,
            'subjects' => $this->subjects,
        ]);
       
    }
}
