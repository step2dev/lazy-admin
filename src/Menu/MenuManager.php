<?php

namespace Step2dev\LazyAdmin\Menu;

use ArrayAccess;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\EnumeratesValues;

class MenuManager extends Collection
{
    protected Menu $menu;

    public function __construct(Menu $menu, $items = [])
    {
        $this->menu = $menu;
        parent::__construct($items);
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

    public function toArray(): array
    {
        return $this->items;
    }

    public function push(...$values): static
    {
        foreach ($values as $value) {
            $this->items[] = $value;
        }

        return $this;
    }

    public function filter(callable $callback = null): static
    {
        if ($callback) {
            return new static(new Menu, Arr::where($this->items, $callback));
        }

        return new static(new Menu,array_filter($this->items));
    }
}
