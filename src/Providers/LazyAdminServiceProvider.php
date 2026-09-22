<?php

namespace Step2dev\LazyAdmin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use ReflectionException;
use Step2dev\LazyAdmin\Controllers\LoginController;
use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;
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
                Route::get(config('lazy.admin.route.login.uri', 'login'), [LoginController::class, 'showLoginForm'])->name('login');
            }, [
                'prefix' => app(LocalizationInterface::class)->setRouteLocale(
                    trim((string) config('lazy.admin.route.login.prefix', ''), '/')
                ),
                'as' => '',
                'middleware' => ['web', 'guest'],
            ]);

            Route::admin(function () {
                require AdminRouter::getRoutePath();
            });
        });
    }

    private function configureRateLimiting(): void {}
}
