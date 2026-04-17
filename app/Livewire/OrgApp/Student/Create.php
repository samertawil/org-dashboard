<?php

namespace App\Livewire\OrgApp\Student;

use App\Concerns\Student\StudentTrait;
use App\Models\Student;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Rules\GlobalValidation;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public $answer = [];

    use StudentTrait;

    #[Validate('required|integer|min_digits:9|max_digits:9|unique:students,identity_number')]
    public $identity_number = '';

    #[Validate('required|in:full_week,sat_mon_wed,sun_tue_thu')]
    public $enrollment_type = 'sat_mon_wed';

    public function rules()
    {
        return [
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


            if (is_array($this->answer)) {
                foreach ($this->answer as $questionId => $answerText) {
                    if (!empty($answerText)) {
                        SurveyAnswer::create([
                            'account_id' => $this->identity_number,
                            'question_id' => $questionId,
                            'answer_ar_text' => $answerText,
                            'survey_no' => 120,
                            'created_by' => Auth::user()->employee->id,
                            
                        ]);
                    }
                }
            }
            DB::commit();
            session()->flash('message', __('Student successfully Created.'));
            return $this->redirect(route('student.index'), navigate: true);
           
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', __('Student creation failed.'));
        }
    }

    #[Computed()]
    public function surveyquestions()
    {
        return  SurveyQuestion::where('survey_for_section', 120)->orderBy('question_order')->get();
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
