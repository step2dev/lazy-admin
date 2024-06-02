<?php

namespace Step2dev\LazyAdmin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Step2dev\LazyAdmin\Services\RouteService;
use Step2dev\LazyAdmin\Services\SettingService;

class LazyAdminServiceProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void
    {
        parent::register();
        $this->app->singleton('settings', fn($app) => new SettingService());
    }

    public function boot(): void
    {
        $this->routes(function () {
            $this->configureRateLimiting();

            RouteService::generateRoutes();
        });
    }

    private function configureRateLimiting(): void
    {

    }
}
