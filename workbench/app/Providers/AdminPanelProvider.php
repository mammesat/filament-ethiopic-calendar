<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Workbench\App\Models\TestSetting;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->discoverResources(
                in: __DIR__ . '/../Filament/Resources',
                for: 'Workbench\\App\\Filament\\Resources',
            )
            ->discoverPages(
                in: __DIR__ . '/../Filament/Pages',
                for: 'Workbench\\App\\Filament\\Pages',
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        try {
            if (Schema::hasTable('test_settings')) {
                $setting = TestSetting::find(1);

                if ($setting) {
                    config([
                        'ethiopic-calendar.display_mode' => $setting->display_mode,
                        'ethiopic-calendar.calendar_locale' => $setting->calendar_locale,
                        'ethiopic-calendar.locale' => $setting->calendar_locale,
                        'ethiopic-calendar.with_time' => (bool) $setting->with_time,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            //
        }
    }
}
