<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicDatePicker;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Mammesat\FilamentEthiopicDatePicker\Services\EthiopicCalendar;

class EthiopicDatePickerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ethiopic-calendar.php', 'ethiopic-calendar');

        $this->app->singleton(EthiopicCalendar::class, static fn (): EthiopicCalendar => new EthiopicCalendar());
        $this->app->alias(EthiopicCalendar::class, 'ethiopic-calendar');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-ethiopic-date-picker');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-ethiopic-date-picker');

        FilamentAsset::register([
            AlpineComponent::make('filament-ethiopic-date-picker', __DIR__.'/../resources/dist/components/filament-ethiopic-calendar.js'),
        ], 'mammesat/filament-ethiopic-date-picker');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ethiopic-calendar.php' => config_path('ethiopic-calendar.php'),
            ], 'filament-ethiopic-date-picker-config');
        }
    }
}
