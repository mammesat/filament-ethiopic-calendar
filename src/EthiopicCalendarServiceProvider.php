<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\ServiceProvider;
use Mammesat\FilamentEthiopicCalendar\Enums\DisplayMode;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicCalendar;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicCalendarService;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicTimeService;
use Mammesat\FilamentEthiopicCalendar\Support\EthiopicConfig;
use Mammesat\FilamentEthiopicCalendar\Support\LocaleManager;

class EthiopicCalendarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ethiopic-calendar.php', 'ethiopic-calendar');

        // Core services
        $this->app->singleton(EthiopicCalendarService::class);
        $this->app->singleton(LocaleManager::class);

        $this->app->singleton(EthiopicTimeService::class, function ($app): EthiopicTimeService {
            return new EthiopicTimeService($app->make(LocaleManager::class));
        });

        $this->app->singleton(EthiopicFormatter::class, function ($app): EthiopicFormatter {
            return new EthiopicFormatter(
                $app->make(EthiopicCalendarService::class),
                $app->make(EthiopicTimeService::class),
                $app->make(LocaleManager::class),
            );
        });

        // Backward compatibility: EthiopicCalendar facade
        $this->app->singleton(EthiopicCalendar::class, static fn (): EthiopicCalendar => new EthiopicCalendar());
        $this->app->alias(EthiopicCalendar::class, 'ethiopic-calendar');
    }

    public function boot(): void
    {
        $this->registerMacros();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-ethiopic-calendar');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-ethiopic-calendar');

        FilamentAsset::register([
            AlpineComponent::make('filament-ethiopic-calendar', __DIR__.'/../resources/dist/components/filament-ethiopic-calendar.js'),
        ], 'mammesat/filament-ethiopic-calendar');


        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ethiopic-calendar.php' => config_path('ethiopic-calendar.php'),
            ], 'filament-ethiopic-calendar-config');

            $this->publishes([
                __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/filament-ethiopic-calendar'),
            ], 'filament-ethiopic-calendar-lang');
        }
    }

    protected function registerMacros(): void
    {
        $tooltipMacro = function (bool|string $mode = 'auto') {
            /** @var \Filament\Tables\Columns\TextColumn|\Filament\Infolists\Components\TextEntry $this */
            $component = $this;

            return $this->tooltip(function (mixed $state) use ($mode, $component): ?string {
                if (blank($state)) {
                    return null;
                }

                // Resolve display mode from component if available
                $displayMode = 'gregorian';
                if (method_exists($component, 'getDisplayMode')) {
                    $modeEnum = $component->getDisplayMode();
                    if ($modeEnum instanceof DisplayMode) {
                        $displayMode = $modeEnum->value;
                    }
                } elseif (method_exists($component, 'getExtraAttributes')) {
                    $attributes = $component->getExtraAttributes();
                    if (isset($attributes['data-ethiopic-display-mode'])) {
                        $displayMode = $attributes['data-ethiopic-display-mode'];
                    }
                }

                $targetMode = $mode === true ? 'auto' : $mode;
                if ($targetMode === 'auto') {
                    if ($displayMode === 'gregorian' || $displayMode === DisplayMode::Gregorian->value) {
                        $targetMode = 'ethiopic';
                    } elseif (str_contains($displayMode, 'ethiopic') || $displayMode === DisplayMode::EthiopicAmharic->value) {
                        $targetMode = 'gregorian';
                    } else {
                        // Dual mode or unknown -> return null
                        return null;
                    }
                }

                try {
                    $carbon = \Carbon\Carbon::parse($state, config('app.timezone'))
                        ->setTimezone(EthiopicConfig::timezone());
                    $dateTimeString = $carbon->format('Y-m-d H:i:s');
                    $dateString = $carbon->format('Y-m-d');
                } catch (\Throwable) {
                    return null;
                }

                $hasTime = method_exists($component, 'hasTime') 
                    ? $component->hasTime() 
                    : EthiopicConfig::withTime();

                $formatter = app(EthiopicFormatter::class);

                if ($targetMode === 'ethiopic') {
                    return $hasTime 
                        ? $formatter->formatDateTime($dateTimeString, 'ethiopic_amharic', 'ethiopian')
                        : $formatter->formatDate($dateString, 'ethiopic_amharic');
                }

                if ($targetMode === 'gregorian') {
                    return $hasTime 
                        ? $formatter->formatDateTime($dateTimeString, 'gregorian', 'gregorian')
                        : $formatter->formatDate($dateString, 'gregorian');
                }

                return null;
            });
        };

        TextColumn::macro('tooltipAlternate', $tooltipMacro);
        TextEntry::macro('tooltipAlternate', $tooltipMacro);
    }
}
