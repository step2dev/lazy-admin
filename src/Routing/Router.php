<?php

namespace Step2dev\LazyAdmin\Routing;

use Closure;
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
                'name' => AdminRouter::getRoutePrefixName(),
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
        $prefix = config('lazy.admin.route_settings.prefix', 'admin');
        $prefix = trim($prefix, '/');

        if (config('lazy.localization.multi_language')) {
            if (class_exists('\Arcanedev\Localization\Routing')) {
                $prefix = \Arcanedev\Localization\Facades\Localization::setLocale().'/'.$prefix;
            }

            if (class_exists('Mcamara\LaravelLocalization\Facades\LaravelLocalization')) {
                $prefix = \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale().'/'.$prefix;
            }
        }

        return $prefix;
    }

    public static function getRoutePath(): string
    {
        return base_path(config('lazy.admin.route_settings.path', 'routes/admin.php'));
    }
}
