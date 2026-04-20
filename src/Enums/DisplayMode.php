<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicDatePicker\Enums;

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
        $value = config('ethiopic-calendar.display_mode');

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
        $locale = $locale ?? config('ethiopic-calendar.locale', 'am');

        return match ($locale) {
            'en' => self::TransliterationNoWeek,
            'hybrid' => self::Hybrid,
            default => self::AmharicNoWeek,
        };
    }
}
