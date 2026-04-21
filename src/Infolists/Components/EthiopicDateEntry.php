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

            try {
                $parsedDate = Carbon::parse($state)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }

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

            try {
                $time = Carbon::parse($state)->format('H:i');
            } catch (\Throwable) {
                return $formattedDate;
            }

            $formattedTime = $calendar->formatEthiopianTime($time);

            if ($formattedTime === null) {
                return $formattedDate;
            }

            return $formattedDate . ' ' . $formattedTime;
        });
    }
}
