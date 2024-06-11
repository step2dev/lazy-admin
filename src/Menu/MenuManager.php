<?php

namespace Step2dev\LazyAdmin\Menu;

use Illuminate\Support\Collection;

class MenuManager extends Collection
{
    protected Menu $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
        parent::__construct();
    }

    public function addItem(
        string $route,
        string $label,
        ?string $icon = null,
        ?array $children = null,
        ?string $permission = null,
    ): static {
        $children ??= [];

        return $this->push(compact('route', 'label', 'icon', 'permission', 'children'));
    }

    public function createMenu(string $url, string $label): Menu
    {
        return $this->menu->make($url, $label);
    }
}
