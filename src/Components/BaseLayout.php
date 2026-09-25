<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;
use Step2dev\LazyAdmin\Services\GenerateRoute;
use Step2dev\LazyUI\LazyComponent;

class BaseLayout extends LazyComponent
{
    public array $routes = [];

    public function __construct(?array $routes = null)
    {
        $this->routes = array_replace(
            GenerateRoute::make()->generateRoutes(),
            $routes ?? [],
        );
    }

    public function render(): \Closure|View
    {
        return function (array $data) {
            return lazyView('lazy::base-layout', $this->mergeData($data))->render();
        };
    }
}
