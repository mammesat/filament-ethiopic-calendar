<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Enums;

use Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig;

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
                        try {
                $isProduction = function_exists('app') && app()->bound('env') && app()->isProduction();
            } catch (\Throwable) {
                $isProduction = false;
            }

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
     * Resolve the configured display mode, falling back to locale.
     *
     * Delegates to EthiopicConfig so runtime overrides — including a Closure
     * resolved per request — reach every component and the formatter. This
     * used to read config() directly, so EthiopicConfig::set('display_mode')
     * was silently ignored everywhere it mattered, unlike calendar_system.
     */
    public static function fromConfig(): self
    {
        return EthiopicConfig::displayMode();
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

