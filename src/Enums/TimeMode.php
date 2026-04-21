<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Enums;

enum TimeMode: string
{
    /** Standard Gregorian AM/PM — no Ethiopian transformation */
    case Gregorian = 'gregorian';

    /** Ethiopian hour system (1–12 starting at 6:00) with day periods */
    case Ethiopian = 'ethiopian';

    /** Dual display: both Gregorian and Ethiopian time shown */
    case Dual = 'dual';

    /**
     * Resolve TimeMode from config, with backward compatibility
     * for the legacy 'time_format' key.
     */
    public static function fromConfig(): self
    {
        // New key takes priority
        try {
            $value = function_exists('config') ? config('ethiopic-calendar.time_mode') : null;
        } catch (\Throwable) {
            $value = null;
        }

        if ($value !== null) {
            $resolved = self::tryFrom($value);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        try {
            $legacy = function_exists('config') ? config('ethiopic-calendar.time_format') : null;
        } catch (\Throwable) {
            $legacy = null;
        }

        if ($legacy !== null) {
            return match ($legacy) {
                'ethiopian' => self::Ethiopian,
                'standard'  => self::Gregorian,
                default     => self::Gregorian,
            };
        }

        return self::Gregorian;
    }
}
