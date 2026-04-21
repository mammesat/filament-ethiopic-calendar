<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Enums;

enum DisplayMode: string
{
    case AmharicCombined = 'amharic_combined';
    case TransliterationCombined = 'transliteration_combined';
    case CleanGregorian = 'clean_gregorian';
    case Hybrid = 'hybrid';
    case CompactAmharic = 'compact_amharic';
    case AmharicNoWeek = 'amharic_no_week';
    case TransliterationNoWeek = 'transliteration_no_week';

    /**
     * Resolve display mode from config, falling back to locale.
     */
    public static function fromConfig(): self
    {
        try {
            $value = function_exists('config') ? config('ethiopic-calendar.display_mode') : null;
        } catch (\Throwable) {
            $value = null;
        }

        if ($value !== null) {
            $resolved = self::tryFrom($value);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        return self::fromLocale();
    }

    /**
     * Resolve display mode from locale string.
     */
    public static function fromLocale(?string $locale = null): self
    {
        try {
            $locale = $locale ?? (function_exists('config') ? config('ethiopic-calendar.locale', 'am') : 'am');
        } catch (\Throwable) {
            $locale = $locale ?? 'am';
        }

        return match ($locale) {
            'en' => self::TransliterationNoWeek,
            'hybrid' => self::Hybrid,
            default => self::AmharicNoWeek,
        };
    }

    /**
     * Map high-level simple mode strings to internal DisplayMode variants.
     *
     * This is the convenience API: displayMode('ethiopic' | 'gregorian' | 'dual')
     */
    public static function fromSimpleMode(string $mode): self
    {
        return match ($mode) {
            'ethiopic'  => self::AmharicNoWeek,
            'gregorian' => self::CleanGregorian,
            'dual'      => self::Hybrid,
            default     => self::tryFrom($mode) ?? self::fromConfig(),
        };
    }
}
