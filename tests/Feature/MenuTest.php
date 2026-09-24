<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Step2dev\LazyMenu\Facades\Menu as MenuFacade;
use Step2dev\LazyMenu\Navigation\Menu\Menu;
use Step2dev\LazyMenu\Navigation\Menu\MenuManager;
use Step2dev\LazyMenu\Navigation\Menu\MenuRegistry;

it('builds the step2dev menu lazily once and filters permissions', function (): void {
    Route::get('/admin', fn () => 'dashboard')->name('admin');
    Route::get('/admin/posts', fn () => 'posts')->name('admin.posts');
    Route::get('/admin/categories/{category}', fn () => 'category')->name('admin.category.show');
    Route::getRoutes()->refreshNameLookups();
    view()->addNamespace('menu-test', __DIR__.'/../Fixtures/views');

    $menu = new MenuManager(new Menu);
    $calls = 0;
    $badgeCalls = 0;

    $menu->buildUsing(function (MenuManager $menu) use (&$calls, &$badgeCalls): void {
        $calls++;

        $menu->addItem('admin.posts', 'Posts', iconView: 'menu-test::icons.dashboard');
        $menu->addItem('admin.category.show', 'Category', parameters: ['category' => 7], badge: function () use (&$badgeCalls): int {
            $badgeCalls++;

            return 5;
        });
        $menu->addItem('admin.posts', 'Restricted', permission: 'edit posts', badge: function () use (&$badgeCalls): int {
            $badgeCalls++;

            return 99;
        });
    });

    $menu->addItem('admin', 'Dashboard', 'home', [
        $menu->createMenu('admin.posts', 'Nested')->iconView('menu-test::icons.dashboard'),
    ], iconView: 'menu-test::icons.dashboard');

    expect($calls)->toBe(0)
        ->and($menu->visibleItems())->toHaveCount(3)
        ->and($calls)->toBe(1)
        ->and($badgeCalls)->toBe(1);

    $html = $menu->render();

    expect($calls)->toBe(1);
    expect($badgeCalls)->toBe(1);
    expect($html)->toContain('href="http://localhost/admin"')
        ->toContain('href="http://localhost/admin/posts"')
        ->toContain('href="http://localhost/admin/categories/7"')
        ->toContain('Dashboard', 'Nested', 'Posts', 'Category', '>5</span>', 'x-data="{ open:', '<svg viewBox=')
        ->not->toContain('Restricted', '>99</span>');
    expect(substr_count($html, '<svg viewBox='))->toBe(3);
});

it('composes module menus and reuses cached category and unread counts', function (): void {
    Schema::create('menu_categories', function (Blueprint $table): void {
        $table->id();
        $table->string('title');
    });
    Schema::create('menu_contacts', function (Blueprint $table): void {
        $table->id();
        $table->string('status');
    });
    DB::table('menu_categories')->insert([
        ['id' => 7, 'title' => 'News'],
        ['id' => 8, 'title' => 'Other'],
    ]);
    DB::table('menu_contacts')->insert(['status' => 'unread']);

    Route::get('/uk/admin/categories/{category}', fn () => 'category')->name('admin.category.show');
    Route::get('/uk/admin/contacts', fn () => 'contacts')->name('admin.contact.index');
    Route::getRoutes()->refreshNameLookups();

    $store = Cache::store('array');
    $store->flush();
    $calls = 0;

    $registry = new MenuRegistry;

    $registry->register(function (MenuManager $menu) use ($store, &$calls): void {
        $calls++;
        $menu->push(['group' => 'Blog']);

        $categories = $store->remember('categories:uk', 300, fn () => DB::table('menu_categories')->get());

        foreach ($categories as $category) {
            $menu->addItem('admin.category.show', $category->title, parameters: ['category' => $category->id]);
        }
    });

    $registry->register(function (MenuManager $menu) use ($store, &$calls): void {
        $calls++;
        $menu->push(['group' => 'Requests']);
        $menu->addItem('admin.contact.index', 'Contacts', badge: fn () => $store->remember(
            'unread-contacts',
            15,
            fn () => DB::table('menu_contacts')->where('status', 'unread')->count()
        ));
    });

    $makeMenu = fn (): MenuManager => new MenuManager(new Menu, [], $registry);

    $this->get('/uk/admin/categories/7')->assertOk();

    DB::enableQueryLog();
    DB::flushQueryLog();

    $firstMenu = $makeMenu();
    $first = $firstMenu->render();
    $active = $firstMenu->visibleItems();
    $firstQueries = count(DB::getQueryLog());

    DB::flushQueryLog();
    $second = $makeMenu()->render();

    expect($calls)->toBe(4)
        ->and($firstQueries)->toBe(2)
        ->and($active[1]['active'])->toBeTrue()
        ->and($active[2]['active'])->toBeFalse()
        ->and(DB::getQueryLog())->toHaveCount(0)
        ->and($first)->toContain('href="http://localhost/uk/admin/categories/7"', 'News', 'Requests')
        ->and($first)->toMatch('/<span[^>]*rounded-full bg-cyan-500[^>]*>\s*1\s*<\/span>/')
        ->and($second)->toContain('News')
        ->and($second)->toMatch('/<span[^>]*rounded-full bg-cyan-500[^>]*>\s*1\s*<\/span>/');

    DB::table('menu_contacts')->insert(['status' => 'unread']);
    $store->forget('unread-contacts');
    DB::flushQueryLog();

    $updated = $makeMenu()->render();

    expect(DB::getQueryLog())->toHaveCount(1)
        ->and($updated)->toMatch('/<span[^>]*rounded-full bg-cyan-500[^>]*>\s*2\s*<\/span>/');
});

