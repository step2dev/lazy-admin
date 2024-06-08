<?php

namespace Step2dev\LazyAdmin\Routing;

use Closure;
/**
 * Class     Router
 *
 * @author   CrazyBoy49z <yura.finiv@gmail.com>
 *
 * @mixin \Illuminate\Routing\Router
 */
class Router
{
    public static ConfigProvider $configProvider;

    public function __construct()
    {
        self::$configProvider = new ConfigProvider();
    }

    public function admin(): Closure
    {
        $adminAttributes = self::$configProvider->getAdminRouteAttributes();

        return function (Closure $callback, array $attributes = []) use ($adminAttributes) {
            $attributes = array_merge($attributes,  $adminAttributes);

            $this->group(array_filter($attributes), $callback);
        };
    }

    public static function configProvider(): ConfigProvider
    {
        return self::$configProvider;
    }

    public static function getRoutePath(): string
    {
        return self::$configProvider->getRoutePath();
    }

    public static function exceptRegexWhere(): string
    {
        return self::$configProvider->exceptRegexWhere();
    }
}
