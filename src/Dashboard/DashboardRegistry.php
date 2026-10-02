<?php

namespace Step2dev\LazyAdmin\Dashboard;

use Closure;
use Illuminate\Support\Facades\Route;

class DashboardRegistry
{
    /** @var array<string, array<string,mixed>> */
    private array $widgets = [];

    public function registerWidget(
        string $id,
        string $label,
        Closure $value,
        ?string $description = null,
        ?string $route = null,
        ?string $permission = null,
        int $priority = 100,
        ?string $group = null,
        string $tone = 'neutral',
        ?Closure $progress = null,
    ): void {
        $tone = in_array($tone, ['neutral', 'info', 'success', 'warning', 'error'], true)
            ? $tone
            : 'neutral';

        $this->widgets[$id] = [
            'type' => 'metric',
            ...compact(
                'id',
                'label',
                'value',
                'description',
                'route',
                'permission',
                'priority',
                'group',
                'tone',
                'progress',
            ),
        ];
    }

    public function registerCustomWidget(
        string $id,
        string $view,
        ?Closure $data = null,
        ?string $group = null,
        int $span = 6,
        ?string $permission = null,
        int $priority = 100,
    ): void {
        $this->widgets[$id] = [
            'type' => 'custom',
            'id' => $id,
            'view' => $view,
            'data' => $data,
            'group' => $group,
            'span' => max(1, min(12, $span)),
            'permission' => $permission,
            'priority' => $priority,
        ];
    }

    /** @return list<array<string,mixed>> */
    public function widgetsFor(?object $user): array
    {
        $widgets = array_values(array_filter(
            $this->widgets,
            fn (array $widget): bool => $this->allowed($user, $widget['permission']),
        ));

        usort($widgets, static fn (array $a, array $b): int => $a['priority'] <=> $b['priority']);

        return array_map(function (array $widget): array {
            if (($widget['type'] ?? 'metric') === 'custom') {
                return [
                    'type' => 'custom',
                    'id' => $widget['id'],
                    'view' => $widget['view'],
                    'data' => $widget['data'] !== null ? (array) ($widget['data'])() : [],
                    'group' => $widget['group'],
                    'span' => $widget['span'],
                ];
            }

            return [
                'type' => 'metric',
                'id' => $widget['id'],
                'label' => $widget['label'],
                'value' => ($widget['value'])(),
                'description' => $widget['description'],
                'url' => $this->resolveUrl($widget['route']),
                'group' => $widget['group'],
                'tone' => $widget['tone'],
                'progress' => $widget['progress'] !== null
                    ? max(0, min(100, (float) ($widget['progress'])()))
                    : null,
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
