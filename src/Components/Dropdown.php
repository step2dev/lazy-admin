<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;
use Step2dev\LazyUI\LazyComponent;

class Dropdown extends LazyComponent
{
    public function __construct(
        public string $position = 'end',
        public string $width = 'w-80',
    ) {}

    public function render(): \Closure|View
    {
        return function (array $data) {
            return lazyView('lazy::dropdown', $this->mergeData($data, [
                'dropdown',
                'dropdown-end' => $this->position === 'end',
                'dropdown-start' => $this->position === 'start',
                'dropdown-top' => $this->position === 'top',
                'dropdown-bottom' => $this->position === 'bottom',
                'dropdown-left' => $this->position === 'left',
                'dropdown-right' => $this->position === 'right',
            ]))->render();
        };
    }
}
