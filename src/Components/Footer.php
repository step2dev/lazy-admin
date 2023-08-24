<?php

namespace Step2dev\LazyAdmin\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Step2dev\LazyUI\LazyComponent;

class Footer extends LazyComponent
{
    public function render(): Closure|View
    {
        return function (array $data) {
            return view('lazy::footer', $this->mergeData($data))->render();
        };
    }
}
