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

    protected bool $tooltipAlternateEnabled = false;

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
     * Enable the alternate-calendar tooltip.
     *
     * When the displayed date is Ethiopic → tooltip shows Gregorian.
     * When the displayed date is Gregorian → tooltip shows Ethiopic.
     * When in Dual mode → tooltip is disabled (both already visible).
     */
    public function tooltipAlternate(bool $enabled = true): static
    {
        $this->tooltipAlternateEnabled = $enabled;

        return $this;
    }

    /**
     * Compute the alternate-calendar tooltip for the given raw state.
     *
     * Flips the display/time modes so the user sees the "other" calendar
     * on hover, without duplicating any formatting logic.
     */
    protected function getAlternateTooltip(mixed $state): ?string
    {
        if (! $this->tooltipAlternateEnabled || $state === null) {
            return null;
        }

        $currentDisplay = $this->getDisplayMode();

        // Dual already shows both — no tooltip needed.
        if ($currentDisplay === DisplayMode::Dual) {
            return null;
        }

        try {
            $carbon = Carbon::parse($state, config('app.timezone'))
                ->setTimezone(EthiopicConfig::timezone());
            $dateTimeString = $carbon->format('Y-m-d H:i:s');
            $dateString = $carbon->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }

        // Determine the opposite display + time modes.
        if ($currentDisplay === DisplayMode::Gregorian) {
            $altDisplay = DisplayMode::EthiopicAmharic;
            $altTime = TimeMode::Ethiopian;
        } else {
            // Any Ethiopic variant → flip to Gregorian
            $altDisplay = DisplayMode::Gregorian;
            $altTime = TimeMode::Gregorian;
        }

        $formatter = app(EthiopicFormatter::class);

        if (! $this->hasTime()) {
            return $formatter->formatDate($dateString, $altDisplay);
        }

        return $formatter->formatDateTime($dateTimeString, $altDisplay, $altTime);
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
