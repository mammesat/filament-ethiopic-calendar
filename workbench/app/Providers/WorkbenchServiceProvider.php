<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Workbench\App\Models\TestSetting;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function boot(): void
    {
        // Register local workbench views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'workbench');
    }
}
