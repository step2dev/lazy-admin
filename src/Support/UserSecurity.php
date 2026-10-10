<?php

namespace Step2dev\LazyAdmin\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class UserSecurity
{
    public static function updateEmail(Model $user, string $email): void
    {
        if ($user->getAttribute('email') !== $email
            && Schema::connection($user->getConnectionName())->hasColumn($user->getTable(), 'email_verified_at')) {
            $user->setAttribute('email_verified_at', null);
        }

        $user->setAttribute('email', $email);
    }

    public static function twoFactorAvailable(Model $user): bool
    {
        return ! config('lazy.auth.login.enabled', false)
            && config('fortify.guard', 'web') === config('lazy.auth.guard', 'web')
            && config('fortify-options.two-factor-authentication.confirm', false)
            && config('fortify-options.two-factor-authentication.confirmPassword', false)
            && method_exists($user, 'twoFactorQrCodeSvg')
            && Route::has('two-factor.enable')
            && Route::has('two-factor.confirm')
            && Route::has('two-factor.disable')
            && Route::has('two-factor.qr-code')
            && Route::has('two-factor.recovery-codes')
            && Route::has('password.confirm');
    }
}
