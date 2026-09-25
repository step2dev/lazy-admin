<?php

declare(strict_types=1);

use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Step2dev\LazyAdmin\Dashboard\DashboardRegistry;
use Step2dev\LazyAdmin\Http\Livewire\Dashboard\Page;
use Step2dev\LazyAdmin\Notifications\NotificationCenter;
use Step2dev\LazyAdmin\Search\SearchRegistry;
use Step2dev\LazyAdmin\Settings\SettingsRegistry;
use Step2dev\LazyAdmin\Support\AdminActivity;

beforeEach(function (): void {
    $this->withoutVite();

    config()->set([
        'database.connections.testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        'lazy.admin.permissions.enforce' => false,
    ]);
});

it('allows modules to register dashboard widgets', function (): void {
    $registry = new DashboardRegistry;

    $registry->registerWidget(
        id: 'orders',
        label: 'Orders',
        value: fn (): int => 42,
        description: 'Open orders',
        priority: 5,
    );

    $widget = collect($registry->widgetsFor(null))->firstWhere('id', 'orders');

    expect($widget)
        ->not->toBeNull()
        ->and($widget['label'])->toBe('Orders')
        ->and($widget['value'])->toBe(42)
        ->and($widget['description'])->toBe('Open orders');
});

it('allows modules to register global search providers', function (): void {
    $registry = new SearchRegistry;

    $registry->register(
        id: 'orders',
        provider: fn (string $query, int $limit): array => [[
            'title' => 'Order '.$query,
            'url' => '/orders/1',
            'description' => 'Example order',
            'type' => 'Order',
        ]],
    );

    $results = $registry->search('ABC', null);

    expect($results)
        ->toHaveCount(1)
        ->and($results[0]['provider'])->toBe('orders')
        ->and($results[0]['title'])->toBe('Order ABC')
        ->and($results[0]['url'])->toBe('/orders/1');
});

it('normalizes localized array values returned by search providers', function (): void {
    app()->setLocale('uk');
    config()->set('app.fallback_locale', 'en');

    $registry = new SearchRegistry;

    $registry->register(
        id: 'localized',
        provider: fn (string $query, int $limit): array => [[
            'title' => ['uk' => 'Сторінка', 'en' => 'Page'],
            'url' => '/pages/1',
            'description' => ['uk' => 'Опис', 'en' => 'Description'],
            'type' => ['uk' => 'Сторінка', 'en' => 'Page'],
        ]],
    );

    $results = $registry->search('page', null);

    expect($results)
        ->toHaveCount(1)
        ->and($results[0]['title'])->toBe('Сторінка')
        ->and($results[0]['description'])->toBe('Опис')
        ->and($results[0]['type'])->toBe('Сторінка');
});

it('skips malformed search results instead of casting arrays blindly', function (): void {
    $registry = new SearchRegistry;

    $registry->register(
        id: 'malformed',
        provider: fn (string $query, int $limit): array => [[
            'title' => ['nested' => ['value']],
            'url' => '/pages/1',
        ]],
    );

    expect($registry->search('page', null))->toBe([]);
});

it('allows modules to register settings sections', function (): void {
    $registry = new SettingsRegistry;

    $registry->registerSection(
        id: 'shop',
        label: 'Shop',
        component: 'lazy-shop.settings',
        description: 'Shop settings',
    );

    $section = collect($registry->sectionsFor(null))->firstWhere('id', 'shop');

    expect($section)
        ->not->toBeNull()
        ->and($section['component'])->toBe('lazy-shop.settings')
        ->and($section['label'])->toBe('Shop');
});

it('keeps notifications disabled until the standard notifications table exists', function (): void {
    expect(app(NotificationCenter::class)->availableFor(null))->toBeFalse();
});

it('writes old and new values through Spatie activity log', function (): void {
    $packageRoot = dirname((new ReflectionClass(Activity::class))->getFileName(), 3);
    $migration = require $packageRoot.'/database/migrations/create_activity_log_table.php.stub';
    $migration->up();

    AdminActivity::log(
        event: 'updated',
        description: 'Settings changed',
        old: ['name' => 'Before'],
        new: ['name' => 'After'],
    );

    $activity = Activity::query()->firstOrFail();

    expect($activity->log_name)->toBe('lazy-admin')
        ->and($activity->event)->toBe('updated')
        ->and($activity->description)->toBe('Settings changed')
        ->and($activity->properties->get('old'))->toBe(['name' => 'Before'])
        ->and($activity->properties->get('new'))->toBe(['name' => 'After']);
});

it('keeps the dashboard available without a dedicated permission', function (): void {
    config()->set('lazy.admin.permissions.enforce', true);

    $registry = new DashboardRegistry;
    $this->app->instance(DashboardRegistry::class, $registry);

    Livewire::test(Page::class)
        ->assertOk();
});
