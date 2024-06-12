<?php

namespace Step2dev\LazyAdmin\Facades;

use Illuminate\Support\Facades\Facade;
use Step2dev\LazyAdmin\Menu\MenuManager;

/**
 * @method static MenuManager addItem(string $key, string $title, string $icon = null, string $route = null, string $permission = null, string $parent = null, int $order = 0)
 */
class Menu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MenuManager::class;
    }
}
