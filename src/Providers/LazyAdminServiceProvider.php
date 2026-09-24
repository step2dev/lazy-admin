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

            if (config('lazy.auth.login.enabled', false)) {
                Route::admin(function (): void {
                    $uri = (string) config('lazy.admin.route.login.uri', 'login');

                    Route::get($uri, [LoginController::class, 'showLoginForm'])->name('login');
                    Route::post($uri, [LoginController::class, 'login'])->name('login.store');
                    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
                }, [
                    'prefix' => app(LocalizationInterface::class)->setRouteLocale(
                        trim((string) config('lazy.admin.route.login.prefix', ''), '/')
                    ),
                    'as' => '',
                    'middleware' => ['web'],
                ]);
            }

            Route::admin(function (): void {
                require AdminRouter::getRoutePath();
            });
        });
    }

    private function configureRateLimiting(): void {}
}
