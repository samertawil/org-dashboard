<?php

namespace App\Livewire\OrgApp\SubjectForLearn;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;
use App\Models\StudentSubjectForLearn;
use App\Concerns\SubjectForLearn\SubjectForLearnTrait;

class Create extends Component
{
    use SubjectForLearnTrait;

    #[Validate('required|string|unique:student_subject_for_learns,name')]
    public $name = '';

    // public function rules() {
    //     return [
    //         'birth_date' => 'required|date|before_or_equal:' . Student::maxBirthDate() . '|after_or_equal:' . Student::minBirthDate(),           
    //     ];
    // }

    public function save()
    {
        $this->validate();

        StudentSubjectForLearn::create([
            'name' => ucfirst($this->name),
            'type_id' => $this->type_id ?: null,
            'description' => $this->description,
            'activation' => $this->activation,
            'from_age' => $this->from_age,
            'to_age' => $this->to_age,
        ]);

        session()->flash('message', __('Subject successfully created.'));

        return $this->redirect(route('subject.index'), navigate: true);
    }

    public function render()
    {
        if(Gate::denies('subject.create')) {
            abort(403, 'You do not have the necessary permissions');
        }
        return view('livewire.org-app.subject-for-learn.create');
    }
}
