<?php

namespace App\Rules;

use Closure;
use App\Enums\GlobalSystemConstant;
use Illuminate\Contracts\Validation\ValidationRule;

class GlobalValidation implements ValidationRule
{
    protected string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $enum = GlobalSystemConstant::tryFrom((int)$value);

        if (! $enum || $enum->getType() !== $this->type) {
            $fail('The selected :attribute is invalid.');
        }
    }
}
