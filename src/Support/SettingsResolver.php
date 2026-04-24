<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Support;

/**
 * Unified settings resolution for the Ethiopic Calendar package.
 *
 * Resolution priority:
 *   Field Override → Config File → Default
 *
 * This provides a single entry point for all configuration lookups,
 * ensuring consistent behavior across DatePicker, Table Column,
 * and Infolist Entry components.
 *
 * Future extension: DB-based settings can be added as a middle layer
 * between field overrides and config file without changing the public API.
 */
final class SettingsResolver
{
    /**
     * Resolve a configuration value with priority:
     *   Field Override → Config File → Default.
     *
     * @param  string  $key       The config key (e.g., 'display_mode', 'with_time')
     * @param  mixed   $default   Fallback value if nothing is configured
     * @param  array   $overrides Field-level overrides (keyed by config key)
     */
    public static function get(string $key, mixed $default = null, array $overrides = []): mixed
    {
        // 1. Field override takes highest priority
        if (array_key_exists($key, $overrides) && $overrides[$key] !== null) {
            return $overrides[$key];
        }

        // 2. Future extension point: DB settings would go here
        // if (static::hasDatabaseSettings()) {
        //     $dbValue = static::getFromDatabase($key);
        //     if ($dbValue !== null) return $dbValue;
        // }

        // 3. Config file (via EthiopicConfig, which also supports runtime overrides)
        return EthiopicConfig::resolve($key, $default);
    }

    /**
     * Resolve a boolean setting.
     */
    public static function bool(string $key, bool $default = false, array $overrides = []): bool
    {
        return (bool) static::get($key, $default, $overrides);
    }

    /**
     * Resolve a string setting.
     */
    public static function string(string $key, string $default = '', array $overrides = []): string
    {
        return (string) static::get($key, $default, $overrides);
    }
}
