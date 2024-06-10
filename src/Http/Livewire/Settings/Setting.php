<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Setting extends Component
{
    public function render(): View
    {
        return view('lazy::pages.settings.settings');
    }
}
