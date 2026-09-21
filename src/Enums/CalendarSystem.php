<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Enums;

/**
 * The calendar grid rendered by the date picker popup.
 *
 * - Ethiopic:  13-month Ethiopic grid (this package's custom Alpine component).
 * - Gregorian: Filament's own default date picker, unchanged.
 *
 * Independent from DisplayMode, which only controls how stored dates are
 * formatted as text (helper text, table columns, infolist entries).
 */
enum CalendarSystem: string
{
    case Ethiopic = 'ethiopic';
    case Gregorian = 'gregorian';

    /**
     * Lenient parser accepting common aliases ('ethiopian', 'gregory', ...).
     */
    public static function fromValue(self|string|null $value): ?self
    {
        if ($value instanceof self || $value === null) {
            return $value;
        }

        return match (strtolower(trim($value))) {
            'ethiopic', 'ethiopian', 'ethiopia', 'eth' => self::Ethiopic,
            'gregorian', 'gregory', 'greg' => self::Gregorian,
            default => null,
        };
    }
}
