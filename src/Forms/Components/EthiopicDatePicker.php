<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicDatePicker\Forms\Components;

use Filament\Forms\Components\DateTimePicker;
use Mammesat\FilamentEthiopicDatePicker\Concerns\HasEthiopicDisplayMode;
use Mammesat\FilamentEthiopicDatePicker\Services\EthiopicCalendar;

class EthiopicDatePicker extends DateTimePicker
{
    use HasEthiopicDisplayMode;

    protected string $view = 'filament-ethiopic-date-picker::forms.components.ethiopic-date-picker';

    protected bool $ethiopicHelperEnabled = true;

    protected bool $ethiopicSuffixEnabled = false;

    protected ?string $calendarLocaleOverride = null;

    protected ?bool $withTimeOverride = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->native(false);
        $this->firstDayOfWeek(1); // Monday
        $this->suffixIcon('heroicon-m-calendar');
        $this->extraAttributes(['data-weekdays-short' => 'short'], true);

        $this->helperText(function (EthiopicDatePicker $component, mixed $state): ?string {
            if (! $component->ethiopicHelperEnabled) {
                return null;
            }

            if ($state === null || trim((string) $state) === '') {
                return null;
            }

            if (preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $state)) {
                $parsed = explode(' ', (string) $state)[0];

                return app(EthiopicCalendar::class)->formatDisplayLabel($parsed, $component->getDisplayMode());
            }

            return null;
        });

        $this->suffix(function (EthiopicDatePicker $component, mixed $state): ?string {
            if (! $component->ethiopicSuffixEnabled) {
                return null;
            }

            if ($state === null || trim((string) $state) === '') {
                return null;
            }

            if (preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $state)) {
                $parsed = explode(' ', (string) $state)[0];

                return app(EthiopicCalendar::class)->formatDisplayLabel($parsed, $component->getDisplayMode());
            }

            return null;
        });

        // formatStateUsing ensures read-only/disabled components display Ethiopic.
        // The interactive custom Alpine UI manages the conversion using its embedded JS, saving as Gregorian.
        $this->formatStateUsing(function (EthiopicDatePicker $component, mixed $state): ?string {
            if ($state === null || trim((string) $state) === '') {
                return null;
            }

            if ($component->isDisabled()) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $state)) {
                    $parsed = explode(' ', (string) $state)[0];
                    $ethiopic = app(EthiopicCalendar::class)->formatDisplayLabel($parsed, $component->getDisplayMode());

                    return $ethiopic ?? $state;
                }
            }

            return $state;
        });
    }

    /**
     * Toggle Ethiopic helper text display below the field.
     * Enabled by default.
     */
    public function showEthiopicHelper(bool $enabled = true): static
    {
        $this->ethiopicHelperEnabled = $enabled;

        return $this;
    }

    /**
     * Toggle Ethiopic suffix display beside the field input.
     * Disabled by default.
     */
    public function showEthiopicSuffix(bool $enabled = true): static
    {
        $this->ethiopicSuffixEnabled = $enabled;

        if ($enabled) {
            $this->suffixIcon(null);
        }

        return $this;
    }

    /**
     * Set the calendar UI locale (month/day names in the popup).
     * Overrides config('ethiopic-calendar.calendar_locale').
     *
     * @param  string  $locale  'am' for Amharic, 'en' for English transliteration
     */
    public function calendarLocale(string $locale): static
    {
        $this->calendarLocaleOverride = $locale;

        return $this;
    }

    /**
     * Get the resolved calendar locale for the popup UI.
     */
    public function getCalendarLocale(): string
    {
        return $this->calendarLocaleOverride ?? config('ethiopic-calendar.calendar_locale', 'am');
    }

    /**
     * Enable or disable the time picker.
     * When enabled, the picker shows HH:mm fields and stores Y-m-d H:i:s.
     * Disabled by default.
     */
    public function withTime(bool $enabled = true): static
    {
        $this->withTimeOverride = $enabled;

        return $this;
    }

    /**
     * Determine if the time picker should be shown.
     */
    public function hasTime(): bool
    {
        return $this->withTimeOverride ?? config('ethiopic-calendar.with_time', false);
    }
}
