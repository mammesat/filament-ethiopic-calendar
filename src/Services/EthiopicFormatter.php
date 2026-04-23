<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Services;

use Carbon\Carbon;
use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Enums\TimeMode;
use Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig;
use Mammesat\FilamentEthiopicCalendar\Support\LocaleManager;

/**
 * ═══════════════════════════════════════════════
 * SINGLE SOURCE OF TRUTH for all display output.
 * ═══════════════════════════════════════════════
 *
 * Every UI component (picker, column, infolist entry) MUST route
 * all formatting through this class. Zero formatting logic elsewhere.
 */
final class EthiopicFormatter
{
    public function __construct(
        private readonly EthiopicCalendarService $calendar,
        private readonly EthiopicTimeService $timeService,
        private readonly LocaleManager $locale,
    ) {}

    // ──────────────────────────────────────────────
    // Public formatting API
    // ──────────────────────────────────────────────

    /**
     * Format a Gregorian date string for Ethiopic display.
     */
    public function formatDate(?string $gregorianDate, DisplayMode|string|null $mode = null): ?string
    {
        if ($gregorianDate === null || trim($gregorianDate) === '') {
            return null;
        }

        $mode = $this->resolveDisplayMode($mode);

        if ($mode === DisplayMode::CleanGregorian) {
            try {
                return Carbon::parse($gregorianDate)->format('M, j Y');
            } catch (\Throwable) {
                return $gregorianDate;
            }
        }

        $ethiopicString = $this->calendar->toEthiopicString($gregorianDate);

        if ($ethiopicString === null) {
            return null;
        }

        [$year, $month, $day] = explode('-', $ethiopicString);

        $dayOfWeek = Carbon::parse($gregorianDate)->dayOfWeekIso;

        $monthName = $this->getFormattedMonthName((int) $month, $mode);
        $dayName = $this->getFormattedDayName($dayOfWeek, $mode);

        return match ($mode) {
            DisplayMode::AmharicCombined,
            DisplayMode::TransliterationCombined,
            DisplayMode::Hybrid => "{$monthName} {$day}, {$year} / {$dayName}",

            DisplayMode::CompactAmharic => "{$monthName} {$day}, {$year} {$dayName}",

            DisplayMode::AmharicNoWeek,
            DisplayMode::TransliterationNoWeek => "{$monthName} {$day}, {$year}",

            DisplayMode::CleanGregorian => $gregorianDate,
        };
    }

    /**
     * Format a time string for display per the given time mode.
     *
     * @param  string|null  $time  Time in HH:MM or HH:MM:SS format (Gregorian 24h)
     */
    public function formatTime(?string $time, TimeMode|string|null $timeMode = null): ?string
    {
        if ($time === null || trim($time) === '') {
            return null;
        }

        $timeMode = $this->resolveTimeMode($timeMode);
        $parsed = $this->timeService->parseTimeString($time);

        if ($parsed === null) {
            return null;
        }

        $hour = $parsed['hour'];
        $minute = $parsed['minute'];

        return match ($timeMode) {
            TimeMode::Gregorian => $this->formatGregorianTime($hour, $minute),
            TimeMode::Ethiopian => $this->formatEthiopianTimeOnly($hour, $minute),
            TimeMode::Dual      => $this->formatDualTime($hour, $minute),
        };
    }

    /**
     * Format a full datetime string (Y-m-d H:i:s or Y-m-d H:i) for display.
     */
    public function formatDateTime(
        ?string $gregorianDateTime,
        DisplayMode|string|null $displayMode = null,
        TimeMode|string|null $timeMode = null,
    ): ?string {
        if ($gregorianDateTime === null || trim($gregorianDateTime) === '') {
            return null;
        }

        // Split date and time parts
        $parts = preg_split('/\s+/', trim($gregorianDateTime), 2);
        $datePart = $parts[0] ?? null;
        $timePart = $parts[1] ?? null;

        $formattedDate = $this->formatDate($datePart, $displayMode);

        if ($formattedDate === null) {
            return null;
        }

        // If no time part, return date only
        if ($timePart === null || trim($timePart) === '') {
            return $formattedDate;
        }

        $formattedTime = $this->formatTime($timePart, $timeMode);

        if ($formattedTime === null) {
            return $formattedDate;
        }

        return $formattedDate . ' ' . $formattedTime;
    }

    // ──────────────────────────────────────────────
    // Public localized name accessors
    // ──────────────────────────────────────────────

    /**
     * Get a localized month name for the given display mode.
     */
    public function getMonthName(int $monthIndex, ?string $locale = null): string
    {
        return $this->locale->getMonthName($monthIndex, $locale);
    }

    /**
     * Get a localized day name.
     */
    public function getDayName(int $dayIndex, string $length = 'long', ?string $locale = null): string
    {
        return $this->locale->getDayName($dayIndex, $length, $locale);
    }

