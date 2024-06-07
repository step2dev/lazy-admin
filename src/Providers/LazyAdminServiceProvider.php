<?php

namespace Step2dev\LazyAdmin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use ReflectionException;
use Step2dev\LazyAdmin\Routing\Router as AdminRouter;

class LazyAdminServiceProvider extends ServiceProvider
{
    /**
     * @throws ReflectionException
     */
    public function boot(): void
    {
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
