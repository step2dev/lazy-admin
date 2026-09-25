<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return lazyView('lazy::auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $guard = Auth::guard((string) config('lazy.auth.guard', 'web'));
        $loginFields = array_values(array_filter((array) config('lazy.auth.login_fields', ['email'])));

        foreach ($loginFields as $field) {
            if ($guard->attempt([
                $field => $request->string('email')->toString(),
                'password' => $request->string('password')->toString(),
            ], $request->boolean('remember'))) {
                $request->session()->regenerate();

                $redirectRoute = (string) config('lazy.auth.login.redirect_route', 'admin.dashboard');

                return Route::has($redirectRoute)
                    ? redirect()->intended(route($redirectRoute))
                    : redirect()->intended((string) config('lazy.admin.home', '/'));
            }
        }

        return back()
            ->withErrors(['email' => __('auth.failed')])
            ->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard((string) config('lazy.auth.guard', 'web'))->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Route::has('login')
            ? redirect()->route('login')
            : redirect((string) config('lazy.admin.home', '/'));
    }
}
