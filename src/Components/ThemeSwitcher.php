<?php

namespace Step2dev\LazyAdmin\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Lazyadm\LazyComponent\LazyComponent;

class ThemeSwitcher extends LazyComponent
{
    public function render(): Closure|View
    {
        return function (array $data) {
            $themeToggle = config('lazy.admin.theme-toggle');
            $themes = config('lazy.admin.themes');


            $data['attributes']['theme-toggle'] = $themeToggle;
            $data['attributes']['themes'] = $themes;

            return view('lazy::themeswitcher', $this->mergeData($data))->render();
        };
    }
}
