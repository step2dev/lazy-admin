<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;
use Step2dev\LazyUI\LazyComponent;

class EmptyState extends LazyComponent
{
    public function __construct(
        public string $title = '',
        public ?string $description = null,
    ) {}

    public function render(): \Closure|View
    {
        return function (array $data) {
            return lazyView('lazy::empty-state', $this->mergeData($data, [
                'rounded-2xl border border-dashed border-base-300 bg-base-100 p-8 text-center',
            ]))->render();
        };
    }
}
