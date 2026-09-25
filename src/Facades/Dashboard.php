<?php

namespace Step2dev\LazyAdmin\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use Step2dev\LazyAdmin\Dashboard\DashboardRegistry;

/**
 * @method static void registerWidget(string $id, string $label, Closure $value, ?string $description = null, ?string $route = null, ?string $permission = null, int $priority = 100)
 *
 * @see DashboardRegistry
 */
class Dashboard extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DashboardRegistry::class;
    }
}
