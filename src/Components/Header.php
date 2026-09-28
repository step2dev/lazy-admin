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
            $authGuard = (string) config('lazy.auth.guard', 'web');
            $user = auth($authGuard)->user();

            return lazyView('lazy::header', $this->mergeData([
                ...$data,
                'authGuard' => $authGuard,
                'user' => $user,
                'dashboardUrl' => Route::has('admin.dashboard')
                    ? route('admin.dashboard')
                    : url((string) config('lazy.admin.home', '/')),
                'logoUrl' => (string) config('lazy.admin.logo', '/main.svg'),
                'appName' => (string) config('app.name'),
                'userName' => $user ? (string) data_get($user, 'name', '') : '',
                'workerType' => $user ? data_get($user, 'worker_type') : null,
                'avatarUrl' => $user
                    ? (string) (data_get($user, 'avatar') ?: config('lazy.admin.avatar', '/img/admin.png'))
                    : '',
                'profileUrl' => Route::has('profile.show') ? route('profile.show') : null,
                'settingsUrl' => Route::has('admin.setting.index') ? route('admin.setting.index') : null,
            ]))->render();
        };
    }
}
