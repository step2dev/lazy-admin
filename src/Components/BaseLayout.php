<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;
use Step2dev\LazyAdmin\Services\GenerateRoute;
use Step2dev\LazyUI\LazyComponent;

class BaseLayout extends LazyComponent
{
    public array $routes = [];

    public function __construct()
    {
        $this->routes = GenerateRoute::make()->generateRoutes();
    }

    public function render(): \Closure|View
    {
        return function (array $data) {
            return view('lazy::base-layout', $this->mergeData($data))->render();
        };
    }
}
