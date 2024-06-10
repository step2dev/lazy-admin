<?php

namespace Step2dev\LazyAdmin\Facades;

use Illuminate\Support\Facades\Facade;
use Step2dev\LazyAdmin\Menu\MenuManager;

class Menu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MenuManager::class;
    }
}