it('keeps module contributors after request scoped services reset', function (): void {
    Route::get('/admin/blog', fn () => 'blog')->name('admin.blog.index');
    Route::get('/admin/pages', fn () => 'pages')->name('admin.pages.index');
    Route::getRoutes()->refreshNameLookups();

    $blogCalls = 0;
    $pageCalls = 0;

    MenuFacade::order('pages', before: 'blog');

    // Equivalent to two independently discovered package service providers.
    MenuFacade::register(function (MenuManager $menu) use (&$blogCalls): void {
        $blogCalls++;
        $menu->addItem('admin.blog.index', 'Blog');
    }, id: 'blog');
    MenuFacade::register(function (MenuManager $menu) use (&$pageCalls): void {
        $pageCalls++;
        $menu->addItem('admin.pages.index', 'Pages');
    }, id: 'pages');

    $first = app(MenuManager::class);
    expect($first->visibleItems())->toHaveCount(2)
        ->and(array_column($first->visibleItems(), 'label'))->toBe(['Pages', 'Blog'])
        ->and($blogCalls)->toBe(1)
        ->and($pageCalls)->toBe(1);

    app()->forgetScopedInstances();

    $second = app(MenuManager::class);
    expect($second)->not->toBe($first)
        ->and(array_column($second->visibleItems(), 'label'))->toBe(['Pages', 'Blog'])
        ->and($blogCalls)->toBe(2)
        ->and($pageCalls)->toBe(2);

    expect(MenuFacade::getFacadeRoot())->toBe($second);
});

it('orders package menu blocks and lets the host swap their positions', function (): void {
    $registry = new MenuRegistry;
    $labels = function () use ($registry): array {
        $menu = new MenuManager(new Menu, [], $registry);

        return array_column($menu->visibleItems(), 'label');
    };

    $registry->register(fn (MenuManager $menu) => $menu->addItem('/dashboard', 'Dashboard'), id: 'dashboard', priority: -10);
    $registry->register(fn (MenuManager $menu) => $menu->addItem('/blog', 'Blog'), id: 'blog');
    $registry->register(fn (MenuManager $menu) => $menu->addItem('/pages', 'Pages'), id: 'pages');
    $registry->register(fn (MenuManager $menu) => $menu->addItem('/contact', 'Contact'), id: 'contact', after: 'pages');
    $registry->register(fn (MenuManager $menu) => $menu->addItem('/other', 'Other'));

    expect($labels())->toBe(['Dashboard', 'Blog', 'Pages', 'Contact', 'Other']);

    // The application can change installed package order without editing its provider.
    $registry->order('pages', before: 'blog');

    expect($labels())->toBe(['Dashboard', 'Pages', 'Blog', 'Contact', 'Other']);
    expect($labels())->toBe(['Dashboard', 'Pages', 'Blog', 'Contact', 'Other']);
});

it('accepts order overrides before an optional module registers', function (): void {
    $registry = new MenuRegistry;
    $registry->order('pages', before: 'blog');
    $registry->register(fn (MenuManager $menu) => $menu->addItem('/blog', 'Blog'), id: 'blog');
    $registry->register(fn (MenuManager $menu) => $menu->addItem('/pages', 'Pages'), id: 'pages');
    $registry->register(fn (MenuManager $menu) => $menu->addItem('/other', 'Other'), id: 'other', after: 'uninstalled-module');

    $menu = new MenuManager(new Menu, [], $registry);

    expect(array_column($menu->visibleItems(), 'label'))->toBe(['Pages', 'Blog', 'Other']);
});

it('rejects cyclic menu positioning', function (): void {
    $registry = new MenuRegistry;
    $registry->register(fn (MenuManager $menu) => $menu->addItem('/blog', 'Blog'), id: 'blog', after: 'pages');
    $registry->register(fn (MenuManager $menu) => $menu->addItem('/pages', 'Pages'), id: 'pages', after: 'blog');

    expect(fn () => $registry->contributors())->toThrow(LogicException::class);
});

it('uses the lazy-menu facade directly', function (): void {
    $menu = app(MenuManager::class);

    expect(MenuFacade::getFacadeRoot())->toBe($menu);

    MenuFacade::register(function (MenuManager $menu): void {
        $menu->addItem('/module', 'Installed module');
    }, id: 'installed-module');

    expect(MenuFacade::render())->toContain('Installed module');
});

it('renders the admin theme by default and keeps the package template selectable', function (): void {
    MenuFacade::addItem('/admin', 'Dashboard', badge: 'New');

    $admin = MenuFacade::render();
    $standalone = MenuFacade::render('lazy-menu::menu-generator');

    expect($admin)
        ->toContain('menu bg-base-200 w-full overflow-visible transition-all duration-200 lg:menu-normal')
        ->toContain("sidebarCompact ? 'menu-compact' : ''")
        ->toContain('badge badge-accent')
        ->not->toContain('bg-slate-900');
    expect($standalone)->toContain('bg-slate-900', 'rounded-full bg-cyan-500')
        ->not->toContain('bg-base-200');
});

it('keeps the original menu item fluent API available', function (): void {
    $item = Menu::make('/admin', 'Dashboard')
        ->iconView('icons.dashboard')
        ->parameters(['locale' => 'uk'])
        ->badge('New')
        ->children(fn (Menu $children) => $children->group('Blog'));

    expect($item)->toBeInstanceOf(Menu::class)
        ->and($item->toArray()[0]['label'])->toBe('Dashboard')
        ->and($item->toArray()[0]['badge'])->toBe('New')
        ->and($item->toArray()[0]['submenu'])->toBeInstanceOf(Menu::class);
});
