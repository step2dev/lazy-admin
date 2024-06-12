<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;
use Step2dev\LazyAdmin\Services\GenerateRoute;
use Step2dev\LazyUI\LazyComponent;

class Layout extends BaseLayout
{
    public function render(): \Closure|View
    {
        return function (array $data) {
            return view('lazy::layout', $this->mergeData($data))->render();
        };
    }
}
