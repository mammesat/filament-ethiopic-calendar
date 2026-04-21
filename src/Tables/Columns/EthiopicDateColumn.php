<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Mammesat\FilamentEthiopicCalendar\Concerns\HasEthiopicFormatting;

class EthiopicDateColumn extends TextColumn
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
