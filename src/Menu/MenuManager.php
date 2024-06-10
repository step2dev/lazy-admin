<?php

namespace Step2dev\LazyAdmin\Menu;

use Illuminate\Support\Collection;

class MenuManager extends Collection
{
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
}
