<?php

namespace Step2dev\LazyAdmin\Navigation\Breadcrumb;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class BreadcrumbManager extends Collection
{
    public function addItem(string $route, string $label, ?string $icon = null): self
    {
        $this->push(compact('route', 'label', 'icon'));

        return $this;
    }

    public function render(): View
    {
        return view('lazy::breadcrumb-trail', [
            'items' => $this->isEmpty() ? null : $this->map(fn (array $item): array => [
                'title' => $item['label'],
                'url' => $item['route'],
            ])->all(),
        ]);
    }
}
