<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;
use Step2dev\LazyUI\LazyComponent;
use Step2dev\LazyAdmin\Services\GenerateRoute;

class LazyLayout extends LazyComponent
{
    public array $routes = [];

    public function __construct(array $routes = [])
    {
        $this->routes = GenerateRoute::make()->generateRoutes();
    }

    public function render(): \Closure|View
    {
        return function (array $data) {
            return view('lazy::layout', $this->mergeData($data))->render();
        };
    }
}
