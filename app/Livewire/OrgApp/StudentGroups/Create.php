<?php

namespace App\Livewire\OrgApp\StudentGroups;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\StudentGroup;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Enums\GlobalSystemConstant;
use App\Models\StudentGroupSchedule;
use Illuminate\Support\Facades\Gate;
use App\Concerns\StudentsGroups\StudentsGroupsTrait;


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
        // $count = StudentGroup::count();
        DB::beginTransaction();
       try {
       
        $data =  StudentGroup::create([
           'name' => ucfirst($this->name),
            // .$count+1,
            'max_students' => $this->max_students,
            'min_students' => $this->min_students,
            'region_id' => $this->region_id ?: null,
            'city_id' => $this->city_id ?: null,
            'neighbourhood_id' => $this->neighbourhood_id ?: null,
            'location_id' => $this->location_id ?: null,
            'address_details' => $this->address_details?: null ,
            'start_date' => $this->start_date?: null,
            'end_date' => $this->end_date?: null,
            'start_time' => $this->start_time?: null,
            'end_time' => $this->end_time?: null,
            'batch_no' => $this->batch_no,
            
            'Moderator' => ucfirst($this->Moderator)?:null,
            'Moderator_phone' => $this->Moderator_phone,
            'Moderator_email' => $this->Moderator_email,
            'description' => $this->description,
            
            'activation' => $this->activation,
            'status_id' => $this->status_id ?: null,
            'subject_to_learn_id' => $this->subject_to_learn_id,
        ]);

        if ($data->start_date && $data->end_date && $this->start_time && $this->end_time) {
            $startDate = Carbon::parse($data->start_date);
            $endDate = Carbon::parse($data->end_date);

            while ($startDate->lte($endDate)) {
                 $hours = 0;
                 if ($this->start_time && $this->end_time) {
                      $s = Carbon::parse($this->start_time);
                      $e = Carbon::parse($this->end_time);
                      $hours = $s->diffInHours($e);
                 }

                 $dayName = $startDate->format('l');
                 $isOffDay = in_array($dayName, ['Friday']);

                 StudentGroupSchedule::create([
                     'student_group_id' => $data->id,
                     'schedule_date' => $startDate->format('Y-m-d'),
                     'day' => $dayName,
                     'start_time' => $this->start_time,
                     'end_time' => $this->end_time,
                     'hours' => $hours,
                     'name' => $data->name,
                     'activation' => 1,
                     'is_off_day' => $isOffDay,
                 ]);
                 
                 $startDate->addDay();
            }
        }

      
         DB::commit();

    } catch (\Throwable $th) {
        dd($th->getMessage());
        DB::rollback();
       }
        session()->flash('message', __('Student Group and Schedules successfully created.'));

        return $this->redirect(route('student.group.index'), navigate: true);
    }

    
    public function render()
    {
        
        if (Gate::denies('student.group.create')) 
        { 
            abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.org-app.student-groups.create', [
            'heading' => __('Create Student Group'),
            'type' => 'save',
            'activations' => $this->activations,
            'regions' => $this->regions,
            'cities' => $this->cities,
            'neighbourhoods' => $this->neighbourhoods,
            'locations' => $this->locations,
            'statuses' => $this->statuses, 
            'subjects' => $this->subjects, 
        ]);
    }
}
