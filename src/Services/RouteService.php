<?php

namespace Step2dev\LazyAdmin\Services;

use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;

class RouteService
{
    public static function getRoutePrefixName(): string
    {
        return config('lazy.admin.route_prefix', 'admin.');
    }

    public static function getRouteDomain(): string
    {
        return config('lazy.admin.domain', '');
    }

    public static function getRouteMiddleware(): array
    {
        return config('lazy.admin.middleware', ['web', 'auth']);
    }

    public static function getRoutePrefix(): string
    {
        return config('lazy.admin.prefix', 'admin');
    }

    public static function getRoutePath(): string
    {
        return base_path(config('lazy.admin.route_path', 'routes/admin.php'));
    }

    public static function generateRoutes(): RouteRegistrar
    {
        return Route::name(self::getRoutePrefixName())
            ->domain(self::getRouteDomain())
            ->middleware(self::getRouteMiddleware())
            ->prefix(self::getRoutePrefix())
            ->group(self::getRoutePath());
    }
}
