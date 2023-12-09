<?php

namespace Danielme85\PhpCalendarSlots;

use Carbon\Carbon;
use Carbon\CarbonInterval;

class CalendarDay
{
    /**
     * @var Carbon
     */
    public Carbon $date;

    public string $startAt;

    public string $endAt;

    public int $interval;

    /**
     * @var array
     */
    public array $slots;

    public function __construct(Carbon $date, string $startAt, string $endAt, int $interval = 60)
    {
        $this->date = $date;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->interval = $interval;
    }

    public function addSlots(array $slots)
    {
        foreach ($slots as $slot) {
            $this->addSlot($slot);
        }

        return $this;
    }

    public function addSlot(CalendarDaySlot $slot)
    {
        $this->slots[] = $slot;
    }

    /**
     * @return void
     */
    public function buildSlots()
    {
        $from = $this->date->copy();
        $to = $this->date->copy();

        $from->setTimeFromTimeString($this->startAt);
        $to->setTimeFromTimeString($this->endAt)->subMinutes($this->interval);

        $intervals = CarbonInterval::minutes($this->interval)
            ->toPeriod(
                $from->toDateTimeString(),
                $to->toDateTimeString(),
            );

        foreach ($intervals as $start) {
            $add = true;
            $end = $start->copy();
            $end->addMinutes($this->interval);

            //Check if we already have slots in this period
            if (!empty($this->slots)) {
                foreach ($this->slots as $slot) {
                    if ($slot->startsAt->isBefore($end) &&
                        $start->isBefore(Carbon::parse($slot->endsAt))) {
                        $add = false;
                        break;
                    }
                }
            }

            if ($add) {
                $this->slots[] = new CalendarDaySlot($start, $end);
            }
        }
    }
}
