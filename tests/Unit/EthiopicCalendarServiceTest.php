<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Tests\Unit;

use InvalidArgumentException;
use Mammesat\FilamentEthiopicCalendar\Contracts\CalendarAdapterInterface;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicCalendarService;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the pure date conversion service.
 *
 * These are the same conversion tests from the original EthiopicCalendarTest,
 * now targeting the extracted EthiopicCalendarService.
 */
final class EthiopicCalendarServiceTest extends TestCase
{
    private EthiopicCalendarService $calendar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calendar = new EthiopicCalendarService();
    }

    public function test_it_implements_calendar_adapter_interface(): void
    {
        self::assertInstanceOf(CalendarAdapterInterface::class, $this->calendar);
    }

    public function test_it_converts_gregorian_to_ethiopic(): void
    {
        $converted = $this->calendar->toEthiopic(2023, 9, 12);

        self::assertSame(['year' => 2016, 'month' => 1, 'day' => 1], $converted);
        self::assertSame('2016-01-01', $this->calendar->toEthiopicString('2023-09-12'));
    }

    public function test_it_converts_ethiopic_to_gregorian(): void
    {
        $converted = $this->calendar->toGregorian(2017, 1, 1);

        self::assertSame(['year' => 2024, 'month' => 9, 'day' => 11], $converted);
        self::assertSame('2024-09-11', $this->calendar->toGregorianString('2017-01-01'));
    }

    public function test_it_handles_pagume_for_common_and_leap_years(): void
    {
        self::assertSame('2024-09-10', $this->calendar->toGregorianString('2016-13-05'));
        self::assertNull($this->calendar->toGregorianString('2016-13-06'));

        self::assertSame('2023-09-11', $this->calendar->toGregorianString('2015-13-06'));
    }

    public function test_days_in_ethiopic_month_for_all_month_types(): void
    {
        self::assertSame(30, $this->calendar->daysInEthiopicMonth(2017, 1));
        self::assertSame(5, $this->calendar->daysInEthiopicMonth(2016, 13));
        self::assertSame(6, $this->calendar->daysInEthiopicMonth(2015, 13));
    }

    public function test_days_in_ethiopic_month_throws_on_invalid_month(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->calendar->daysInEthiopicMonth(2017, 14);
    }

    public function test_days_in_ethiopic_month_month_zero_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->calendar->daysInEthiopicMonth(2017, 0);
    }

    public function test_invalid_gregorian_date_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->calendar->toEthiopic(2024, 2, 30);
    }

    public function test_invalid_ethiopic_date_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->calendar->toGregorian(2016, 13, 6);
    }

    public function test_string_conversions_are_null_safe(): void
    {
        self::assertNull($this->calendar->toEthiopicString(null));
        self::assertNull($this->calendar->toEthiopicString(''));
        self::assertNull($this->calendar->toEthiopicString('2024/09/11'));
        self::assertNull($this->calendar->toEthiopicString('2024-2-09'));

        self::assertNull($this->calendar->toGregorianString(null));
        self::assertNull($this->calendar->toGregorianString(''));
        self::assertNull($this->calendar->toGregorianString('2017/01/01'));
        self::assertNull($this->calendar->toGregorianString('2017-13-07'));
    }

    public function test_date_string_validation_helper(): void
    {
        self::assertTrue($this->calendar->isValidEthiopicDateString('2017-01-01'));
        self::assertFalse($this->calendar->isValidEthiopicDateString('2016-13-06'));
        self::assertFalse($this->calendar->isValidEthiopicDateString('invalid'));
    }

    public function test_known_new_year_boundaries_are_correct(): void
    {
        self::assertSame('2015-13-06', $this->calendar->toEthiopicString('2023-09-11'));
        self::assertSame('2016-01-01', $this->calendar->toEthiopicString('2023-09-12'));
        self::assertSame('2016-13-05', $this->calendar->toEthiopicString('2024-09-10'));
        self::assertSame('2017-01-01', $this->calendar->toEthiopicString('2024-09-11'));
    }

    public function test_invalid_string_formats_are_rejected_strictly(): void
    {
        self::assertNull($this->calendar->toEthiopicString(' 2024-09-11 '));
        self::assertNull($this->calendar->toGregorianString('2017-1-01'));
        self::assertNull($this->calendar->toGregorianString('2017-01-1'));
        self::assertNull($this->calendar->toGregorianString('abcd-ef-gh'));
    }

    public function test_round_trip_remains_stable_on_known_boundary_dates(): void
    {
        $knownGregorianDates = [
            '2023-09-11',
            '2023-09-12',
            '2024-09-10',
            '2024-09-11',
            '2020-02-29',
        ];

        foreach ($knownGregorianDates as $gregorian) {
            $ethiopic = $this->calendar->toEthiopicString($gregorian);

            self::assertNotNull($ethiopic);
            self::assertSame($gregorian, $this->calendar->toGregorianString($ethiopic));
        }
    }

    public function test_pagume_round_trips_correctly_and_does_not_overflow(): void
    {
        $testCases = [
            '2018-13-01' => '2026-09-06',
            '2018-13-05' => '2026-09-10',
            '2011-13-06' => '2019-09-11',
        ];

        foreach ($testCases as $ethiopic => $gregorian) {
            self::assertSame($gregorian, $this->calendar->toGregorianString($ethiopic));
            self::assertSame($ethiopic, $this->calendar->toEthiopicString($gregorian));
        }
    }

    public function test_century_boundary_dates(): void
    {
        $ethiopic = $this->calendar->toEthiopicString('1900-03-01');
        self::assertNotNull($ethiopic);
        self::assertSame('1900-03-01', $this->calendar->toGregorianString($ethiopic));

        $ethiopic2 = $this->calendar->toEthiopicString('2100-01-01');
        self::assertNotNull($ethiopic2);
        self::assertSame('2100-01-01', $this->calendar->toGregorianString($ethiopic2));

        $ethiopic3 = $this->calendar->toEthiopicString('2000-02-29');
        self::assertNotNull($ethiopic3);
        self::assertSame('2000-02-29', $this->calendar->toGregorianString($ethiopic3));
    }
}
