<?php

namespace App\Services\Survey\Validation;

use App\Services\Survey\Validation\Strategies\ExistsStudentStrategy;
use App\Services\Survey\Validation\Strategies\UniqueStudentStrategy;
use App\Models\SurveyTable;

class SurveyValidationFactory
{
    /**
     * Resolve and return the validation strategy for the given survey.
     *
     * @param SurveyTable $survey
     * @return SurveyValidationStrategy
     */
    public static function make(SurveyTable $survey): SurveyValidationStrategy
    {
        // Currently determining strategy based on survey ID:
        // Survey ID 3 needs 'unique', all other surveys need 'exists'.
        return match ($survey->id) {
            3 => new UniqueStudentStrategy(),
            default => new ExistsStudentStrategy(),
        };
    }
}
