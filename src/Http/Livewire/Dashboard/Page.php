<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Dashboard;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Step2dev\LazyAdmin\Dashboard\DashboardRegistry;

#[Layout('lazy::livewire-layout', ['title' => 'Dashboard'])]
class Page extends Component
{
    public function render(): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        return lazyView('lazy::dashboard.index', [
            'widgets' => app(DashboardRegistry::class)->widgetsFor($user),
        ]);
    }
}
