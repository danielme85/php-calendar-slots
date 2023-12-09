<?php

namespace Danielme85\PhpCalendarSlots;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Calendar
{
    /**
     * @var int
     */
    public int $interval;

    /**
     * @var string
     */
    public string $startDate;

    /**
     * @var string
     */
    public string $endDate;

    public string $defaultStartTime;

    public string $defaultEndTime;

    /**
     * @var string
     */
    public string $timeZone;

    /**
     * @var array
     */
    public array $days;

    public function __construct(string $startDate = null,
                                string $endDate = null,
                                string $starTime = '09:00',
                                string $endTime = '17:00',
                                int    $interval = 30,
                                string $calendarTimeZone = 'UTC')
    {
        $this->startDate = $startDate ?? Carbon::today()->format('Y-m-d');
        $this->endDate = $endDate ?? Carbon::today()->format('Y-m-d');
        $this->defaultStartTime = $starTime;
        $this->defaultEndTime = $endTime;
        $this->timeZone = $calendarTimeZone;
        $this->interval = $interval;
    }

    /**
     * @param string $startDate
     * @return $this
     */
    public function setStartDate(string $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @param string $endDate
     * @return $this
     */
    public function setEndDate(string $endDate): self
    {
        $this->endDate = $endDate;

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
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string $starTime
     * @param string $endTime
     * @param int $interval
     * @param string $calendarTimeZone
     *
     * @return Calendar
     */
    public static function build(string $startDate = null,
                                 string $endDate = null,
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
        $dateRange = CarbonPeriod::create(
            Carbon::parse($this->startDate, $this->timeZone),
            Carbon::parse($this->endDate, $this->timeZone)
        );
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

        foreach ($this->days as $day)
        {
            $day->buildSlots();
        }

        return $this;
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