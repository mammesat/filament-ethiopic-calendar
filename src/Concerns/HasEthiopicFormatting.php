<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Concerns;

use Carbon\Carbon;
use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Enums\TimeMode;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter;
use Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig;

/**
 * Shared formatting concern for Table Columns and Infolist Entries.
 *
 * Centralizes the state-to-display conversion by delegating entirely
 * to EthiopicFormatter — the single source of truth.
 */
trait HasEthiopicFormatting
{
    use HasEthiopicDisplayMode;
    use HasEthiopicTimeMode;

    protected ?bool $withTimeOverride = null;

    /**
     * Convenience preset: full Ethiopian mode.
     */
    public function ethiopic(): static
    {
        return $this
            ->displayMode(DisplayMode::EthiopicAmharic)
            ->timeMode(TimeMode::Ethiopian);
    }

    /**
     * Convenience preset: dual mode (Ethiopian + Gregorian).
     */
    public function dual(): static
    {
        return $this
            ->displayMode(DisplayMode::Dual)
            ->timeMode(TimeMode::Dual);
    }

    /**
     * Convenience preset: pure Gregorian mode.
     */
    public function gregorian(): static
    {
        return $this
            ->displayMode(DisplayMode::Gregorian)
            ->timeMode(TimeMode::Gregorian);
    }

    /**
     * Enable or disable showing time.
     */
    public function withTime(bool $enabled = true): static
    {
        $this->withTimeOverride = $enabled;

        return $this;
    }

    /**
     * Determine if time should be formatted.
     */
    public function hasTime(): bool
    {
        return $this->withTimeOverride ?? EthiopicConfig::withTime();
    }

    /**
     * Format a state value using the central EthiopicFormatter.
     */
    protected function formatEthiopicState(mixed $state): ?string
    {
        if ($state === null) {
            return null;
        }

        try {
            $carbon = Carbon::parse($state, config('app.timezone'))->setTimezone(EthiopicConfig::timezone());
            $dateTimeString = $carbon->format('Y-m-d H:i:s');
            $dateString = $carbon->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }

        if (! $this->hasTime()) {
            return app(EthiopicFormatter::class)->formatDate(
                $dateString,
                $this->getDisplayMode(),
            );
        }

        return app(EthiopicFormatter::class)->formatDateTime(
            $dateTimeString,
            $this->getDisplayMode(),
            $this->getTimeMode(),
        );
    }
}
