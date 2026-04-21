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
        self::assertSame(DisplayMode::AmharicNoWeek, DisplayMode::fromSimpleMode('ethiopic'));
        self::assertSame(DisplayMode::CleanGregorian, DisplayMode::fromSimpleMode('gregorian'));
        self::assertSame(DisplayMode::Hybrid, DisplayMode::fromSimpleMode('dual'));
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
}
