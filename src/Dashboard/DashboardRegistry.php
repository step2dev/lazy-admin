<?php

namespace Step2dev\LazyAdmin\Dashboard;

use Closure;
use Illuminate\Support\Facades\Route;

class DashboardRegistry
{
    /** @var array<string, array{id:string,label:string,value:Closure,description:?string,route:?string,permission:?string,priority:int}> */
    private array $widgets = [];

    public function registerWidget(
        string $id,
        string $label,
        Closure $value,
        ?string $description = null,
        ?string $route = null,
        ?string $permission = null,
        int $priority = 100,
    ): void {
        $this->widgets[$id] = compact('id', 'label', 'value', 'description', 'route', 'permission', 'priority');
    }

    /** @return list<array{id:string,label:string,value:mixed,description:?string,url:?string}> */
    public function widgetsFor(?object $user): array
    {
        $widgets = array_values(array_filter(
            $this->widgets,
            fn (array $widget): bool => $this->allowed($user, $widget['permission']),
        ));

        usort($widgets, static fn (array $a, array $b): int => $a['priority'] <=> $b['priority']);

        return array_map(function (array $widget): array {
            return [
                'id' => $widget['id'],
                'label' => $widget['label'],
                'value' => ($widget['value'])(),
                'description' => $widget['description'],
                'url' => $this->resolveUrl($widget['route']),
            ];
        }, $widgets);
    }

    private function resolveUrl(?string $route): ?string
    {
        if ($route === null) {
            return null;
        }

        return Route::has($route) ? route($route) : $route;
    }

    private function allowed(?object $user, ?string $permission): bool
    {
        if ($permission === null || ! config('lazy.admin.permissions.enforce', true)) {
            return true;
        }

        return $user !== null && method_exists($user, 'can') && (bool) $user->can($permission);
    }
}
