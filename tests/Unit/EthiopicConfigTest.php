<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Tests\Unit;

use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Enums\TimeMode;
use Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the config resolution system.
 */
final class EthiopicConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        EthiopicConfig::reset();
    }

    protected function tearDown(): void
    {
        EthiopicConfig::reset();

        parent::tearDown();
    }

    public function test_runtime_overrides_take_priority(): void
    {
        EthiopicConfig::set('locale', 'en');

        self::assertSame('en', EthiopicConfig::resolve('locale'));
    }

    public function test_resolve_returns_default_when_no_override_or_config(): void
    {
        // Without Laravel config(), resolve should return the default
        self::assertSame('fallback', EthiopicConfig::resolve('nonexistent_key', 'fallback'));
    }

    public function test_reset_clears_all_overrides(): void
    {
        EthiopicConfig::set('locale', 'en');
        EthiopicConfig::set('time_mode', 'ethiopian');

        EthiopicConfig::reset();

        // After reset, runtime override should be gone
        self::assertNotSame('en', EthiopicConfig::resolve('locale', 'am'));
        self::assertSame('am', EthiopicConfig::resolve('locale', 'am'));
    }

    public function test_forget_clears_specific_override(): void
    {
        EthiopicConfig::set('locale', 'en');
        EthiopicConfig::set('time_mode', 'ethiopian');

        EthiopicConfig::forget('locale');

        self::assertSame('am', EthiopicConfig::resolve('locale', 'am'));
        self::assertSame('ethiopian', EthiopicConfig::resolve('time_mode'));
    }

    public function test_time_mode_enum_from_config(): void
    {
        // Test string to enum mapping
        self::assertSame(TimeMode::Gregorian, TimeMode::tryFrom('gregorian'));
        self::assertSame(TimeMode::Ethiopian, TimeMode::tryFrom('ethiopian'));
        self::assertSame(TimeMode::Dual, TimeMode::tryFrom('dual'));
        self::assertNull(TimeMode::tryFrom('invalid'));
    }

    public function test_display_mode_from_simple_mode(): void
    {
        self::assertSame(DisplayMode::EthiopicAmharic, DisplayMode::fromSimpleMode('ethiopic'));
        self::assertSame(DisplayMode::Gregorian, DisplayMode::fromSimpleMode('gregorian'));
        self::assertSame(DisplayMode::Dual, DisplayMode::fromSimpleMode('dual'));
    }

    public function test_display_mode_from_simple_mode_falls_back_to_enum_value(): void
    {
        self::assertSame(
            DisplayMode::CompactAmharic,
            DisplayMode::fromSimpleMode('compact_amharic'),
        );
    }

    public function test_dual_time_format_has_default(): void
    {
        self::assertSame(':gregorian (:ethiopian)', EthiopicConfig::resolve('dual_time_format', ':gregorian (:ethiopian)'));
    }

    public function test_display_mode_runtime_override_reaches_from_config(): void
    {
        // Components, the formatter and the calendar service all resolve
        // through DisplayMode::fromConfig(); it must honour a runtime override.
        EthiopicConfig::set('display_mode', DisplayMode::Gregorian);

        self::assertSame(DisplayMode::Gregorian, DisplayMode::fromConfig());
    }

    public function test_display_mode_accepts_a_closure_resolved_per_call(): void
    {
        $tenantUsesGregorian = false;
        EthiopicConfig::set('display_mode', function () use (&$tenantUsesGregorian): string {
            return $tenantUsesGregorian ? 'gregorian' : 'ethiopic_english';
        });

        self::assertSame(DisplayMode::EthiopicEnglish, DisplayMode::fromConfig());

        $tenantUsesGregorian = true;

        self::assertSame(DisplayMode::Gregorian, DisplayMode::fromConfig());
    }

    public function test_display_mode_closure_that_throws_falls_back_to_locale(): void
    {
        EthiopicConfig::set('display_mode', fn () => throw new \RuntimeException('no tenant'));

        self::assertSame(DisplayMode::fromLocale(), EthiopicConfig::displayMode());
    }

    public function test_display_mode_still_maps_legacy_keys(): void
    {
        EthiopicConfig::set('display_mode', 'clean_gregorian');

        self::assertSame(DisplayMode::Gregorian, @EthiopicConfig::displayMode());
    }
}
