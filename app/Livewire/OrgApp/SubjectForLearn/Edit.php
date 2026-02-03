<?php

namespace App\Livewire\OrgApp\SubjectForLearn;

use Livewire\Component;
use App\Models\StudentSubjectForLearn;
use App\Concerns\SubjectForLearn\SubjectForLearnTrait;

class Edit extends Component
{
    use SubjectForLearnTrait;

    public StudentSubjectForLearn $subject;
    public $name = '';

    public function rules() 
    {
        return [
             'name' => 'required|string|unique:student_subject_for_learns,name,' . $this->subject->id,
        ];
    }

    public function mount(StudentSubjectForLearn $subject)
    {
        $this->subject = $subject;
        $this->name = $subject->name;
        $this->type_id = $subject->type_id;
        $this->description = $subject->description;
        $this->activation = $subject->activation;
        // Arrays are loaded by trait boot method
    }

    public function save()
    {
        $this->validate();

        $this->subject->update([
            'name' => ucfirst($this->name),
            'type_id' => $this->type_id ?: null,
            'description' => $this->description,
            'activation' => $this->activation,
        ]);

        session()->flash('message', __('Subject successfully updated.'));

        return $this->redirect(route('subject.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.org-app.subject-for-learn.edit');
    }
}
