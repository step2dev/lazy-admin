<?php

namespace Step2dev\LazyAdmin\Routing;

use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;

class ConfigProvider
{
    public function getAdminRouteAttributes(): array
    {
        return [
            'prefix' => $this->getRoutePrefix(),
            'middleware' => $this->getRouteMiddleware(),
            'as' => $this->getRoutePrefixName(),
            'domain' => $this->getRouteDomain(),
        ];
    }

    public function getRoutePrefixName(): string
    {
        return trim(config('lazy.admin.route.name', 'admin.'), '.').'.';
    }

    public function getRouteDomain(): string
    {
        return (string) config('lazy.admin.route.domain', '');
    }

    public function getRouteMiddleware(): array
    {
        return array_filter(
            config('lazy.admin.route.middleware', ['web', 'auth'])
        );
    }

    public function getRoutePrefix(): string
    {
        $prefix = trim(config('lazy.admin.route.prefix', 'admin'), '/');

        return app(LocalizationInterface::class)->setRouteLocale($prefix);
    }

    public function getRoutePath(): string
    {
        return base_path(config('lazy.admin.route.path', 'routes/admin.php'));
    }

    public function exceptRegexWhere(): string
    {
        return '^(?!.*\b'.config('lazy.admin.route.prefix').'\b).*';
    }

    public function getRouteName(string $name): string
    {
        if (str_starts_with($name, $this->getRoutePrefixName())) {
            return $name;
        }

        return $this->getRoutePrefixName().$name;
    }
}
