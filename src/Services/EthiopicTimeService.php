<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Services;

use Mammesat\FilamentEthiopicCalendar\Support\LocaleManager;

/**
 * Ethiopian Time System Engine.
 *
 * Handles the 6-hour shift between Gregorian and Ethiopian time,
 * and maps to the four Ethiopian day periods.
 *
 * Ethiopian time counts from sunrise (6:00 Gregorian = 12:00 Ethiopian).
 * The day is divided into four 6-hour periods:
 *   - ጠዋት  (morning)   : 06:00–11:59 Gregorian → 12:00–5:59 Ethiopian
 *   - ከሰዓት (afternoon) : 12:00–17:59 Gregorian → 6:00–11:59 Ethiopian
 *   - ማታ   (evening)   : 18:00–23:59 Gregorian → 12:00–5:59 Ethiopian
 *   - ለሊት  (night)     : 00:00–05:59 Gregorian → 6:00–11:59 Ethiopian
 */
final class EthiopicTimeService
{
    public function __construct(
        private readonly LocaleManager $locale,
    ) {}

    /**
     * Convert Gregorian 24h time → Ethiopian time components.
     *
     * @return array{hour: int, minute: int, period: string, periodKey: string}
     */
    public function toEthiopian(int $gregorianHour, int $minute, ?string $locale = null): array
    {
        $this->assertValidTime($gregorianHour, $minute);

        $ethiopianHour = ($gregorianHour + 6) % 12;

        if ($ethiopianHour === 0) {
            $ethiopianHour = 12;
        }

        $periodKey = $this->getDayPeriodKey($gregorianHour);

        return [
            'hour' => $ethiopianHour,
            'minute' => $minute,
            'period' => $this->locale->getDayPeriod($periodKey, $locale),
            'periodKey' => $periodKey,
        ];
    }

    /**
     * Convert Ethiopian time → Gregorian 24h time.
     *
     * @param  string  $periodKey  One of: morning, afternoon, evening, night
     * @return array{hour: int, minute: int}
     */
    public function toGregorian(int $ethiopianHour, int $minute, string $periodKey): array
    {
        $this->assertValidEthiopianHour($ethiopianHour);
        $this->assertValidMinute($minute);

        // The forward conversion: ethHour = (gregHour + 6) % 12, where 0 → 12
        // Inverse within a 12-hour cycle:
        //   gregHourMod12 = ethHour == 12 ? 6 : ((ethHour - 6 + 12) % 12)
        $gregHourMod12 = $ethiopianHour === 12
            ? 6
            : (($ethiopianHour - 6 + 12) % 12);

        // Each period spans exactly 6 hours.
        // Morning (6-11): Eth 12→6, periods with gregHour in [6..11] → halfBase 0
        // Afternoon (12-17): Eth 6→12, periods with gregHour in [12..17] → halfBase 12
        // Evening (18-23): Eth 12→18, periods with gregHour in [18..23] → halfBase 12
        // Night (0-5): Eth 6→0, periods with gregHour in [0..5] → halfBase 0
        //
        // However gregHourMod12 maps us into [0..11], and we need to add
        // the correct 12-hour offset:
        $halfBase = match ($periodKey) {
            'morning'   => 0,   // gregHour 6-11, mod12 gives 6-11, + 0
            'afternoon' => 12,  // gregHour 12-17, mod12 gives 0-5, + 12
            'evening'   => 12,  // gregHour 18-23, mod12 gives 6-11, + 12
            'night'     => 0,   // gregHour 0-5, mod12 gives 0-5, + 0
            default     => 0,
        };

        $gregorianHour = ($gregHourMod12 + $halfBase) % 24;

        return [
            'hour' => $gregorianHour,
            'minute' => $minute,
        ];
    }

    /**
     * Get the Ethiopian day period key for a Gregorian hour.
     */
    public function getDayPeriodKey(int $gregorianHour): string
    {
        return match (true) {
            $gregorianHour >= 6 && $gregorianHour <= 11  => 'morning',
            $gregorianHour >= 12 && $gregorianHour <= 17 => 'afternoon',
            $gregorianHour >= 18 && $gregorianHour <= 23 => 'evening',
            default                                       => 'night',
        };
    }

    /**
     * Get the localized day period name for a Gregorian hour.
     */
    public function getDayPeriod(int $gregorianHour, ?string $locale = null): string
    {
        $key = $this->getDayPeriodKey($gregorianHour);

        return $this->locale->getDayPeriod($key, $locale);
    }

    /**
     * Get all day period definitions with their Gregorian hour ranges.
     *
     * @return array<string, array{start: int, end: int, label: string}>
     */
    public function getDayPeriods(?string $locale = null): array
    {
        return [
            'morning' => [
                'start' => 6,
                'end' => 11,
                'label' => $this->locale->getDayPeriod('morning', $locale),
            ],
            'afternoon' => [
                'start' => 12,
                'end' => 17,
                'label' => $this->locale->getDayPeriod('afternoon', $locale),
            ],
            'evening' => [
                'start' => 18,
                'end' => 23,
                'label' => $this->locale->getDayPeriod('evening', $locale),
            ],
            'night' => [
                'start' => 0,
                'end' => 5,
                'label' => $this->locale->getDayPeriod('night', $locale),
            ],
        ];
    }

    /**
     * Format Ethiopian time as a display string.
     *
     * Example: "ጠዋት 4:30" or "Morning 4:30"
     */
    public function formatEthiopianTime(int $gregorianHour, int $minute, ?string $locale = null): string
    {
        $ethiopian = $this->toEthiopian($gregorianHour, $minute, $locale);

        return sprintf('%s %d:%02d', $ethiopian['period'], $ethiopian['hour'], $ethiopian['minute']);
    }

    /**
     * Parse a time string (HH:MM or HH:MM:SS) into hour and minute.
     *
     * @return array{hour: int, minute: int}|null
     */
    public function parseTimeString(?string $time): ?array
    {
        if ($time === null) {
            return null;
        }

        $time = trim($time);

        if ($time === '') {
            return null;
        }

        if (! preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $matches)) {
            return null;
        }

        $hour = (int) $matches[1];
        $minute = (int) $matches[2];

        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
            return null;
        }

        return ['hour' => $hour, 'minute' => $minute];
    }

    // ──────────────────────────────────────────────
    // Validation
    // ──────────────────────────────────────────────

    private function assertValidTime(int $hour, int $minute): void
    {
        if ($hour < 0 || $hour > 23) {
            throw new \InvalidArgumentException("Invalid hour: {$hour}. Must be 0–23.");
        }

        if ($minute < 0 || $minute > 59) {
            throw new \InvalidArgumentException("Invalid minute: {$minute}. Must be 0–59.");
        }
    }

    private function assertValidEthiopianHour(int $hour): void
    {
        if ($hour < 1 || $hour > 12) {
            throw new \InvalidArgumentException("Invalid Ethiopian hour: {$hour}. Must be 1–12.");
        }
    }

    private function assertValidMinute(int $minute): void
    {
        if ($minute < 0 || $minute > 59) {
            throw new \InvalidArgumentException("Invalid minute: {$minute}. Must be 0–59.");
        }
    }
}
