<?php

namespace App\Concerns\SubjectForLearn;

use App\Reposotries\StatusRepo;
use Livewire\Attributes\Validate;
use App\Enums\GlobalSystemConstant;
use Illuminate\Validation\Rule;

trait SubjectForLearnTrait
{
    #[Validate('nullable|exists:statuses,id')]
    public $type_id = '';

    #[Validate('nullable|string')]
    public $description = null;

    public $from_age = null;

    public $to_age = null;

    #[Validate('required|integer')]
    public $activation = GlobalSystemConstant::ACTIVE->value;

    public $statuses = [];
    public $activations = [];

    public function rules()
    {
        return [
            'from_age' => 'nullable|integer|min:6|max:12',
            'to_age' => [
                'nullable',
                'integer',
                'min:6',
                'max:12',
                Rule::when($this->from_age, fn() => ['gte:' . $this->from_age]),
            ],
        ];
    }

    public function bootSubjectForLearnTrait()
    {
        $this->statuses = StatusRepo::statuses()->where('p_id_sub', 63);
        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
    }
}
