<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Concerns;

use Mammesat\FilamentEthiopicCalendar\Enums\TimeMode;
use Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig;

/**
 * Provides time mode management for Filament components.
 */
trait HasEthiopicTimeMode
{
    protected ?TimeMode $timeMode = null;

    /**
     * Set the time mode for this component.
     */
    public function timeMode(TimeMode|string|null $mode): static
    {
        if (is_string($mode)) {
            $mode = TimeMode::tryFrom($mode);
        }

        $this->timeMode = $mode;

        return $this;
    }

    /**
     * Get the resolved time mode.
     */
    public function getTimeMode(): TimeMode
    {
        return $this->timeMode ?? EthiopicConfig::timeMode();
    }
}
