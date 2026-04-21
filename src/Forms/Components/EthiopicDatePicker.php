<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Forms\Components;

use Mammesat\FilamentEthiopicCalendar\Fields\EthiopicDateTimePicker;

/**
 * @deprecated Use \Mammesat\FilamentEthiopicCalendar\Fields\EthiopicDateTimePicker instead.
 *
 * This class is preserved for backward compatibility and will be removed in v2.0.
 */
class EthiopicDatePicker extends EthiopicDateTimePicker
{
    protected function setUp(): void
    {
        parent::setUp();

        if (function_exists('trigger_deprecation')) {
            trigger_deprecation(
                'mammesat/filament-ethiopic-calendar',
                '1.1',
                'The "%s" class is deprecated, use "%s" instead.',
                self::class,
                EthiopicDateTimePicker::class,
            );
        }
    }
}
