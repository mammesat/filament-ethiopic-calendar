<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicDatePicker\Infolists\Components;

use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Mammesat\FilamentEthiopicDatePicker\Concerns\HasEthiopicDisplayMode;
use Mammesat\FilamentEthiopicDatePicker\Services\EthiopicCalendar;

class EthiopicDateEntry extends TextEntry
{
    use HasEthiopicDisplayMode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-calendar');

        $this->formatStateUsing(function (mixed $state): ?string {
            if ($state === null) {
                return null;
            }

            $parsed = Carbon::parse($state)->format('Y-m-d');

            return app(EthiopicCalendar::class)->formatDisplayLabel(
                $parsed,
                $this->getDisplayMode(),
            );
        });
    }
}
