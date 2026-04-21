<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicDatePicker\Tables\Columns;

use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Mammesat\FilamentEthiopicDatePicker\Concerns\HasEthiopicDisplayMode;
use Mammesat\FilamentEthiopicDatePicker\Services\EthiopicCalendar;

class EthiopicDateColumn extends TextColumn
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

            $parsedDate = Carbon::parse($state)->format('Y-m-d');
            $calendar = app(EthiopicCalendar::class);

            $formattedDate = $calendar->formatDisplayLabel(
                $parsedDate,
                $this->getDisplayMode(),
            );

            if ($formattedDate === null) {
                return null;
            }

            if (! config('ethiopic-calendar.with_time', false) || config('ethiopic-calendar.time_format', 'standard') !== 'ethiopian') {
                return $formattedDate;
            }

            $time = Carbon::parse($state)->format('H:i');
            $formattedTime = $calendar->formatEthiopianTime($time);

            if ($formattedTime === null) {
                return $formattedDate;
            }

            return $formattedDate . ' ' . $formattedTime;
        });
    }
}
