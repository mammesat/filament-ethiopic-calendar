<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicCalendar;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicCalendarService;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicFormatter;
use Mammesat\FilamentEthiopicCalendar\Services\EthiopicTimeService;
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
}
