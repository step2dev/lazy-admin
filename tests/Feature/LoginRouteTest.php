<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Providers\LazyAdminServiceProvider as AdminRouteServiceProvider;
use Step2dev\LazyAdmin\Tests\TestCase;

uses(
    new class extends TestCase
    {
        protected function getPackageProviders($app): array
        {
            return [
                ...parent::getPackageProviders($app),
                AdminRouteServiceProvider::class,
            ];
        }
    }
);

it('registers the login route with a web session and without authentication', function (): void {
    $login = Route::getRoutes()->getByName('login');

    expect($login)->not->toBeNull()
        ->and($login->uri())->toBe('login')
        ->and($login->gatherMiddleware())->toContain('web', 'guest')
        ->not->toContain('auth');
});
