<?php

namespace Step2dev\LazyAdmin\Facades;

use Illuminate\Support\Facades\Facade;
use Step2dev\LazyAdmin\Navigation\Menu\MenuManager;

/**
 * @method static MenuManager addItem(string $route, string $label, ?string $icon = null, ?array $children = null, ?string $permission = null)
 */
class Menu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MenuManager::class;
    }
}
