<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Step2dev\LazyAdmin\Settings\SettingsRegistry;

class SettingController extends Controller
{
    public function __construct(private readonly SettingsRegistry $settings) {}

    public function index(Request $request): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        if (config('lazy.admin.permissions.enforce', true)) {
            abort_unless($user && $user->can('settings.view'), 403);
        }

        $sections = $this->settings->sectionsFor($user);
        $requested = trim((string) $request->query('section', ''));
        $active = collect($sections)->firstWhere('id', $requested) ?? ($sections[0] ?? null);

        abort_if($active === null, 404);

        return lazyView('lazy::settings.index', [
            'sections' => $sections,
            'activeSection' => $active,
        ]);
    }
}
