<?php

namespace Step2dev\LazyAdmin\Services;

use Illuminate\Support\Facades\Route;

class GenerateRoute
{
    final public static function make(): static
    {
        return new static;
    }

    /** @noinspection MethodShouldBeFinalInspection */
    public function generateRoutes(): array
    {
        $defaultRoutes = $this->getDefaultRoutes();

        $routes = [];

        $routeName = $this->getRouteName();

        foreach ($defaultRoutes as $key => $route) {
            $routes[$key] = null;

            if ($route['name'] && str($routeName)->contains($route['showOn'])) {
                if (in_array($key, ['index', 'create'])) {
                    $routes[$key] = [
                        'url' => route($route['name']),
                        'target' => $route['target'] ?? '_self',
                    ];
                } else {
                    $routes[$key] = [
                        'url' => $route['url'] ?? route($route['name'], $this->getRouteParameters()),
                        'target' => $route['target'] ?? '_self',
                    ];
                }
            }
        }

        return $routes;
    }

    public function getDefaultRoutes(): array
    {
        return [
            'index' => [
                'name' => $this->getRouteIndexName(),
                'showOn' => [],
            ],
            'create' => [
                'name' => $this->getRouteCreateName(),
                'showOn' => ['index', 'edit'],
            ],
            'show' => [
                'name' => $this->getRouteShowName(),
                'showOn' => ['edit'],
            ],
            'edit' => [
                'name' => $this->getRouteEditName(),
                'showOn' => ['show'],
            ],
            'destroy' => [
                'name' => $this->getRouteDestroyName(),
                'showOn' => ['show', 'edit'],
            ],
        ];
    }

    public function getRouteIndexName(): ?string
    {
        return $this->generateRouteNameByKey();
    }

    public function generateRouteNameByKey(string $key = 'index'): ?string
    {
        $routeName = $this->getRoutePrefixNameWithDot().$key;

        if (Route::has($routeName)) {
            return $routeName;
        }

        return null;
    }

    public function getRoutePrefixNameWithDot(): ?string
    {
        return $this->getRoutePrefixName().'.';
    }

    public function getRoutePrefixName(): ?string
    {
        return str($this->getRouteName())->beforeLast('.');
    }

    public function getRouteName(): ?string
    {
        return Route::currentRouteName();
    }

    public function getRouteCreateName(): ?string
    {
        return $this->generateRouteNameByKey('create');
    }

    public function getRouteShowName(): ?string
    {
        return $this->generateRouteNameByKey('show');
    }

    public function getRouteEditName(): ?string
    {
        return $this->generateRouteNameByKey('edit');
    }

    public function getRouteDestroyName(): ?string
    {
        return $this->generateRouteNameByKey('destroy');
    }

    public function getRouteParameters(): ?array
    {
        return request()->route()?->parameters();
    }
}
