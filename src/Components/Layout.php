<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;

class Layout extends BaseLayout
{
    public function render(): \Closure|View
    {
        return function (array $data) {
            return view('lazy::layout', $this->mergeData($data))->render();
        };
    }
}
