<?php

namespace App\Services\Survey\Validation;

interface SurveyValidationStrategy
{
    /**
     * Get the validation rules.
     *
     * @return array
     */
    public function getRules(): array;

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function getMessages(): array;
}
