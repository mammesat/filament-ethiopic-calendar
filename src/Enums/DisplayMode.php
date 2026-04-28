<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Enums;

enum DisplayMode: string
{
    case EthiopicAmharic = 'ethiopic_amharic';
    case EthiopicEnglish = 'ethiopic_english';
    case Gregorian = 'gregorian';
    case Dual = 'dual';

    // The following were not part of the rename request but keeping for any internal complex uses
    case AmharicCombined = 'amharic_combined';
    case TransliterationCombined = 'transliteration_combined';
    case CompactAmharic = 'compact_amharic';

    /**
     * Parse a legacy string value to the modern DisplayMode or fallback.
     */
    public static function fromLegacy(string $value): ?self
    {
        $mapped = match ($value) {
            'amharic_no_week' => self::EthiopicAmharic,
            'transliteration_no_week' => self::EthiopicEnglish,
            'clean_gregorian' => self::Gregorian,
            'hybrid' => self::Dual,
            default => null,
        };

        if ($mapped !== null) {
            $isProduction = function_exists('app') && method_exists(app(), 'isProduction') && app()->isProduction();

            if (! $isProduction) {
                @trigger_error("Legacy display mode key '{$value}' is deprecated and will be removed in a future release. Use '{$mapped->value}' instead.", E_USER_DEPRECATED);
            }
            
            return $mapped;
        }

        return self::tryFrom($value);
    }

    /**
     * Map the active DisplayMode back to its legacy equivalent.
     * Useful for backward-compatible exports or downgrades.
     */
    public function toLegacy(): string
    {
        return match ($this) {
            self::EthiopicAmharic => 'amharic_no_week',
            self::EthiopicEnglish => 'transliteration_no_week',
            self::Gregorian => 'clean_gregorian',
            self::Dual => 'hybrid',
            default => $this->value,
        };
    }

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
            $resolved = self::fromLegacy($value);

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
            'en' => self::EthiopicEnglish,
            'hybrid' => self::Dual,
            default => self::EthiopicAmharic,
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
            'ethiopic'  => self::EthiopicAmharic,
            'gregorian' => self::Gregorian,
            'dual'      => self::Dual,
            default     => self::fromLegacy($mode) ?? self::fromConfig(),
        };
    }
}
