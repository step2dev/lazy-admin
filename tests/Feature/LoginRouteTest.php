<?php

use Illuminate\Support\Facades\Route;

it('allows a guest login route with a web session', function (): void {
    config()->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));

    Route::admin(function (): void {
        Route::get('/login-test', fn () => csrf_token())->name('login-test');
    }, [
        'prefix' => '',
        'as' => '',
        'middleware' => ['web', 'guest'],
    ]);

    Route::getRoutes()->refreshNameLookups();

    $route = Route::getRoutes()->getByName('login-test');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('login-test')
        ->and($route->gatherMiddleware())->toContain('web', 'guest')
        ->not->toContain('auth');

    $response = $this->get('/login-test');

    $response->assertOk();
    expect($response->getContent())->toMatch('/^[A-Za-z0-9]{40}$/');
});

it('honors custom paths and names passed to Route::admin', function (): void {
    Route::admin(function (): void {
        Route::get('/overview', fn () => 'ok')->name('overview');
    }, [
        'prefix' => 'uk/custom-admin',
        'as' => 'custom.',
        'middleware' => ['web'],
    ]);

    Route::getRoutes()->refreshNameLookups();

    $route = Route::getRoutes()->getByName('custom.overview');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('uk/custom-admin/overview')
        ->and($route->gatherMiddleware())->toContain('web')
        ->not->toContain('auth');
});
