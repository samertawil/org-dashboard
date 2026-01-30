<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Enums\GlobalSystemConstant;  // Adjust namespace

class ValidStatusConstant implements Rule
{
    public function passes($attribute, $value): bool
    {
        $enum = GlobalSystemConstant::tryFrom((int) $value);
        return $enum && $enum->getType() === 'status';
    }

    public function message(): string
    {
        return 'The selected module name is invalid.';
    }
}
