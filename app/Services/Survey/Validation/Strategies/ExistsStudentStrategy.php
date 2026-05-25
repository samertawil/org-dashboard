<?php

namespace App\Services\Survey\Validation\Strategies;

use App\Services\Survey\Validation\SurveyValidationStrategy;

class ExistsStudentStrategy implements SurveyValidationStrategy
{
    /**
     * Get the validation rules.
     *
     * @return array
     */
    public function getRules(): array
    {
        return [
            'account_id' => 'required|numeric|min_digits:9|max_digits:9|exists:students,identity_number',
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function getMessages(): array
    {
        return [
            'account_id.required'   => 'رقم الهوية مطلوب',
            'account_id.numeric'    => 'رقم الهوية يجب أن يكون رقمًا',
            'account_id.min_digits' => 'رقم الهوية يجب أن يكون 9 أرقام',
            'account_id.max_digits' => 'رقم الهوية يجب أن يكون 9 أرقام',
            'account_id.exists'     => 'رقم الهوية غير صحيح أو غير مسجل لدينا',
        ];
    }
}
