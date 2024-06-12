<?php

namespace Step2dev\LazyAdmin\Navigation\Breadcrumb;

use Illuminate\Support\Collection;

class BreadcrumbManager extends Collection
{
    public function addItem(string $route, string $label, ?string $icon = null): self
    {
        $this->push(compact('route', 'label', 'icon'));

        return $this;
    }
}
