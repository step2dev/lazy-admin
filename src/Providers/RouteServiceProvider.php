<?php

namespace Step2dev\LazyAdmin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            $this->configureRateLimiting();

            Route::name('admin.')
                ->domain(config('lazy.admin.domain'))
                ->middleware(config('lazy.admin.middleware'))
                ->prefix(config('lazy.admin.prefix'))
                ->group(base_path('routes/admin.php'));
        });
    }

    private function configureRateLimiting(): void
    {

    }


}
