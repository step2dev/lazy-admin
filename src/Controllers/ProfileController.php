<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Step2dev\LazyAdmin\Support\AdminActivity;
use Step2dev\LazyAdmin\Support\UserSecurity;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = $this->user();

        return lazyView('lazy::users.profile', [
            'user' => $user,
            'twoFactorAvailable' => UserSecurity::twoFactorAvailable($user),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $this->user();
        $validated = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique($user->getTable(), 'email')->ignore($user->getKey(), $user->getKeyName())],
            'current_password' => ['required', 'current_password:'.config('lazy.auth.guard', 'web')],
            'roles' => ['prohibited'],
            'password' => ['prohibited'],
        ]);
        $old = $user->only(['name', 'email']);
        $user->setAttribute('name', $validated['name']);
        UserSecurity::updateEmail($user, $validated['email']);
        $user->save();
        AdminActivity::log('profile_updated', 'Profile updated', $user, old: $old, new: $user->only(['name', 'email']));

        return back()->with('status', __('lazy-admin::users.profile_updated'));
    }

    public function password(Request $request): RedirectResponse
    {
        $user = $this->user();
        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password:'.config('lazy.auth.guard', 'web')],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);
        $user->setAttribute('password', Hash::make($validated['password']));
        $user->setAttribute($user->getRememberTokenName(), Str::random(60));
        $user->save();
        $request->session()->regenerate();
        AdminActivity::log('password_changed', 'User password changed', $user, properties: ['self' => true]);

        return back()->with('status', __('lazy-admin::users.password_changed'));
    }

    private function user(): Model
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();
        abort_unless($user instanceof Model, 403);

        return $user;
    }
}
