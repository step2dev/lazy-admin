<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Navigation\Breadcrumb\BreadcrumbManager;
use Step2Dev\LazyBreadcrumb\Breadcrumbs;
use Step2Dev\LazyBreadcrumb\Breadcrumbs\Trail;
use Step2Dev\LazyBreadcrumb\LazyBreadcrumbServiceProvider;

it('keeps the legacy breadcrumb manager usable without debug output', function (): void {
    $manager = new BreadcrumbManager;
    $html = $manager->addItem('/admin', 'Dashboard')->addItem('/admin/user', 'Users')->render()->render();

    expect($html)->toContain('href="/admin"', 'aria-current="page">Users');
});

it('registers the breadcrumb dependency', function (): void {
    expect(app()->getProvider(LazyBreadcrumbServiceProvider::class))->not->toBeNull();
});

it('renders route breadcrumbs from lazy breadcrumb with route parameters', function (): void {
    Route::get('/breadcrumb-users/{user}', fn () => view('lazy::breadcrumb-trail'))->name('breadcrumb.users.edit');
    Breadcrumbs::for('breadcrumb.users.edit', function (Trail $trail, array $parameters): void {
        $trail->push('Users', '/breadcrumb-users');
        $trail->push('User '.$parameters['user'], '/breadcrumb-users/'.$parameters['user']);
    });

    $this->get('/breadcrumb-users/42')->assertOk()
        ->assertSee('href="/breadcrumb-users"', false)
        ->assertSee('aria-current="page">User 42', false);
});

it('does not render empty breadcrumb navigation', function (): void {
    Route::get('/breadcrumb-empty', fn () => view('lazy::breadcrumb-trail'))->name('breadcrumb.empty');

    $this->get('/breadcrumb-empty')->assertOk()->assertDontSee('<nav', false);
});

it('escapes breadcrumb labels', function (): void {
    Route::get('/breadcrumb-escaped', fn () => view('lazy::breadcrumb-trail'))->name('breadcrumb.escaped');
    Breadcrumbs::for('breadcrumb.escaped', function (Trail $trail): void {
        $trail->push('<script>alert(1)</script>', '/breadcrumb-escaped');
    });

    $this->get('/breadcrumb-escaped')->assertOk()
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false);
});
