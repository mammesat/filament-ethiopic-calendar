<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Mammesat\FilamentEthiopicCalendar\Concerns\HasEthiopicFormatting;

class EthiopicDateEntry extends TextEntry
{
    use HasEthiopicFormatting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-calendar');

        $this->formatStateUsing(function (mixed $state): ?string {
            return $this->formatEthiopicState($state);
        });
    }
}