    // ──────────────────────────────────────────────
    // Static convenience (for table/infolist usage)
    // ──────────────────────────────────────────────

    /**
     * Static shorthand for formatDate().
     */
    public static function format(?string $gregorianDate, DisplayMode|string|null $mode = null): ?string
    {
        return app(static::class)->formatDate($gregorianDate, $mode);
    }

    /**
     * Static shorthand for formatDateTime().
     */
    public static function dateTime(
        ?string $gregorianDateTime,
        DisplayMode|string|null $displayMode = null,
        TimeMode|string|null $timeMode = null,
    ): ?string {
        return app(static::class)->formatDateTime($gregorianDateTime, $displayMode, $timeMode);
    }

    // ──────────────────────────────────────────────
    // Internal formatting strategies
    // ──────────────────────────────────────────────

    /**
     * Format as standard Gregorian 12h AM/PM.
     */
    private function formatGregorianTime(int $hour, int $minute): string
    {
        $period = $hour >= 12 ? 'PM' : 'AM';
        $displayHour = $hour % 12;

        if ($displayHour === 0) {
            $displayHour = 12;
        }

        return sprintf('%d:%02d %s', $displayHour, $minute, $period);
    }

    /**
     * Format as Ethiopian time only (no Gregorian shown).
     */
    private function formatEthiopianTimeOnly(int $gregorianHour, int $minute): string
    {
        return $this->timeService->formatEthiopianTime($gregorianHour, $minute);
    }

    /**
     * Format as dual display: "10:00 AM (ጠዋት 4:00)"
     *
     * Template is enforced from config: dual_time_format.
     */
    private function formatDualTime(int $gregorianHour, int $minute): string
    {
        $gregorianFormatted = $this->formatGregorianTime($gregorianHour, $minute);
        $ethiopianFormatted = $this->timeService->formatEthiopianTime($gregorianHour, $minute);

        $template = EthiopicConfig::dualTimeFormat();

        return str_replace(
            [':gregorian', ':ethiopian'],
            [$gregorianFormatted, $ethiopianFormatted],
            $template,
        );
    }

    // ──────────────────────────────────────────────
    // Month/Day name formatting per DisplayMode
    // ──────────────────────────────────────────────

    private function getFormattedMonthName(int $monthIndex, DisplayMode $mode): string
    {
        $locale = $this->localeForMode($mode);

        $name = $this->locale->getMonthName($monthIndex, $locale);

        if ($mode === DisplayMode::Hybrid) {
            $amharic = $this->locale->getMonthName($monthIndex, 'am');
            $english = $this->locale->getMonthName($monthIndex, 'en');

            return "{$english} ({$amharic})";
        }

        return $name;
    }

    private function getFormattedDayName(int $dayIndex, DisplayMode $mode): string
    {
        if ($mode === DisplayMode::AmharicNoWeek || $mode === DisplayMode::TransliterationNoWeek) {
            return '';
        }

        $locale = $this->localeForMode($mode);

        if ($mode === DisplayMode::Hybrid) {
            $amharic = $this->locale->getDayName($dayIndex, 'long', 'am');
            $english = $this->locale->getDayName($dayIndex, 'long', 'en');

            return "{$english} ({$amharic})";
        }

        return $this->locale->getDayName($dayIndex, 'long', $locale);
    }

    /**
     * Map a DisplayMode to the appropriate locale string.
     */
    private function localeForMode(DisplayMode $mode): string
    {
        return match ($mode) {
            DisplayMode::AmharicCombined,
            DisplayMode::CompactAmharic,
            DisplayMode::AmharicNoWeek => 'am',

            DisplayMode::TransliterationCombined,
            DisplayMode::TransliterationNoWeek,
            DisplayMode::CleanGregorian => 'en',

            DisplayMode::Hybrid => 'en', // Primary locale; hybrid builds both internally
        };
    }

    // ──────────────────────────────────────────────
    // Mode resolution
    // ──────────────────────────────────────────────

    private function resolveDisplayMode(DisplayMode|string|null $mode): DisplayMode
    {
        if ($mode instanceof DisplayMode) {
            return $mode;
        }

        if (is_string($mode)) {
            // Try the simple mode API first ('ethiopic', 'gregorian', 'dual')
            if (in_array($mode, ['ethiopic', 'gregorian', 'dual'], true)) {
                return DisplayMode::fromSimpleMode($mode);
            }

            return DisplayMode::tryFrom($mode) ?? DisplayMode::fromConfig();
        }

        return DisplayMode::fromConfig();
    }

    private function resolveTimeMode(TimeMode|string|null $mode): TimeMode
    {
        if ($mode instanceof TimeMode) {
            return $mode;
        }

        if (is_string($mode)) {
            $resolved = TimeMode::tryFrom($mode);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        return EthiopicConfig::timeMode();
    }
}
