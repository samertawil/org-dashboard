<?php

namespace App\Services\EducationalActivityDetail\Validation;

use App\Models\ActivitySchedule;
use App\Services\EducationalActivityDetail\Validation\Strategies\ShortActivityValidationStrategy;
use App\Services\EducationalActivityDetail\Validation\Strategies\StandardActivityValidationStrategy;

class EducationalActivityDetailValidationFactory
{
    /**
     * Resolve and return the validation strategy for the given schedule.
     *
     * @param ActivitySchedule|null $schedule
     * @return EducationalActivityDetailValidationStrategy
     */
    public static function make(?ActivitySchedule $schedule): EducationalActivityDetailValidationStrategy
    {
        if ($schedule && $schedule->period_start && $schedule->period_end) {
            $duration = $schedule->period_start->diffInMinutes($schedule->period_end);
            if ($duration <= 15) {
                return new ShortActivityValidationStrategy();
            }
        }

        return new StandardActivityValidationStrategy();
    }
}
