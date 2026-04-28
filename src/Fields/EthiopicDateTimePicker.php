<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Fields;

use Filament\Forms\Components\DateTimePicker;
use Mammesat\FilamentEthiopicCalendar\Concerns\HasEthiopicDisplayMode;
use Mammesat\FilamentEthiopicCalendar\Concerns\HasEthiopicTimeMode;
use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Enums\TimeMode;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter;
use Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig;

/**
 * Ethiopian Calendar & Time Engine — Primary Filament v5 Field.
 *
 * Usage:
 *   EthiopicDateTimePicker::make('date')
 *       ->ethiopic()
 *       ->displayMode('ethiopic')    // ethiopic | gregorian | dual
 *       ->timeMode('gregorian')      // gregorian | ethiopian | dual
 *       ->calendarLocale('am')       // am | en
 *       ->withTime()
 */
class EthiopicDateTimePicker extends DateTimePicker
{
    use HasEthiopicDisplayMode;
    use HasEthiopicTimeMode;

    protected string $view = 'filament-ethiopic-calendar::forms.components.ethiopic-date-picker';

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
        $this->timezone(EthiopicConfig::timezone());

        $this->helperText(function (self $component, mixed $state) {
            if (! $component->ethiopicHelperEnabled) {
                return null;
            }

            $display = $component->formatStateForDisplay($state);

            if ($display === null) {
                return null;
            }

            return new \Illuminate\Support\HtmlString(
                $display . '<br><span class="text-xs text-gray-500" style="opacity: 0.8; font-size: 0.75rem;">Stored as: Gregorian (system standard)</span>'
            );
        });

        $this->suffix(function (self $component, mixed $state): ?string {
            if (! $component->ethiopicSuffixEnabled) {
                return null;
            }

            return $component->formatStateForDisplay($state);
        });

        $this->formatStateUsing(function (self $component, mixed $state): ?string {
            if ($state === null || trim((string) $state) === '') {
                return null;
            }

            if ($component->isDisabled()) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $state)) {
                    $ethiopic = $component->formatStateForDisplay($state);

                    return $ethiopic ?? $state;
                }
            }

            return $state;
        });
    }

    // ──────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────

    /**
     * Convenience method: configure for full Ethiopian mode.
     *
     * Sets displayMode to 'ethiopic' and timeMode to 'ethiopian'.
     */
    public function ethiopic(): static
    {
        $this->displayMode(DisplayMode::EthiopicAmharic);
        $this->timeMode(TimeMode::Ethiopian);

        return $this;
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
     *
     * @param  string  $locale  'am' for Amharic, 'en' for English transliteration
     */
    public function calendarLocale(string $locale): static
    {
        $this->calendarLocaleOverride = in_array($locale, ['am', 'en'], true) ? $locale : 'am';

        return $this;
    }

    /**
     * Get the resolved calendar locale for the popup UI.
     */
    public function getCalendarLocale(): string
    {
        return $this->calendarLocaleOverride ?? EthiopicConfig::calendarLocale();
    }

    /**
     * Enable or disable the time picker.
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
        return $this->withTimeOverride ?? EthiopicConfig::withTime();
    }

    // ──────────────────────────────────────────────
    // Display formatting (delegates to EthiopicFormatter)
    // ──────────────────────────────────────────────

    protected function formatStateForDisplay(mixed $state): ?string
    {
        if ($state === null || trim((string) $state) === '') {
            return null;
        }

        if (! preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $state)) {
            return null;
        }

        try {
            $carbon = \Carbon\Carbon::parse($state, config('app.timezone'))->setTimezone(EthiopicConfig::timezone());
            $stateToFormat = $this->hasTime() ? $carbon->format('Y-m-d H:i:s') : $carbon->format('Y-m-d');
        } catch (\Throwable) {
            $stateToFormat = trim((string) $state);
        }

        $formatter = app(EthiopicFormatter::class);

        // Determine time mode for display
        $timeMode = $this->getTimeMode();

        // If time is not enabled, format date only
        if (! $this->hasTime()) {
            return $formatter->formatDate(
                trim(explode(' ', $stateToFormat)[0]),
                $this->getDisplayMode(),
            );
        }

        return $formatter->formatDateTime(
            $stateToFormat,
            $this->getDisplayMode(),
            $timeMode,
        );
    }
}
