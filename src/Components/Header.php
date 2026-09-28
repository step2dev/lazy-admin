<?php

namespace Step2dev\LazyAdmin\Components;

use Closure;
use Illuminate\Support\Facades\Route;
use Step2dev\LazyUI\LazyComponent;

class Header extends LazyComponent
{
    public function render(): Closure
    {
        return function (array $data) {
            $guard = (string) config('lazy.auth.guard', 'web');
            $user = auth($guard)->user();

            return lazyView('lazy::header', [
                ...$this->mergeData($data),
                'user' => $user,
                'dashboardUrl' => Route::has('admin.dashboard')
                    ? route('admin.dashboard')
                    : url((string) config('lazy.admin.home', '/')),
                'profileUrl' => Route::has('profile.show') ? route('profile.show') : null,
                'settingsUrl' => Route::has('admin.setting.index') ? route('admin.setting.index') : null,
                'avatarUrl' => $user
                    ? (data_get($user, 'avatar') ?: config('lazy.admin.avatar', '/img/admin.png'))
                    : null,
                'workerType' => $user ? data_get($user, 'worker_type') : null,
            ])->render();
        };
    }
}
