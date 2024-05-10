<?php

namespace Step2dev\LazyAdmin\Components;

use Closure;
use Step2dev\LazyUI\LazyComponent;

class Header extends LazyComponent
{
    public function render(): Closure
    {
        return function (array $data) {
            return view('lazy::header', $this->mergeData($data))->render();
        };
    }
}
