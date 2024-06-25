<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('lazy::auth.login');
    }

    public function login(Request $request)
    {
        $loginFields = config('lazy.auth.login_fields');
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        foreach ($loginFields as $field) {
            if (Auth::attempt([$field => $request->login, 'password' => $request->password])) {
                return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
