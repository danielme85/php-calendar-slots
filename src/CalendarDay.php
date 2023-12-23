<?php

namespace Danielme85\PhpCalendarSlots;

use Carbon\Carbon;
use Carbon\CarbonInterval;

class CalendarDay
{
    use CalendarTools;

    /**
     * @var Carbon
     */
    public Carbon $date;

    /**
     * @var string
     */
    public string $startAt;

    /**
     * @var string
     */
    public string $endAt;

    /**
     * @var int
     */
    public int $interval;

    /**
     * @var array
     */
    public array $slots;

    /**
     * @param string|Carbon $date
     * @param string $startAt
     * @param string $endAt
     * @param int $interval
     */
    public function __construct(string|Carbon $date, string $startAt, string $endAt, int $interval = 60)
    {
        $this->date = self::createCarbonDateIfNeeded($date);
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->interval = $interval;
    }

    /**
     * @param array $slots
     * @return self
     */
    public function addSlots(array $slots): self
    {
        foreach ($slots as $slot) {
            $this->addSlot($slot);
        }

        return $this;
    }

    /**
     * @param CalendarDaySlot $slot
     * @return self
     */
    public function addSlot(CalendarDaySlot $slot): self
    {
        $this->slots[] = $slot;

        return $this;
    }

    /**
     * @return void
     */
    public function buildSlots(): void
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
