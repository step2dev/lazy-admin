<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Step2dev\LazyAdmin\Dashboard\DashboardRegistry;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardRegistry $dashboard) {}

    public function __invoke(): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        if (config('lazy.admin.permissions.enforce', true)) {
            abort_unless($user && $user->can('dashboard.view'), 403);
        }

        return lazyView('lazy::dashboard.index', [
            'widgets' => $this->dashboard->widgetsFor($user),
        ]);
    }
}
