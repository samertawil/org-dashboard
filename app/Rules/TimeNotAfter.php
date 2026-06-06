<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class TimeNotAfter implements ValidationRule
{
    protected string $maxTime;

    /**
     * Create a new rule instance.
     *
     * @param string $maxTime The maximum allowed time in 'H:i' format.
     */
    public function __construct(string $maxTime = '16:00')
    {
        $this->maxTime = $maxTime;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        try {
            $time = Carbon::parse($value)->format('H:i');
            if ($time > $this->maxTime) {
                if ($this->maxTime === '16:00') {
                    $fail(__('The time cannot exceed 4:00 PM. لا يمكن أن تتجاوز الساعة الرابعة مساءً'));
                } else {
                    $fail(__('The time cannot exceed :time.', ['time' => $this->maxTime]));
                }
            }
        } catch (\Exception $e) {
            // Let the date_format rule handle parsing/format errors
        }
    }
}
