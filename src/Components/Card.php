<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;
use Step2dev\LazyUI\LazyComponent;

class Card extends LazyComponent
{
    public function __construct(
        public ?string $href = null,
        public bool $hover = false,
    ) {}

    public function render(): \Closure|View
    {
        return function (array $data) {
            return lazyView('lazy::card', $this->mergeData($data, [
                'card',
                'border border-base-300 bg-base-100 shadow-sm',
                'transition hover:shadow-md' => $this->hover || $this->href !== null,
            ]))->render();
        };
    }
}
