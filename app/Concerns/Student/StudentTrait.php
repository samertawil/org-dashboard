<?php

namespace App\Concerns\Student;

use App\Enums\GlobalSystemConstant;
use App\Models\StudentGroup;
use App\Reposotries\StatusRepo;
use App\Services\CivilRegistryApiServices;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;

trait StudentTrait
{
    public $birth_date = '';
    public $identity_number = '';

    #[Validate('required|string')]
    public $full_name = '';

    #[Validate('nullable|exists:student_groups,id')]
    public $student_groups_id = '';

    public $gender = '';

    #[Validate('required')]
    #[Validate('global_validation:status', message: 'Wrong value')]
    public $activation = GlobalSystemConstant::ACTIVE->value;

    public $genders = [];

    #[Validate('nullable|exists:statuses,id')]
    public $status_id = '';

    #[Validate('nullable|string')]
    public $parent_phone = '';

    #[Validate('nullable|exists:statuses,id')]
    public $living_parent_id = '';

    #[Validate('nullable|string')]
    public $notes = '';

    public $activations = [];
    public $statuses = [];
    public $studentGroups = [];

    public $relations_data = [];
    public $selected_relation = null;

    public function updatedSelectedRelation($index)
    {
        if (isset($this->relations_data[$index])) {
            $relation = $this->relations_data[$index];
            $this->answer[1] = $relation['fullNameArabic'] ?? '';
            $this->answer[3] = $relation['relationIdentityNumber'] ?? '';
            $this->answer[2] = $relation['relationTypeName'] ?? '';
        }
    }





    public function bootStudentTrait()
    {

        $this->activations = GlobalSystemConstant::options()->where('type', 'status');
        $this->genders = GlobalSystemConstant::options()->where('type', 'gender');
        $this->statuses = StatusRepo::statuses()->where('p_id_sub', config('appConstant.student_groups'));
        $this->studentGroups = StudentGroup::all();
    }

    public function getData(CivilRegistryApiServices $CivilRegistryApiServices)
    {
        $this->relations_data = [];
        $this->selected_relation = null;

        $identity_number =  $this->validateOnly('identity_number')['identity_number'];
        try {
            $response =  $CivilRegistryApiServices->getData($identity_number);

            $this->full_name = $response['data']['full_name'];
            $this->birth_date = $response['data']['birth_date'];
            $this->gender = $response['data']['gender'] == 0 ?
                GlobalSystemConstant::MALE->value : GlobalSystemConstant::FEMALE->value;
            $this->relations_data = $response['relations_data'] ?? [];
        } catch (\Exception $e) {

            throw ValidationException::withMessages([
                'identity_number' =>  __($e->getMessage()),
            ]);
        }
    }
}
