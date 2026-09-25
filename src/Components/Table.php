<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;
use Step2dev\LazyUI\LazyComponent;

class Table extends LazyComponent
{
    public function __construct(
        public bool $zebra = false,
        public bool $compact = false,
    ) {}

    public function render(): \Closure|View
    {
        return function (array $data) {
            return lazyView('lazy::table', $this->mergeData($data, [
                'overflow-x-auto rounded-2xl border border-base-300 bg-base-100',
            ]))->render();
        };
    }
}
