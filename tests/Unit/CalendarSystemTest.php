<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Tests\Unit;

use Mammesat\FilamentEthiopicCalendar\Enums\CalendarSystem;
use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Fields\EthiopicDateTimePicker;
use Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig;
use Mammesat\FilamentEthiopicCalendar\Tests\TestCase;

final class CalendarSystemTest extends TestCase
{
    protected function tearDown(): void
    {
        EthiopicConfig::reset();

        parent::tearDown();
    }

    public function test_from_value_accepts_aliases(): void
    {
        self::assertSame(CalendarSystem::Ethiopic, CalendarSystem::fromValue('ethiopian'));
        self::assertSame(CalendarSystem::Gregorian, CalendarSystem::fromValue('Gregory'));
        self::assertNull(CalendarSystem::fromValue('julian'));
        self::assertNull(CalendarSystem::fromValue(null));
    }

    public function test_config_defaults_to_ethiopic(): void
    {
        self::assertSame(CalendarSystem::Ethiopic, EthiopicConfig::calendarSystem());
    }

    public function test_config_resolves_lazy_closures(): void
    {
        EthiopicConfig::set('calendar_system', fn () => 'gregorian');

        self::assertSame(CalendarSystem::Gregorian, EthiopicConfig::calendarSystem());
    }

    public function test_picker_uses_ethiopic_view_by_default(): void
    {
        $picker = EthiopicDateTimePicker::make('date');

        self::assertFalse($picker->isGregorianCalendar());
        self::assertTrue($picker->hasView());
        self::assertTrue($picker->isEthiopicHelperEnabled());
    }

    public function test_gregorian_calendar_falls_back_to_filament_default_picker(): void
    {
        $picker = EthiopicDateTimePicker::make('date')->gregorianCalendar();

        self::assertTrue($picker->isGregorianCalendar());
        self::assertFalse($picker->hasView(), 'Filament renders its embedded DateTimePicker when no view is set.');
        self::assertFalse($picker->isEthiopicHelperEnabled());
        self::assertNull($picker->getHelperDisplay('2026-09-11'));
    }

    public function test_gregorian_helper_can_be_opted_back_in(): void
    {
        $picker = EthiopicDateTimePicker::make('date')->gregorianCalendar()->showEthiopicHelper();

        self::assertNotNull($picker->getHelperDisplay('2026-09-11'));
    }

    public function test_gregorian_calendar_accepts_closure(): void
    {
        $useGregorian = true;
        $picker = EthiopicDateTimePicker::make('date')->gregorianCalendar(fn () => $useGregorian);

        self::assertTrue($picker->isGregorianCalendar());
    }

    public function test_field_setting_overrides_config(): void
    {
        EthiopicConfig::set('calendar_system', 'gregorian');

        self::assertTrue(EthiopicDateTimePicker::make('date')->isGregorianCalendar());
        self::assertFalse(EthiopicDateTimePicker::make('date')->ethiopicCalendar()->isGregorianCalendar());
        self::assertFalse(EthiopicDateTimePicker::make('date')->ethiopic()->isGregorianCalendar());
    }

    public function test_gregorian_preset_switches_calendar_and_display(): void
    {
        $picker = EthiopicDateTimePicker::make('date')->gregorian();

        self::assertTrue($picker->isGregorianCalendar());
        self::assertSame(DisplayMode::Gregorian, $picker->getDisplayMode());
    }
}
