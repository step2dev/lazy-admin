<?php

namespace Step2dev\LazyAdmin\Helpers;

class Menu
{
    protected array $items = [];

    public static function make(string $url, string $label): static
    {
        $menu = new static();
        $menu->addItem($url, $label);

        return $menu;
    }

    public function children(callable $callback): static
    {
        $submenu = new static();
        $callback($submenu);

        $this->items[count($this->items) - 1]['submenu'] = $submenu;

        return $this;
    }

    public function permission(string $permission): static
    {
        $this->items[count($this->items) - 1]['permission'] = $permission;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->items[count($this->items) - 1]['icon'] = $icon;

        return $this;
    }

    public function badge(mixed $badge): static
    {
        $this->items[count($this->items) - 1]['badge'] = $badge;

        return $this;
    }

    public function group(string $label): static
    {
        $this->items[] = ['group' => $label];

        return $this;
    }

    protected function addItem(string $url, string $label): void
    {
        $this->items[] = compact('url', 'label');
    }

    public function toArray(): array
    {
        return $this->getItems();
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
