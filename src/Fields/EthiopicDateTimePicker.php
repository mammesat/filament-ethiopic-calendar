<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Fields;

use Closure;
use Filament\Forms\Components\DateTimePicker;
use Mammesat\FilamentEthiopicCalendar\Concerns\HasEthiopicDisplayMode;
use Mammesat\FilamentEthiopicCalendar\Concerns\HasEthiopicTimeMode;
use Mammesat\FilamentEthiopicCalendar\Enums\CalendarSystem;
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
 *       ->calendarSystem('gregorian') // ethiopic | gregorian (popup grid)
 *       ->withTime()
 *
 * With the Gregorian calendar system the field renders Filament's own
 * default DateTimePicker, so it behaves exactly like a stock picker.
 */
class EthiopicDateTimePicker extends DateTimePicker
{
    use HasEthiopicDisplayMode;
    use HasEthiopicTimeMode;

    protected string $view = 'filament-ethiopic-calendar::forms.components.ethiopic-date-picker';

    /** null = default: shown for the Ethiopic calendar, hidden for Gregorian. */
    protected ?bool $ethiopicHelperEnabled = null;

    protected CalendarSystem|string|Closure|null $calendarSystem = null;

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

        $this->helperText(fn (self $component, mixed $state): ?\Illuminate\Support\HtmlString => $component->getHelperDisplay($state));

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

