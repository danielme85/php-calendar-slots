<?php

namespace Danielme85\PhpCalendarSlots;

use Carbon\Carbon;

class CalendarDaySlot
{
    /**
     * @var Carbon
     */
    public Carbon $startsAt;

    /**
     * @var Carbon
     */
    public Carbon $endsAt;

    /**
     * @var bool
     */
    public bool $available;

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param bool $available
     */
    public function __construct(Carbon $start, Carbon $end, bool $available = true)
    {
        $this->startsAt = $start;
        $this->endsAt = $end;
        $this->available = $available;
    }

}