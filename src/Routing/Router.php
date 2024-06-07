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
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function admin(): Closure
    {
        return function (Closure $callback, array $attributes = []) {
            $attributes = array_merge($attributes, $this->configProvider->getAdminRouteAttributes());

            $this->group(array_filter($attributes), $callback);
        };
    }

    public static function getRoutePath(): string
    {
        return base_path(config('lazy.admin.route_settings.path', 'routes/admin.php'));
    }
}
