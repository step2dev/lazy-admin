<?php

namespace Step2dev\LazyAdmin\Routing;

use Closure;
use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;
use Step2dev\LazyAdmin\Routing\Router as AdminRouter;

/**
 * Class     Router
 *
 * @author   CrazyBoy49z <yura.finiv@gmail.com>
 *
 * @mixin \Illuminate\Routing\Router
 */
class Router
{
    public function admin(): Closure
    {
        return function (Closure $callback, array $attributes = []) {
            $attributes = array_merge($attributes, [
                'prefix' => AdminRouter::getRoutePrefix(),
                'middleware' => AdminRouter::getRouteMiddleware(),
                'as' => AdminRouter::getRoutePrefixName(),
                'domain' => AdminRouter::getRouteDomain(),
            ]);

            $this
                ->group(array_filter($attributes), $callback);

        };
    }

    public static function getRoutePrefixName(): string
    {
        return trim(config('lazy.admin.route_settings.name', 'admin.'), '.').'.';
    }

    public static function getRouteDomain(): string
    {
        return (string) config('lazy.admin.route_settings.domain', '');
    }

    public static function getRouteMiddleware(): array
    {
        return array_filter(
            config('lazy.admin.route.middleware', ['web', 'auth'])
        );
    }

    public static function getRoutePrefix(): string
    {
        $prefix = trim(config('lazy.admin.route_settings.prefix', 'admin'), '/');

        return app(LocalizationInterface::class)->setRouteLocale($prefix);
    }

    public static function getRoutePath(): string
    {
        return base_path(config('lazy.admin.route_settings.path', 'routes/admin.php'));
    }

    public static function exceptRegexWhere(): string
    {
        return '^(?!.*\b'.config('lazy/admin.route_settings.prefix').'\b).*';
    }
}
