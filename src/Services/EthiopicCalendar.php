<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Services;

use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;

/**
 * Backward-compatible facade over the new service layer.
 *
 * @deprecated This class is preserved for backward compatibility.
 *             Use EthiopicCalendarService, EthiopicTimeService, and EthiopicFormatter directly.
 *
 * All existing public methods continue to work by delegating to the appropriate new service.
 * This class will be removed in v2.0.
 */
final class EthiopicCalendar
{
    public const DATE_FORMAT = '%04d-%02d-%02d';

    private ?EthiopicCalendarService $calendarService = null;

    private ?EthiopicTimeService $timeService = null;

    private ?EthiopicFormatter $formatter = null;

    // ──────────────────────────────────────────────
    // Calendar conversion (delegates to EthiopicCalendarService)
    // ──────────────────────────────────────────────

    /**
     * @return array{year: int, month: int, day: int}
     */
    public function toEthiopic(int $year, int $month, int $day): array
    {
        return $this->calendar()->toEthiopic($year, $month, $day);
    }

    /**
     * @return array{year: int, month: int, day: int}
     */
    public function toGregorian(int $year, int $month, int $day): array
    {
        return $this->calendar()->toGregorian($year, $month, $day);
    }

    public function toEthiopicString(?string $gregorian): ?string
    {
        return $this->calendar()->toEthiopicString($gregorian);
    }

    public function toGregorianString(?string $ethiopic): ?string
    {
        return $this->calendar()->toGregorianString($ethiopic);
    }

    public function isValidEthiopicDateString(?string $ethiopic): bool
    {
        return $this->calendar()->isValidEthiopicDateString($ethiopic);
    }

    public function daysInEthiopicMonth(int $year, int $month): int
    {
        return $this->calendar()->daysInEthiopicMonth($year, $month);
    }

    // ──────────────────────────────────────────────
    // Display formatting (delegates to EthiopicFormatter)
    // ──────────────────────────────────────────────

    public function getDisplayMonthName(int $monthIndex, DisplayMode|string|null $mode = null): string
    {
        return $this->formatter()->formatDate(
            // Use the formatter's month name resolution
            null,
            $mode,
        ) ?? $this->getMonthNameDirect($monthIndex, $mode);
    }

    public function getDisplayDayName(int $dayIndex, DisplayMode|string|null $mode = null): string
    {
        return $this->getDayNameDirect($dayIndex, $mode);
    }

    public function formatDisplayLabel(?string $gregorian, DisplayMode|string|null $mode = null): ?string
    {
        return $this->formatter()->formatDate($gregorian, $mode);
    }

    public function formatEthiopianTime(?string $time): ?string
    {
        if ($time === null) {
            return null;
        }

        $time = trim($time);

        if ($time === '') {
            return null;
        }

        $parsed = $this->time()->parseTimeString($time);

        if ($parsed === null) {
            return null;
        }

        return $this->time()->formatEthiopianTime($parsed['hour'], $parsed['minute']);
    }

    // ──────────────────────────────────────────────
    // Static convenience methods (backward compatible)
    // ──────────────────────────────────────────────

    public static function formatEthiopicDisplay(?string $gregorianDate, DisplayMode|string|null $mode = null): ?string
    {
        return app(EthiopicFormatter::class)->formatDate($gregorianDate, $mode);
    }

    public static function getMonthName(int $month, DisplayMode|string|null $mode = null): string
    {
        return app(static::class)->getDisplayMonthName($month, $mode);
    }

    public static function getDayName(int $dayIndex, DisplayMode|string|null $mode = null): string
    {
        return app(static::class)->getDisplayDayName($dayIndex, $mode);
    }

    // ──────────────────────────────────────────────
    // Internal helpers for month/day name resolution
    // ──────────────────────────────────────────────

    private function getMonthNameDirect(int $monthIndex, DisplayMode|string|null $mode = null): string
    {
        $mode = $this->resolveMode($mode);

        $locale = match ($mode) {
            DisplayMode::TransliterationCombined,
            DisplayMode::TransliterationNoWeek,
            DisplayMode::CleanGregorian => 'en',
            default => 'am',
        };

        if ($mode === DisplayMode::Hybrid) {
            $english = $this->formatter()->getMonthName($monthIndex, 'en');
            $amharic = $this->formatter()->getMonthName($monthIndex, 'am');

            return "{$english} ({$amharic})";
        }

        return $this->formatter()->getMonthName($monthIndex, $locale);
    }

    private function getDayNameDirect(int $dayIndex, DisplayMode|string|null $mode = null): string
    {
        $mode = $this->resolveMode($mode);

        if ($mode === DisplayMode::AmharicNoWeek || $mode === DisplayMode::TransliterationNoWeek) {
            return '';
        }

        $locale = match ($mode) {
            DisplayMode::TransliterationCombined,
            DisplayMode::CleanGregorian => 'en',
            default => 'am',
        };

        // Normalize Carbon's 0 (Sunday) → 7
        $index = $dayIndex === 0 ? 7 : $dayIndex;

        if ($mode === DisplayMode::Hybrid) {
            $english = $this->formatter()->getDayName($index, 'long', 'en');
            $amharic = $this->formatter()->getDayName($index, 'long', 'am');

            return "{$english} ({$amharic})";
        }

        return $this->formatter()->getDayName($index, 'long', $locale);
    }

    private function resolveMode(DisplayMode|string|null $mode): DisplayMode
    {
        if ($mode instanceof DisplayMode) {
            return $mode;
        }

        if (is_string($mode)) {
            return DisplayMode::tryFrom($mode) ?? DisplayMode::fromConfig();
        }

        return DisplayMode::fromConfig();
    }

    // ──────────────────────────────────────────────
    // Service resolution
    // ──────────────────────────────────────────────

    private function calendar(): EthiopicCalendarService
    {
        return $this->calendarService ??= app(EthiopicCalendarService::class);
    }

    private function time(): EthiopicTimeService
    {
        return $this->timeService ??= app(EthiopicTimeService::class);
    }

    private function formatter(): EthiopicFormatter
    {
        return $this->formatter ??= app(EthiopicFormatter::class);
    }
}
