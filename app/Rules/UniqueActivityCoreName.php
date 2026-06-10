<?php

namespace App\Rules;

use App\Models\EducationalActivityName;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates that the activity name does not duplicate an existing record
 * at the "core content" level — ignoring parenthetical prefixes/suffixes,
 * dashes, underscores, and extra spaces.
 *
 * Usage in Create:
 *   new UniqueActivityCoreName()
 *
 * Usage in Edit (to exclude the current record):
 *   new UniqueActivityCoreName($this->activityName->id)
 */
class UniqueActivityCoreName implements ValidationRule
{
    public function __construct(private ?int $excludeId = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (EducationalActivityName::isCoreDuplicate((string) $value, $this->excludeId)) {
            $fail(__('يوجد نشاط آخر بنفس المحتوى الجوهري. يرجى استخدام اسم مختلف.'));
        }
    }
}
