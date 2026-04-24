<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Tests\Unit;

use Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig;
use Mammesat\FilamentEthiopicCalendar\Support\SettingsResolver;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the SettingsResolver — the unified config resolution system.
 */
final class SettingsResolverTest extends TestCase
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

    // ──────────────────────────────────────────────
    // Override priority
    // ──────────────────────────────────────────────

    public function test_field_override_takes_highest_priority(): void
    {
        // Even if EthiopicConfig has a runtime override set...
        EthiopicConfig::set('display_mode', 'hybrid');

        // ...field override wins
        $result = SettingsResolver::get('display_mode', 'amharic_no_week', [
            'display_mode' => 'compact_amharic',
        ]);

        self::assertSame('compact_amharic', $result);
    }

    public function test_config_fallback_when_no_override(): void
    {
        EthiopicConfig::set('locale', 'en');

        // No field override → should fall through to EthiopicConfig (runtime override → config → default)
        $result = SettingsResolver::get('locale', 'am');

        self::assertSame('en', $result);
    }

    public function test_default_fallback_when_no_override_or_config(): void
    {
        // No override, no config → default
        $result = SettingsResolver::get('nonexistent_key', 'my_default');

        self::assertSame('my_default', $result);
    }

    public function test_empty_overrides_array_behaves_like_no_override(): void
    {
        EthiopicConfig::set('locale', 'en');

        $result = SettingsResolver::get('locale', 'am', []);

        self::assertSame('en', $result);
    }

    public function test_null_override_value_falls_through(): void
    {
        EthiopicConfig::set('locale', 'en');

        // Null value in overrides should fall through to config
        $result = SettingsResolver::get('locale', 'am', [
            'locale' => null,
        ]);

        self::assertSame('en', $result);
    }

    public function test_override_with_false_value_is_respected(): void
    {
        EthiopicConfig::set('with_time', true);

        $result = SettingsResolver::get('with_time', true, [
            'with_time' => false,
        ]);

        self::assertFalse($result);
    }

    // ──────────────────────────────────────────────
    // Typed accessors
    // ──────────────────────────────────────────────

    public function test_bool_accessor(): void
    {
        self::assertFalse(SettingsResolver::bool('with_time', false));
        self::assertTrue(SettingsResolver::bool('with_time', false, ['with_time' => true]));
    }

    public function test_string_accessor(): void
    {
        self::assertSame('am', SettingsResolver::string('locale', 'am'));
        self::assertSame('en', SettingsResolver::string('locale', 'am', ['locale' => 'en']));
    }

    // ──────────────────────────────────────────────
    // Integration with EthiopicConfig
    // ──────────────────────────────────────────────

    public function test_resolver_uses_ethiopic_config_runtime_overrides(): void
    {
        EthiopicConfig::set('time_mode', 'ethiopian');

        // SettingsResolver should see EthiopicConfig's runtime override
        $result = SettingsResolver::get('time_mode', 'gregorian');

        self::assertSame('ethiopian', $result);
    }

    public function test_resolver_field_override_beats_config_runtime_override(): void
    {
        EthiopicConfig::set('time_mode', 'ethiopian');

        // Field override should beat EthiopicConfig's runtime override
        $result = SettingsResolver::get('time_mode', 'gregorian', [
            'time_mode' => 'dual',
        ]);

        self::assertSame('dual', $result);
    }
}
