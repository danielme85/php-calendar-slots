<?php

namespace Danielme85\PhpCalendarSlots;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Calendar
{
    use CalendarTools;

    /**
     * @var int
     */
    public int $interval;

    /**
     * @var Carbon
     */
    public Carbon $startDate;

    /**
     * @var Carbon
     */
    public Carbon $endDate;

    /**
     * @var string
     */
    public string $defaultStartTime;

    /**
     * @var string
     */
    public string $defaultEndTime;

    /**
     * @var string
     */
    public string $timeZone;

    /**
     * @var array
     */
    public array $days;

    /**
     * @param string|Carbon|null $startDate
     * @param string|Carbon|null $endDate
     * @param string $starTime
     * @param string $endTime
     * @param int $interval
     * @param string $calendarTimeZone
     */
    public function __construct(string|Carbon $startDate = null,
                                string|Carbon $endDate = null,
                                string $starTime = '09:00',
                                string $endTime = '17:00',
                                int    $interval = 30,
                                string $calendarTimeZone = 'UTC')
    {
        $this->startDate = self::createCarbonDateIfNeeded($startDate) ?? new (Carbon::today());
        $this->endDate = self::createCarbonDateIfNeeded($endDate) ?? new (Carbon::today());
        $this->defaultStartTime = $starTime;
        $this->defaultEndTime = $endTime;
        $this->timeZone = $calendarTimeZone;
        $this->interval = $interval;
    }

    /**
     * @param string|Carbon $startDate
     * @return $this
     */
    public function setStartDate(string|Carbon $startDate): self
    {
        $this->startDate = self::createCarbonDateIfNeeded($startDate);

        return $this;
    }

    /**
     * @param string|Carbon $endDate
     * @return $this
     */
    public function setEndDate(string|Carbon $endDate): self
    {
        $this->endDate = self::createCarbonDateIfNeeded($endDate);

        return $this;
    }

    /**
     * @param string $startTime
     * @return $this
     */
    public function setStartTime(string $startTime): self
    {
        $this->defaultStartTime = $startTime;

        return $this;
    }

    /**
     * @param string $endTime
     * @return $this
     */
    public function setEndTime(string $endTime): self
    {
        $this->defaultEndTime = $endTime;

        return $this;
    }

    /**
     * @param string $timeZone
     * @return $this
     */
    public function setCalendarTimeZone(string $timeZone): self
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * Static shortcut to build a calendar.
     * Calendar::build();
     *
     * @param string|Carbon|null $startDate
     * @param string|Carbon|null $endDate
     * @param string $starTime
     * @param string $endTime
     * @param int $interval
     * @param string $calendarTimeZone
     *
     * @return Calendar
     */
    public static function build(string|Carbon $startDate = null,
                                 string|Carbon $endDate = null,
                                 string $starTime = '09:00',
                                 string $endTime = '17:00',
                                 int    $interval = 30,
                                 string $calendarTimeZone = 'UTC'): Calendar
    {
        return (new self($startDate, $endDate, $starTime, $endTime, $interval, $calendarTimeZone))
            ->buildDays();

    }

    /**
     * @param array $days
     * @return $this
     */
    public function addCalendarDays(array $days): self
    {
        foreach ($days as $day) {
            $this->addCalendarDay($day);
        }

        return $this;
    }

    /**
     * @param CalendarDay $day
     * @return $this
     */
    public function addCalendarDay(CalendarDay $day): self
    {
        $this->days[$day->date->format('Y-m-d')] = $day;

        return $this;
    }

    /**
     * @return $this
     */
    public function buildDays(): self
    {
        $dateRange = CarbonPeriod::create($this->startDate, $this->endDate);
        foreach ($dateRange as $date) {
            $dateformat = $date->format('Y-m-d');
            if (!isset($this->days[$dateformat]) || empty($this->days[$dateformat])) {
                $this->days[$date->format('Y-m-d')] = new CalendarDay(
                    $date,
                    $this->defaultStartTime,
                    $this->defaultEndTime
                );
            }
        }

        //Add time slots in each day
        foreach ($this->days as $day)
        {
            $day->buildSlots();
        }

        return $this;
    }

    public function getDay(string|Carbon|int $date): ?CalendarDay
    {
        if (is_numeric($date)) {
            $date = array_keys($this->days)[$date];
        }
        else if (is_object($date) && get_class($date) === Carbon::class) {
            $date = $date->format('Y-m-d');
        }

        return $this->days[$date] ?? null;
    }

    /**
     * @throws \JsonException
     */
    public function toArray()
    {
        return json_decode($this->toJson(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \JsonException
     */
    public function toJson()
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }

}