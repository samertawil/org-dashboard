<?php

namespace App\Services\EducationalActivityDetail\Validation;

interface EducationalActivityDetailValidationStrategy
{
    /**
     * Get the validation rules.
     *
     * @param int|null $maxConsistent
     * @param mixed $statusId
     * @return array
     */
    public function getRules(?int $maxConsistent, $statusId): array;

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function getMessages(): array;
}
