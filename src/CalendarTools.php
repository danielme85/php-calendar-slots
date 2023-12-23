<?php

namespace Danielme85\PhpCalendarSlots;

use Carbon\Carbon;

trait CalendarTools
{

    /**
     * @param string|Carbon|null $date
     * @param string $timezone
     * @return Carbon|null
     */
    public static function createCarbonDateIfNeeded(string|Carbon|null $date, string $timezone = 'UTC'): ?Carbon
    {
        if (empty($date)) {
            return null;
        }
        if (is_string($date)) {
            return Carbon::parse($date, $timezone);
        }

        return $date;
    }

}