<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Support;

use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Enums\TimeMode;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter as FormatterService;

/**
 * Thin static facade for the EthiopicFormatter service.
 *
 * This class contains ZERO formatting logic. Every call delegates
 * entirely to Services\EthiopicFormatter — the single source of truth.
 *
 * Usage:
 *   EthiopicFormatter::formatDate('2023-09-12', 'ethiopic');
 *   EthiopicFormatter::formatDateTime('2023-09-12 10:00:00', 'ethiopic', 'ethiopian');
 *   EthiopicFormatter::formatEthiopianTime('10:00');
 */
final class EthiopicFormatter
{
    /**
     * Format a Gregorian date string for Ethiopic display.
     */
    public static function formatDate(?string $gregorianDate, DisplayMode|string|null $mode = null): ?string
    {
        return self::service()->formatDate($gregorianDate, $mode);
    }

    /**
     * Format a full datetime string for display.
     */
    public static function formatDateTime(
        ?string $gregorianDateTime,
        DisplayMode|string|null $displayMode = null,
        TimeMode|string|null $timeMode = null,
    ): ?string {
        return self::service()->formatDateTime($gregorianDateTime, $displayMode, $timeMode);
    }

    /**
     * Format a time string using the Ethiopian time system.
     *
     * @param  string|null  $time  Time in HH:MM or HH:MM:SS format (Gregorian 24h)
     */
    public static function formatEthiopianTime(?string $time): ?string
    {
        return self::service()->formatEthiopianTime($time);
    }

    /**
     * Format a time string per the given time mode.
     */
    public static function formatTime(?string $time, TimeMode|string|null $timeMode = null): ?string
    {
        return self::service()->formatTime($time, $timeMode);
    }

    /**
     * Get a localized month name.
     */
    public static function monthName(int $monthIndex, ?string $locale = null): string
    {
        return self::service()->getMonthName($monthIndex, $locale);
    }

    /**
     * Get a localized day name.
     */
    public static function dayName(int $dayIndex, string $length = 'long', ?string $locale = null): string
    {
        return self::service()->getDayName($dayIndex, $length, $locale);
    }

    /**
     * Resolve the formatter service from the container.
     */
    private static function service(): FormatterService
    {
        return app(FormatterService::class);
    }
}
