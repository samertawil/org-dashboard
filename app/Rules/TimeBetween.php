<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class TimeBetween implements ValidationRule
{
    protected string $startTime;
    protected string $endTime;

    /**
     * Create a new rule instance.
     *
     * @param string $startTime Start time in 'H:i' format.
     * @param string $endTime End time in 'H:i' format.
     */
    public function __construct(string $startTime = '08:00', string $endTime = '16:00')
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
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
            if ($time < $this->startTime || $time > $this->endTime) {
                if ($this->startTime === '08:00' && $this->endTime === '16:00') {
                    $fail(__('The time must be between 8:00 AM and 4:00 PM. يجب أن يكون الوقت بين الثامنة صباحاً والرابعة مساءً'));
                } else {
                    $fail(__('The time must be between :start and :end.', [
                        'start' => Carbon::parse($this->startTime)->format('g:i A'),
                        'end' => Carbon::parse($this->endTime)->format('g:i A')
                    ]));
                }
            }
        } catch (\Exception $e) {
            // Let the date_format rule handle parsing/format errors
        }
    }
}
