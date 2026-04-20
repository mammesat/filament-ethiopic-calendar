<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicDatePicker\Tests\Unit;

use InvalidArgumentException;
use Mammesat\FilamentEthiopicDatePicker\Enums\DisplayMode;
use Mammesat\FilamentEthiopicDatePicker\Services\EthiopicCalendar;
use PHPUnit\Framework\TestCase;

final class EthiopicCalendarTest extends TestCase
{
    private EthiopicCalendar $calendar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calendar = new EthiopicCalendar();
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
            '2018-13-01' => '2026-09-06', // Pagume 1, 2018 (Common)
            '2018-13-05' => '2026-09-10', // Pagume 5, 2018 (Common)
            '2011-13-06' => '2019-09-11', // Pagume 6, 2011 (Leap year)
        ];

        foreach ($testCases as $ethiopic => $gregorian) {
            // Verify round trips
            self::assertSame($gregorian, $this->calendar->toGregorianString($ethiopic));
            self::assertSame($ethiopic, $this->calendar->toEthiopicString($gregorian));
        }
    }

    // ──────────────────────────────────────────────
    // New tests: Display formatting
    // ──────────────────────────────────────────────

    public function test_format_display_label_amharic_combined_mode(): void
    {
        $result = $this->calendar->formatDisplayLabel('2023-09-12', DisplayMode::AmharicCombined);

        self::assertNotNull($result);
        self::assertStringContainsString('መስከረም', $result);
        self::assertStringContainsString('2016', $result);
        self::assertStringContainsString('01', $result);
    }

    public function test_format_display_label_transliteration_mode(): void
    {
        $result = $this->calendar->formatDisplayLabel('2023-09-12', DisplayMode::TransliterationCombined);

        self::assertNotNull($result);
        self::assertStringContainsString('Meskerem', $result);
        self::assertStringContainsString('2016', $result);
    }

    public function test_format_display_label_hybrid_mode(): void
    {
        $result = $this->calendar->formatDisplayLabel('2023-09-12', DisplayMode::Hybrid);

        self::assertNotNull($result);
        // Hybrid shows both English and Amharic: "Meskerem (መስከረም)"
        self::assertStringContainsString('Meskerem', $result);
        self::assertStringContainsString('መስከረም', $result);
    }

    public function test_format_display_label_compact_amharic_mode(): void
    {
        $result = $this->calendar->formatDisplayLabel('2023-09-12', DisplayMode::CompactAmharic);

        self::assertNotNull($result);
        self::assertStringContainsString('መስከረም', $result);
        // Compact mode uses space (no slash separator)
        self::assertStringNotContainsString('/', $result);
    }

    public function test_format_display_label_clean_gregorian_mode(): void
    {
        $result = $this->calendar->formatDisplayLabel('2023-09-12', DisplayMode::CleanGregorian);

        // Clean gregorian just returns the input as-is
        self::assertSame('2023-09-12', $result);
    }

    public function test_format_display_label_null_input(): void
    {
        self::assertNull($this->calendar->formatDisplayLabel(null, DisplayMode::AmharicCombined));
        self::assertNull($this->calendar->formatDisplayLabel('', DisplayMode::AmharicCombined));
        self::assertNull($this->calendar->formatDisplayLabel('   ', DisplayMode::AmharicCombined));
    }

    public function test_get_display_month_name_all_modes(): void
    {
        // Month 1 = Meskerem / መስከረም
        self::assertSame('መስከረም', $this->calendar->getDisplayMonthName(1, DisplayMode::AmharicCombined));
        self::assertSame('Meskerem', $this->calendar->getDisplayMonthName(1, DisplayMode::TransliterationCombined));
        self::assertSame('Meskerem (መስከረም)', $this->calendar->getDisplayMonthName(1, DisplayMode::Hybrid));
        self::assertSame('መስከረም', $this->calendar->getDisplayMonthName(1, DisplayMode::CompactAmharic));
        self::assertSame('Meskerem', $this->calendar->getDisplayMonthName(1, DisplayMode::CleanGregorian));

        // Month 13 = Pagume / ጳጉሜ
        self::assertSame('ጳጉሜ', $this->calendar->getDisplayMonthName(13, DisplayMode::AmharicCombined));
        self::assertSame('Pagume', $this->calendar->getDisplayMonthName(13, DisplayMode::TransliterationCombined));
    }

    public function test_get_display_day_name_all_modes(): void
    {
        // Day 1 (Monday) = ሰኞ / Monday
        self::assertSame('ሰኞ', $this->calendar->getDisplayDayName(1, DisplayMode::AmharicCombined));
        self::assertSame('Monday', $this->calendar->getDisplayDayName(1, DisplayMode::TransliterationCombined));
        self::assertSame('Monday (ሰኞ)', $this->calendar->getDisplayDayName(1, DisplayMode::Hybrid));

        // Sunday (0 from Carbon, maps to 7 internally)
        self::assertSame('እሁድ', $this->calendar->getDisplayDayName(0, DisplayMode::AmharicCombined));
        self::assertSame('Sunday', $this->calendar->getDisplayDayName(0, DisplayMode::TransliterationCombined));
    }

    public function test_century_boundary_dates(): void
    {
        // 1900-03-01 — Gregorian non-leap year (divisible by 100 but not 400)
        $ethiopic = $this->calendar->toEthiopicString('1900-03-01');
        self::assertNotNull($ethiopic);
        $roundTripped = $this->calendar->toGregorianString($ethiopic);
        self::assertSame('1900-03-01', $roundTripped);

        // 2100-01-01 — future century boundary
        $ethiopic2 = $this->calendar->toEthiopicString('2100-01-01');
        self::assertNotNull($ethiopic2);
        $roundTripped2 = $this->calendar->toGregorianString($ethiopic2);
        self::assertSame('2100-01-01', $roundTripped2);

        // 2000-02-29 — Gregorian leap year (divisible by 400)
        $ethiopic3 = $this->calendar->toEthiopicString('2000-02-29');
        self::assertNotNull($ethiopic3);
        $roundTripped3 = $this->calendar->toGregorianString($ethiopic3);
        self::assertSame('2000-02-29', $roundTripped3);
    }

    public function test_days_in_ethiopic_month_month_zero_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->calendar->daysInEthiopicMonth(2017, 0);
    }

    // ──────────────────────────────────────────────
    // New tests: No-week display modes
    // ──────────────────────────────────────────────

    public function test_format_display_label_amharic_no_week_mode(): void
    {
        // 2023-09-12 = Meskerem 01, 2016 (Tuesday)
        $result = $this->calendar->formatDisplayLabel('2023-09-12', DisplayMode::AmharicNoWeek);

        self::assertNotNull($result);
        self::assertSame('መስከረም 01, 2016', $result);
        // Must NOT contain weekday separator or day name
        self::assertStringNotContainsString('/', $result);
        self::assertStringNotContainsString('ማክሰኞ', $result); // Tuesday in Amharic
    }

    public function test_format_display_label_transliteration_no_week_mode(): void
    {
        // 2023-09-12 = Meskerem 01, 2016 (Tuesday)
        $result = $this->calendar->formatDisplayLabel('2023-09-12', DisplayMode::TransliterationNoWeek);

        self::assertNotNull($result);
        self::assertSame('Meskerem 01, 2016', $result);
        // Must NOT contain weekday separator or day name
        self::assertStringNotContainsString('/', $result);
        self::assertStringNotContainsString('Tuesday', $result);
    }

    public function test_get_display_month_name_no_week_modes(): void
    {
        // AmharicNoWeek uses Amharic month names
        self::assertSame('መስከረም', $this->calendar->getDisplayMonthName(1, DisplayMode::AmharicNoWeek));
        self::assertSame('ጳጉሜ', $this->calendar->getDisplayMonthName(13, DisplayMode::AmharicNoWeek));

        // TransliterationNoWeek uses English transliteration
        self::assertSame('Meskerem', $this->calendar->getDisplayMonthName(1, DisplayMode::TransliterationNoWeek));
        self::assertSame('Pagume', $this->calendar->getDisplayMonthName(13, DisplayMode::TransliterationNoWeek));
    }

    public function test_get_display_day_name_no_week_modes_return_empty(): void
    {
        // No-week modes should return empty string for day names
        self::assertSame('', $this->calendar->getDisplayDayName(1, DisplayMode::AmharicNoWeek));
        self::assertSame('', $this->calendar->getDisplayDayName(0, DisplayMode::AmharicNoWeek)); // Sunday via Carbon
        self::assertSame('', $this->calendar->getDisplayDayName(5, DisplayMode::TransliterationNoWeek));
    }

    public function test_format_display_label_no_week_null_input(): void
    {
        self::assertNull($this->calendar->formatDisplayLabel(null, DisplayMode::AmharicNoWeek));
        self::assertNull($this->calendar->formatDisplayLabel('', DisplayMode::TransliterationNoWeek));
        self::assertNull($this->calendar->formatDisplayLabel('   ', DisplayMode::AmharicNoWeek));
    }
}

