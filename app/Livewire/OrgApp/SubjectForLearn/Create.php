<?php

namespace App\Livewire\OrgApp\SubjectForLearn;

use Livewire\Component;
use App\Models\StudentSubjectForLearn;
use App\Concerns\SubjectForLearn\SubjectForLearnTrait;
use Livewire\Attributes\Validate;

class Create extends Component
{
    use SubjectForLearnTrait;

    #[Validate('required|string|unique:student_subject_for_learns,name')]
    public $name = '';

    public function save()
    {
        $this->validate();

        StudentSubjectForLearn::create([
            'name' => ucfirst($this->name),
            'type_id' => $this->type_id ?: null,
            'description' => $this->description,
            'activation' => $this->activation,
        ]);

        session()->flash('message', __('Subject successfully created.'));

        return $this->redirect(route('subject.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.org-app.subject-for-learn.create');
    }
}
