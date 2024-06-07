<?php

namespace Step2dev\LazyAdmin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use ReflectionException;
use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;
use Step2dev\LazyAdmin\Routing\Router as AdminRouter;
use Step2dev\LazyAdmin\Services\SettingService;

class LazyAdminServiceProvider extends ServiceProvider
{

    /**
     * @throws ReflectionException
     */
    public function boot(): void
    {

        /** @var Router $router */
        $router = $this->app['router'];
        $router?->mixin(new AdminRouter);

        $this->routes(function () {
            $this->configureRateLimiting();

            Route::admin(function () {
                require AdminRouter::getRoutePath();
            });

        });
    }

    private function configureRateLimiting(): void
    {

    }
}
