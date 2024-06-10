<?php

namespace Step2dev\LazyAdmin\Menu;

use Illuminate\Support\Collection;

class MenuManager extends Collection
{
    public function addItem(
        string $route,
        string $label,
        string|null $icon = null,
        array|null $children = null,
        string|null $permission = null,
    ): static
    {
        $children ??= [];

        return $this->push(compact('route', 'label', 'icon', 'permission', 'children'));
    }
}
