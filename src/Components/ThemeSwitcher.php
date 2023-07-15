<?php

namespace Step2dev\LazyAdmin\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Lazyadm\LazyComponent\LazyComponent;
use Step2dev\LazyAdmin\LazyAdmin;

class ThemeSwitcher extends LazyComponent
{
    public function render(): Closure|View
    {
        return function (array $data) {
            return view('lazy::themeswitcher', $this->mergeData($data))->render();
        };
    }
}
