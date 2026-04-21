<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Support;

/**
 * Translation wrapper for the Ethiopic Calendar package.
 *
 * All localized strings (months, days, time periods) are resolved
 * through Laravel's translation system, never hardcoded.
 */
class LocaleManager
{
    private const PACKAGE_NAMESPACE = 'filament-ethiopic-calendar';

    /**
     * Get a localized month name (1-indexed: 1=Meskerem, 13=Pagume).
     */
    public function getMonthName(int $index, ?string $locale = null): string
    {
        $locale = $this->resolveLocale($locale);

        // Translation files use 0-based arrays, but we accept 1-based index
        $months = __("{$this->namespace()}.months", [], $locale);

        if (is_array($months) && isset($months[$index - 1])) {
            return (string) $months[$index - 1];
        }

        return (string) $index;
    }

    /**
     * Get a localized day name (ISO-8601: 1=Monday, 7=Sunday, 0=Sunday alias).
     */
    public function getDayName(int $index, string $length = 'long', ?string $locale = null): string
    {
        $locale = $this->resolveLocale($locale);

        // Normalize Carbon's 0 (Sunday) → 7
        $normalizedIndex = $index === 0 ? 7 : $index;

        $key = "{$this->namespace()}.days.{$length}";
        $days = __($key, [], $locale);

        if (is_array($days) && isset($days[$normalizedIndex])) {
            return (string) $days[$normalizedIndex];
        }

        return (string) $normalizedIndex;
    }

    /**
     * Get a localized day period name.
     *
     * @param  string  $periodKey  One of: morning, afternoon, evening, night
     */
    public function getDayPeriod(string $periodKey, ?string $locale = null): string
    {
        $locale = $this->resolveLocale($locale);

        $key = "{$this->namespace()}.time_periods.{$periodKey}";
        $translated = __($key, [], $locale);

        // If the translation key is returned as-is, fallback to the key
        if ($translated === $key) {
            return $periodKey;
        }

        return (string) $translated;
    }

    /**
     * Get all day periods for a locale.
     *
     * @return array<string, string>
     */
    public function getAllDayPeriods(?string $locale = null): array
    {
        $periods = [];

        foreach (['morning', 'afternoon', 'evening', 'night'] as $key) {
            $periods[$key] = $this->getDayPeriod($key, $locale);
        }

        return $periods;
    }

    /**
     * Resolve the effective locale.
     */
    public function resolveLocale(?string $locale = null): string
    {
        if ($locale !== null && $locale !== '') {
            return $locale;
        }

        return EthiopicConfig::locale();
    }

    private function namespace(): string
    {
        return self::PACKAGE_NAMESPACE . '::ethiopic';
    }
}
