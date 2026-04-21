<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Concerns;

use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;

trait HasEthiopicDisplayMode
{
    protected ?DisplayMode $displayMode = null;

    public function displayMode(DisplayMode|string|null $mode): static
    {
        if (is_string($mode)) {
            $mode = DisplayMode::fromSimpleMode($mode);
        }

        $this->displayMode = $mode;

        return $this;
    }

    /**
     * Fluent alias for displayMode() — preferred API for Filament developers.
     */
    public function ethiopicDisplayMode(DisplayMode|string|null $mode): static
    {
        return $this->displayMode($mode);
    }

    public function getDisplayMode(): DisplayMode
    {
        return $this->displayMode ?? DisplayMode::fromConfig();
    }
}
