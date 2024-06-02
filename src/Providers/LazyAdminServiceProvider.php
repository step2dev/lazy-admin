<?php

namespace Step2dev\LazyAdmin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route as RouteFacade;
use ReflectionException;
use Step2dev\LazyAdmin\Routing\Router as AdminRouter;
use Step2dev\LazyAdmin\Services\SettingService;

class LazyAdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @throws ReflectionException
     */
    public function register(): void
    {
        $this->app->singleton('settings', fn ($app) => new SettingService());
        parent::register();

    }

    public function boot(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];

        $router
            ->mixin(new AdminRouter);

        $this->routes(function () {
            RouteFacade::admin(function () {
                require AdminRouter::getRoutePath();
            });

            $this->configureRateLimiting();

        });

        //
    }

    private function configureRateLimiting(): void
    {

    }
}
