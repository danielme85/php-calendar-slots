<?php
declare(strict_types=1);

use Danielme85\PhpCalendarSlots\Calendar;
use Danielme85\PhpCalendarSlots\CalendarDay;
use Danielme85\PhpCalendarSlots\CalendarDaySlot;
use PHPUnit\Framework\TestCase;
use Carbon\Carbon;

final class CalendarTest extends TestCase
{
    protected $tz = 'America/New_York';

    public function testCanAddDays()
    {
        $calendar = new Calendar();
        $calendar->addCalendarDays([
            new CalendarDay(Carbon::today($this->tz), '08:00', '16:00'),
            new CalendarDay(Carbon::tomorrow($this->tz), '08:00', '16:00')
        ]);

        $calendar->buildDays();
        $this->assertNotEmpty($calendar->days);

    }

}