            if ($component->isDisabled() && ! $component->isGregorianCalendar()) {
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
     * Convenience preset: full Ethiopian mode.
     *
     * Sets displayMode to ethiopic_amharic, timeMode to ethiopian,
     * and calendar locale to Amharic. One-liner for the most common config.
     */
    public function ethiopic(): static
    {
        return $this
            ->calendarSystem(CalendarSystem::Ethiopic)
            ->displayMode(DisplayMode::EthiopicAmharic)
            ->timeMode(TimeMode::Ethiopian)
            ->calendarLocale('am');
    }

    /**
     * Convenience preset: dual mode (Ethiopian + Gregorian side by side).
     */
    public function dual(): static
    {
        return $this
            ->displayMode(DisplayMode::Dual)
            ->timeMode(TimeMode::Dual);
    }

    /**
     * Convenience preset: pure Gregorian mode.
     *
     * Renders Filament's default Gregorian date picker and formats as Gregorian.
     */
    public function gregorian(): static
    {
        return $this
            ->calendarSystem(CalendarSystem::Gregorian)
            ->displayMode(DisplayMode::Gregorian)
            ->timeMode(TimeMode::Gregorian);
    }

    /**
     * Get the formatted helper HTML for a given state value.
     *
     * Displays the converted Gregorian date when the field is in Ethiopian mode,
     * or the converted Ethiopian date when the field is in Gregorian mode.
     */
    public function getHelperDisplay(mixed $state): ?\Illuminate\Support\HtmlString
    {
        if (! $this->isEthiopicHelperEnabled()) {
            return null;
        }

        $isEthiopic = $this->getDisplayMode() !== DisplayMode::Gregorian;

        if ($isEthiopic) {
            $display = $this->formatStateForGregorianDisplay($state);
            $label = 'Stored as: Gregorian (system standard)';
        } else {
            // When in Gregorian mode, format the helper text as Ethiopian
            $altMode = $this->getCalendarLocale() === 'en' ? DisplayMode::EthiopicEnglish : DisplayMode::EthiopicAmharic;
            try {
                $carbon = \Carbon\Carbon::parse($state, config('app.timezone'))->setTimezone(EthiopicConfig::timezone());
                $stateToFormat = $this->hasTime() ? $carbon->format('Y-m-d H:i:s') : $carbon->format('Y-m-d');
            } catch (\Throwable) {
                $stateToFormat = trim((string) $state);
            }

            $formatter = app(EthiopicFormatter::class);
            $display = $this->hasTime()
                ? $formatter->formatDateTime($stateToFormat, $altMode, $this->getTimeMode())
                : $formatter->formatDate(trim(explode(' ', $stateToFormat)[0]), $altMode);
            $label = 'Ethiopian calendar equivalent';
        }

        $wrapperStyle = $display === null ? 'display: none;' : '';

        return new \Illuminate\Support\HtmlString(
            '<div class="fi-ethiopic-helper-container" style="' . $wrapperStyle . '">' .
            '<span class="fi-ethiopic-gregorian-preview font-medium text-gray-700 dark:text-gray-300">' . e($display ?? '') . '</span><br>' .
            '<span class="text-xs text-gray-500 dark:text-gray-400" style="opacity: 0.8; font-size: 0.75rem;">' . $label . '</span>' .
            '</div>'
        );
    }

    /**
     * Get the formatted Ethiopic preview for a given state value.
     *
     * Use this in helperText closures instead of manually calling EthiopicFormatter:
     *   ->helperText(fn ($state, $component) => $component->getFormattedPreview($state))
     */
    public function getFormattedPreview(mixed $state): ?string
    {
        return $this->formatStateForDisplay($state);
    }

    /**
     * Toggle Ethiopic / Gregorian helper text display below the field.
     * Enabled by default for the Ethiopic calendar, disabled for Gregorian.
     */
    public function showEthiopicHelper(bool $enabled = true): static
    {
        $this->ethiopicHelperEnabled = $enabled;

        return $this;
    }

    /**
     * Fluent alias for showEthiopicHelper().
     */
    public function showGregorianHelper(bool $enabled = true): static
    {
        return $this->showEthiopicHelper($enabled);
    }

    public function isEthiopicHelperEnabled(): bool
    {
        return $this->ethiopicHelperEnabled ?? ! $this->isGregorianCalendar();
    }

    /**
     * Set the calendar grid shown in the popup.
     *
     * Accepts a CalendarSystem, 'ethiopic' / 'gregorian', or a Closure returning
     * either (evaluated with the usual Filament injection: $get, $record, ...).
     * Null falls back to the `calendar_system` config value.
     */
    public function calendarSystem(CalendarSystem|string|Closure|null $system): static
    {
        $this->calendarSystem = $system;

        return $this;
    }

    /**
     * Switch to Filament's default Gregorian date picker (or back to Ethiopic).
     */
    public function gregorianCalendar(bool|Closure $condition = true): static
    {
        return $this->calendarSystem(
            $condition instanceof Closure
                ? fn (self $component): CalendarSystem => $component->evaluate($condition) ? CalendarSystem::Gregorian : CalendarSystem::Ethiopic
                : ($condition ? CalendarSystem::Gregorian : CalendarSystem::Ethiopic),
        );
    }

    public function ethiopicCalendar(bool|Closure $condition = true): static
    {
        return $this->calendarSystem(
            $condition instanceof Closure
                ? fn (self $component): CalendarSystem => $component->evaluate($condition) ? CalendarSystem::Ethiopic : CalendarSystem::Gregorian
                : ($condition ? CalendarSystem::Ethiopic : CalendarSystem::Gregorian),
        );
    }

    public function getCalendarSystem(): CalendarSystem
    {
        $system = $this->evaluate($this->calendarSystem);

        return CalendarSystem::fromValue($system instanceof CalendarSystem || is_string($system) ? $system : null)
            ?? EthiopicConfig::calendarSystem();
    }

    public function isGregorianCalendar(): bool
    {
        return $this->getCalendarSystem() === CalendarSystem::Gregorian;
    }

    /**
     * In Gregorian mode, drop the custom Blade view so Filament renders its
     * built-in embedded DateTimePicker (see ViewComponent::toHtml()).
     */
    public function hasView(): bool
    {
        if ($this->isGregorianCalendar()) {
            return false;
        }

        return parent::hasView();
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

    public function formatStateForDisplay(mixed $state): ?string
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

    /**
     * Format a state value for Gregorian display (used for helper text preview of stored DB value).
     */
    public function formatStateForGregorianDisplay(mixed $state): ?string
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

        if (! $this->hasTime()) {
            return $formatter->formatDate(
                trim(explode(' ', $stateToFormat)[0]),
                DisplayMode::Gregorian,
            );
        }

        return $formatter->formatDateTime(
            $stateToFormat,
            DisplayMode::Gregorian,
            TimeMode::Gregorian,
        );
    }
}
