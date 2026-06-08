<?php

namespace App\Livewire\OrgApp\Student;

use App\Concerns\Student\StudentTrait;
use App\Models\Student;
use App\Rules\GlobalValidation;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{

    use StudentTrait;

    public $identity_number = '';

    #[Validate('required|in:full_week,sat_mon_wed,sun_tue_thu')]
    public $enrollment_type = 'sat_mon_wed';

    public function rules()
    {
        return [
            'identity_number' => [
                'required',
                'integer',
                'min_digits:9',
                'max_digits:9',
                function ($attribute, $value, $fail) {
                    $existingStudent = Student::where('identity_number', $value)->first();
                    if ($existingStudent) {
                        $groupName = $existingStudent->studentGroup?->name ?? __('No Group. لا توجد مجموعة');
                        $fail(__('  هذا الطالب موجود بالفعل في مركز :group', ['group' => $groupName]));
                    }
                }
            ],
            'birth_date' => 'required|date|before_or_equal:' . Student::maxBirthDate() . '|after_or_equal:' . Student::minBirthDate(),

            'gender' => [
                'required',
                new GlobalValidation('gender'),
            ],
        ];
    }
    public function save()
    {

        $this->validate();

        DB::beginTransaction();
        try {

            Student::create([
                'identity_number' => $this->identity_number,
                'full_name' => $this->full_name,
                'birth_date' => $this->birth_date,
                'student_groups_id' => $this->student_groups_id ?: null,
                'gender' => $this->gender,
                'enrollment_type' => $this->enrollment_type,
                'activation' => $this->activation,
                'status_id' => $this->status_id ?: null,
                'parent_phone' => $this->parent_phone,
                'living_parent_id' => $this->living_parent_id ?: null,
                'notes' => $this->notes,
                'added_type' => 1, // Manual
                'created_by' => Auth::user()->employee->id,
            ]);


            DB::commit();
            session()->flash('message', __('Student successfully Created.'));
            return $this->redirect(route('student.index'), navigate: true);
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', __('Student creation failed.'));
        }
    }



    public function render()
    {

        if (Gate::denies('student.create')) {
            abort(403, 'You do not have the necessary permissions.');
        }
        return view('livewire.org-app.student.create', [
            'heading' => __('Create Student'),
            'type' => 'save',
        ]);
    }
}
