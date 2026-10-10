<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Exceptions\AdminErrorPages;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('renders only admin HTML errors', function (): void {
    config()->set('lazy.admin.route.prefix', 'admin');

    $render = new AdminErrorPages;

    $admin = $render(new HttpException(404), Request::create('/admin/missing'));
    expect($admin)->not->toBeNull()
        ->and($admin->getStatusCode())->toBe(404)
        ->and($admin->getContent())->toContain('Page not found');

    expect($render(new HttpException(404), Request::create('/public/missing')))->toBeNull();
    expect($render(new HttpException(404), Request::create('/administrator/missing')))->toBeNull();
    expect($render(new HttpException(404), Request::create('/admin/missing', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json'])))->toBeNull();
    expect($render(new HttpException(404), Request::create('/livewire/update')))->toBeNull();
});

it('supports localized admin paths and custom prefixes', function (): void {
    config()->set('lazy.admin.route.prefix', 'control');

    $render = new AdminErrorPages;

    expect($render(new HttpException(403), Request::create('/uk/control/private'))?->getStatusCode())->toBe(403)
        ->and($render(new HttpException(503), Request::create('/control/offline'))?->getStatusCode())->toBe(503)
        ->and($render(new HttpException(404), Request::create('/uk/admin/missing')))->toBeNull();
});

it('honors a configured admin domain', function (): void {
    config()->set('lazy.admin.route.domain', 'admin.example.test');

    $render = new AdminErrorPages;

    expect($render(new HttpException(404), Request::create('https://example.test/admin/missing')))->toBeNull()
        ->and($render(new HttpException(404), Request::create('https://admin.example.test/admin/missing'))?->getStatusCode())->toBe(404);
});

it('avoids the admin shell for guest requests and recoverable session errors', function (): void {
    $renderer = new AdminErrorPages;

    foreach ([401, 403, 419, 429, 500, 503] as $status) {
        $response = $renderer(new HttpException($status), Request::create('/admin/missing'));

        expect($response?->getStatusCode())->toBe($status)
            ->and($response?->getContent())->not->toContain('data-lazy-admin-shell');
    }
});

it('uses the actual admin layout for authenticated missing pages', function (): void {
    $user = new class extends User {};
    $user->id = 123;
    $this->be($user);

    $response = (new AdminErrorPages)(new HttpException(404), Request::create('/admin/missing'));

    expect($response?->getStatusCode())->toBe(404)
        ->and($response?->getContent())->toContain('data-lazy-admin-shell')
        ->and($response?->getContent())->toContain('Back to dashboard');
});

it('does not expose the admin interface to guests', function (): void {
    $renderer = new AdminErrorPages;
    $response = $renderer(new HttpException(404), Request::create('/admin/missing'));

    expect($response?->getStatusCode())->toBe(404)
        ->and($response?->getContent())->not->toContain('data-lazy-admin-shell');
});

it('registers a guarded fallback for unknown admin routes', function (): void {
    $route = collect(Route::getRoutes()->getRoutes())
        ->first(fn ($route) => $route->isFallback && str_contains($route->uri(), 'admin'));

    expect($route)->not->toBeNull()
        ->and($route->gatherMiddleware())->toContain('web')
        ->and($route->gatherMiddleware())->toContain('auth:web');
});

it('provides a separate overridable template for each supported status', function (): void {
    foreach ([401, 403, 404, 419, 429, 500, 503] as $status) {
        expect(view()->exists("lazy::errors.{$status}"))->toBeTrue();
    }

    expect(view()->exists('lazy::errors.partials.admin-layout'))->toBeTrue()
        ->and(view()->exists('lazy::errors.partials.standalone'))->toBeTrue();
});

it('loads localized error titles and actions', function (): void {
    app()->setLocale('uk');

    expect(__('lazy-admin::errors.404.title'))->toBe('Сторінку не знайдено')
        ->and(__('lazy-admin::errors.back_to_dashboard'))->toBe('До панелі керування');
});

it('renders the individual 404 view directly with the real admin shell', function (): void {
    $user = new class extends User {};
    $user->id = 456;
    $this->be($user);

    $response = (new AdminErrorPages)(new HttpException(404), Request::create('/admin/missing'));

    expect($response?->getStatusCode())->toBe(404)
        ->and($response?->getContent())->toContain('data-lazy-admin-shell');
});
