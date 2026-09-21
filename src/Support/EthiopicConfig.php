<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Support;

use Mammesat\FilamentEthiopicCalendar\Enums\CalendarSystem;
use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Enums\TimeMode;

/**
 * Centralized config resolution for the Ethiopic Calendar package.
 *
 * Priority chain: runtime override → config file → default.
 */
final class EthiopicConfig
{
    /** @var array<string, mixed> */
    private static array $runtimeOverrides = [];

    /**
     * Resolve a config value with priority: runtime → config → default.
     */
    public static function resolve(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$runtimeOverrides)) {
            return self::$runtimeOverrides[$key];
        }

        try {
            if (function_exists('config')) {
                return config("ethiopic-calendar.{$key}", $default);
            }
        } catch (\Throwable) {
            // config() exists (Laravel helpers loaded) but app not booted
        }

        return $default;
    }

    /**
     * Set a runtime override for a config key.
     */
    public static function set(string $key, mixed $value): void
    {
        self::$runtimeOverrides[$key] = $value;
    }

    /**
     * Clear all runtime overrides.
     */
    public static function reset(): void
    {
        self::$runtimeOverrides = [];
    }

    /**
     * Clear a specific runtime override.
     */
    public static function forget(string $key): void
    {
        unset(self::$runtimeOverrides[$key]);
    }

    // ──────────────────────────────────────────────
    // Typed accessors
    // ──────────────────────────────────────────────

    /**
     * Resolve the display mode — the single source every component, the
     * formatter and the calendar service read (via DisplayMode::fromConfig()).
     *
     * Like calendar_system, the value may be a DisplayMode, a string (current
     * or legacy key), or a Closure returning either, so apps can follow the
     * current tenant:
     *   EthiopicConfig::set('display_mode', fn () => $tenant->usesGregorian() ? 'gregorian' : 'ethiopic_english');
     */
    public static function displayMode(): DisplayMode
    {
        $value = self::resolve('display_mode');

        if ($value instanceof \Closure) {
            try {
                $value = $value();
            } catch (\Throwable) {
                $value = null;
            }
        }

        if ($value instanceof DisplayMode) {
            return $value;
        }

        if (is_string($value)) {
            return DisplayMode::fromLegacy($value) ?? DisplayMode::fromLocale();
        }

        return DisplayMode::fromLocale();
    }

    /**
     * Resolve the picker calendar system.
     *
     * The value may be a CalendarSystem, a string, or a Closure returning either,
     * so apps can resolve it lazily per request (e.g. from the current tenant):
     *   EthiopicConfig::set('calendar_system', fn () => $tenant->usesGregorian() ? 'gregorian' : 'ethiopic');
     */
    public static function calendarSystem(): CalendarSystem
    {
        $value = self::resolve('calendar_system');

        if ($value instanceof \Closure) {
            try {
                $value = $value();
            } catch (\Throwable) {
                $value = null;
            }
        }

        return CalendarSystem::fromValue(is_string($value) || $value instanceof CalendarSystem ? $value : null)
            ?? CalendarSystem::Ethiopic;
    }

    public static function timeMode(): TimeMode
    {
        $value = self::resolve('time_mode');

        if ($value instanceof TimeMode) {
            return $value;
        }

        if (is_string($value)) {
            $resolved = TimeMode::tryFrom($value);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        return TimeMode::fromConfig();
    }

    public static function locale(): string
    {
        return (string) self::resolve('locale', 'am');
    }

    public static function calendarLocale(): string
    {
        return (string) self::resolve('calendar_locale', 'am');
    }

    public static function withTime(): bool
    {
        return (bool) self::resolve('with_time', false);
    }

    public static function timezone(): string
    {
        return (string) self::resolve('timezone', 'Africa/Addis_Ababa');
    }

    public static function dualTimeFormat(): string
    {
        return (string) self::resolve('dual_time_format', ':gregorian (:ethiopian)');
    }
}
