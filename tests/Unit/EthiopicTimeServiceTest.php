<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Tests\Unit;

use InvalidArgumentException;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicTimeService;
use Mammesat\FilamentEthiopicCalendar\Support\LocaleManager;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Ethiopian Time System Engine.
 */
final class EthiopicTimeServiceTest extends TestCase
{
    private EthiopicTimeService $timeService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock LocaleManager that returns raw period keys
        // (no Laravel app needed for unit tests)
        $locale = $this->createMock(LocaleManager::class);
        $locale->method('getDayPeriod')->willReturnCallback(
            fn (string $key) => match ($key) {
                'morning'   => 'ጠዋት',
                'afternoon' => 'ከሰዓት',
                'evening'   => 'ማታ',
                'night'     => 'ለሊት',
                default     => $key,
            }
        );

        $this->timeService = new EthiopicTimeService($locale);
    }

    // ──────────────────────────────────────────────
    // Gregorian → Ethiopian conversion
    // ──────────────────────────────────────────────

    public function test_morning_period_conversion(): void
    {
        // 06:00 Gregorian → 12:00 ጠዋት
        $result = $this->timeService->toEthiopian(6, 0);
        self::assertSame(12, $result['hour']);
        self::assertSame(0, $result['minute']);
        self::assertSame('morning', $result['periodKey']);
        self::assertSame('ጠዋት', $result['period']);

        // 07:15 Gregorian → 1:15 ጠዋት
        $result = $this->timeService->toEthiopian(7, 15);
        self::assertSame(1, $result['hour']);
        self::assertSame(15, $result['minute']);
        self::assertSame('morning', $result['periodKey']);

        // 11:59 Gregorian → 5:59 ጠዋት
        $result = $this->timeService->toEthiopian(11, 59);
        self::assertSame(5, $result['hour']);
        self::assertSame(59, $result['minute']);
        self::assertSame('morning', $result['periodKey']);
    }

    public function test_afternoon_period_conversion(): void
    {
        // 12:00 Gregorian → 6:00 ከሰዓት
        $result = $this->timeService->toEthiopian(12, 0);
        self::assertSame(6, $result['hour']);
        self::assertSame(0, $result['minute']);
        self::assertSame('afternoon', $result['periodKey']);
        self::assertSame('ከሰዓት', $result['period']);

        // 12:45 Gregorian → 6:45 ከሰዓት
        $result = $this->timeService->toEthiopian(12, 45);
        self::assertSame(6, $result['hour']);
        self::assertSame(45, $result['minute']);
        self::assertSame('afternoon', $result['periodKey']);

        // 17:59 Gregorian → 11:59 ከሰዓት
        $result = $this->timeService->toEthiopian(17, 59);
        self::assertSame(11, $result['hour']);
        self::assertSame(59, $result['minute']);
        self::assertSame('afternoon', $result['periodKey']);
    }

    public function test_evening_period_conversion(): void
    {
        // 18:00 Gregorian → 12:00 ማታ
        $result = $this->timeService->toEthiopian(18, 0);
        self::assertSame(12, $result['hour']);
        self::assertSame(0, $result['minute']);
        self::assertSame('evening', $result['periodKey']);
        self::assertSame('ማታ', $result['period']);

        // 19:00 Gregorian → 1:00 ማታ
        $result = $this->timeService->toEthiopian(19, 0);
        self::assertSame(1, $result['hour']);
        self::assertSame(0, $result['minute']);
        self::assertSame('evening', $result['periodKey']);

        // 23:59 Gregorian → 5:59 ማታ
        $result = $this->timeService->toEthiopian(23, 59);
        self::assertSame(5, $result['hour']);
        self::assertSame(59, $result['minute']);
        self::assertSame('evening', $result['periodKey']);
    }

    public function test_night_period_conversion(): void
    {
        // 00:00 Gregorian → 6:00 ለሊት
        $result = $this->timeService->toEthiopian(0, 0);
        self::assertSame(6, $result['hour']);
        self::assertSame(0, $result['minute']);
        self::assertSame('night', $result['periodKey']);
        self::assertSame('ለሊት', $result['period']);

        // 02:30 Gregorian → 8:30 ለሊት
        $result = $this->timeService->toEthiopian(2, 30);
        self::assertSame(8, $result['hour']);
        self::assertSame(30, $result['minute']);
        self::assertSame('night', $result['periodKey']);

        // 05:59 Gregorian → 11:59 ለሊት
        $result = $this->timeService->toEthiopian(5, 59);
        self::assertSame(11, $result['hour']);
        self::assertSame(59, $result['minute']);
        self::assertSame('night', $result['periodKey']);
    }

    // ──────────────────────────────────────────────
    // Ethiopian → Gregorian conversion
    // ──────────────────────────────────────────────

    public function test_ethiopian_to_gregorian_round_trip_for_every_hour(): void
    {
        for ($gregHour = 0; $gregHour < 24; $gregHour++) {
            $minute = 30;
            $ethiopian = $this->timeService->toEthiopian($gregHour, $minute);

            $gregorian = $this->timeService->toGregorian(
                $ethiopian['hour'],
                $ethiopian['minute'],
                $ethiopian['periodKey'],
            );

            self::assertSame($gregHour, $gregorian['hour'], "Failed round-trip for Gregorian hour {$gregHour}");
            self::assertSame($minute, $gregorian['minute'], "Failed minute round-trip for Gregorian hour {$gregHour}");
        }
    }

    // ──────────────────────────────────────────────
    // Format Ethiopian time
    // ──────────────────────────────────────────────

    public function test_format_ethiopian_time_matches_expected_output(): void
    {
        self::assertSame('ጠዋት 12:00', $this->timeService->formatEthiopianTime(6, 0));
        self::assertSame('ጠዋት 1:05', $this->timeService->formatEthiopianTime(7, 5));
        self::assertSame('ጠዋት 1:15', $this->timeService->formatEthiopianTime(7, 15));
        self::assertSame('ከሰዓት 6:00', $this->timeService->formatEthiopianTime(12, 0));
        self::assertSame('ከሰዓት 6:45', $this->timeService->formatEthiopianTime(12, 45));
        self::assertSame('ማታ 12:00', $this->timeService->formatEthiopianTime(18, 0));
        self::assertSame('ማታ 1:00', $this->timeService->formatEthiopianTime(19, 0));
        self::assertSame('ለሊት 6:00', $this->timeService->formatEthiopianTime(0, 0));
        self::assertSame('ለሊት 8:30', $this->timeService->formatEthiopianTime(2, 30));
    }

    // ──────────────────────────────────────────────
    // Time string parsing
    // ──────────────────────────────────────────────

    public function test_parse_time_string_valid_inputs(): void
    {
        self::assertSame(['hour' => 6, 'minute' => 0], $this->timeService->parseTimeString('06:00'));
        self::assertSame(['hour' => 12, 'minute' => 45], $this->timeService->parseTimeString('12:45'));
        self::assertSame(['hour' => 0, 'minute' => 0], $this->timeService->parseTimeString('00:00'));
        self::assertSame(['hour' => 23, 'minute' => 59], $this->timeService->parseTimeString('23:59'));
        self::assertSame(['hour' => 7, 'minute' => 15], $this->timeService->parseTimeString('07:15:30')); // with seconds
    }

    public function test_parse_time_string_invalid_inputs(): void
    {
        self::assertNull($this->timeService->parseTimeString(null));
        self::assertNull($this->timeService->parseTimeString(''));
        self::assertNull($this->timeService->parseTimeString('not-a-time'));
        self::assertNull($this->timeService->parseTimeString('24:00'));
        self::assertNull($this->timeService->parseTimeString('12:60'));
    }

    // ──────────────────────────────────────────────
    // Day period key resolution
    // ──────────────────────────────────────────────

    public function test_day_period_key_for_all_hours(): void
    {
        // Night: 0-5
        for ($h = 0; $h <= 5; $h++) {
            self::assertSame('night', $this->timeService->getDayPeriodKey($h));
        }

        // Morning: 6-11
        for ($h = 6; $h <= 11; $h++) {
            self::assertSame('morning', $this->timeService->getDayPeriodKey($h));
        }

        // Afternoon: 12-17
        for ($h = 12; $h <= 17; $h++) {
            self::assertSame('afternoon', $this->timeService->getDayPeriodKey($h));
        }

        // Evening: 18-23
        for ($h = 18; $h <= 23; $h++) {
            self::assertSame('evening', $this->timeService->getDayPeriodKey($h));
        }
    }

    // ──────────────────────────────────────────────
    // Validation
    // ──────────────────────────────────────────────

    public function test_invalid_gregorian_hour_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->timeService->toEthiopian(24, 0);
    }

    public function test_invalid_minute_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->timeService->toEthiopian(12, 60);
    }

    public function test_invalid_ethiopian_hour_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->timeService->toGregorian(0, 0, 'morning');
    }

    public function test_invalid_ethiopian_hour_13_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->timeService->toGregorian(13, 0, 'morning');
    }
}
