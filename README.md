# Lazy Admin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/step2dev/lazy-admin.svg?style=flat-square)](https://packagist.org/packages/step2dev/lazy-admin)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/step2dev/lazy-admin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/step2dev/lazy-admin/actions/workflows/run-tests.yml?query=branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/step2dev/lazy-admin/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/step2dev/lazy-admin/actions/workflows/fix-php-code-style-issues.yml?query=branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/step2dev/lazy-admin.svg?style=flat-square)](https://packagist.org/packages/step2dev/lazy-admin)

Laravel admin layout and configurable route group, with navigation provided by [lazy-menu](https://github.com/step2dev/lazy-menu). Requires PHP 8.2+ and Laravel 10–13.

## Installation

```bash
composer require step2dev/lazy-admin
php artisan lazy-admin:install
```

The installer publishes configuration and registers the application's `LazyAdminServiceProvider`. Existing applications can keep their published provider and configuration.

## Menu dependency during Git development

The navigation engine lives in `step2dev/lazy-menu`; lazy-admin keeps its own DaisyUI menu templates. Until a tagged release is available, this branch installs the public package from Git through its VCS repository and the `dev-main` constraint. Composer and CI do not need a separate token for lazy-menu.

Lazy Admin uses the `step2dev/lazy-menu` classes directly. Use `Step2dev\\LazyMenu\\Facades\\Menu`, `Step2dev\\LazyMenu\\Navigation\\Menu\\MenuManager`, `Menu`, and `MenuRegistry`; the former Lazy Admin menu wrappers are removed.

The admin layout renders its own DaisyUI templates under `lazy::menu-generator`, `lazy::menu-item` and `lazy::menu-label`. Override the corresponding files in `resources/views/vendor/lazy/`, or select your own view in a provider with `Menu::useView('admin.navigation.menu')`. For a single render, pass a view to `Menu::render('admin.navigation.menu')`. The standalone lazy-menu package uses a Tailwind template. No menu config needs publishing. See the [lazy-menu documentation](https://github.com/step2dev/lazy-menu) for template variables.

## Routes

Protected routes live in `lazy.admin.route.path` (default `routes/admin.php`); the package loads the file inside `Route::admin()`.

```php
Route::get('/', DashboardController::class)->name('dashboard');
Route::resource('posts', PostController::class);
```

Default names include `admin.dashboard` and `admin.posts.index`; the prefix defaults to `/admin`. Explicit attributes passed to `Route::admin($callback, $attributes)` override configuration. Localized prefixes use the configured localization manager. The guest login route uses `lazy.admin.route.login.uri` and `lazy.admin.route.login.prefix`, with `web` and `guest` middleware. The host application supplies POST authentication.

## Automatic module registration

A separately installed package such as `lazy-blog` or `lazy-pages` can add its own routes and navigation without editing the host application's menu. The module must depend on `step2dev/lazy-admin` and publish a Laravel provider in its own `composer.json`:

```json
{
    "require": {
        "step2dev/lazy-admin": "dev-main"
    },
    "extra": {
        "laravel": {
            "providers": [
                "Step2dev\\LazyBlog\\LazyBlogServiceProvider"
            ]
        }
    }
}
```

Use a tagged version when one is available. For the current step2dev application, the dependency is `dev-main`. Register the routes with their admin prefix and the menu contribution in that provider:

```php
namespace Step2dev\LazyBlog;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Step2dev\LazyMenu\Facades\Menu;
use Step2dev\LazyMenu\Navigation\Menu\MenuManager;

class LazyBlogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->routesAreCached()) {
            Route::admin(function (): void {
                Route::get('blog', BlogController::class)->name('blog.index');
            });
        }

        Menu::register(function (MenuManager $menu): void {
            $menu->push(['group' => 'Blog']);
            $menu->addItem('admin.blog.index', 'Blog');
        }, id: 'blog', priority: 20);
    }
}
```

When a module ships a separate route file, load it inside the same `routesAreCached()` guard. Each installed module contributes its own callback. Laravel package discovery loads providers; the menu registry keeps callbacks across requests, and the menu manager invokes them only when rendered. If package discovery is disabled in the host application, register its provider explicitly.

## Dynamic navigation

Each application or package module may call `Menu::register(...)` in its service provider. The optional `group` argument adds a translated section heading without a manual `push`; omitting an item label translates its route name (for example, `admin.blog.index`). Missing `before`/`after` modules are ignored until installed. Contributors run in registration order, only when the menu is rendered, and once per request. The older `buildUsing(...)` remains available for a single application builder:

```php
use App\Models\BlogCategory;
use App\Models\Contact;
use Illuminate\Support\Facades\Cache;
use Step2dev\LazyMenu\Facades\Menu;
use Step2dev\LazyMenu\Navigation\Menu\MenuManager;

Menu::register(function (MenuManager $menu): void {
    $menu->push(['group' => 'Blog']);

    $menu->addItem('admin.dashboard', 'Dashboard', iconView: 'icons.dashboard');

    $categories = Cache::remember(
        'admin-menu-categories:'.app()->getLocale(),
        300,
        fn () => BlogCategory::query()->select('id')->with('translations')->get()
    );

    foreach ($categories as $category) {
        $menu->addItem(
            'admin.category.show',
            $category->title,
            permission: 'blog_show',
            parameters: ['category' => $category->id],
        );
    }
});

// Another module's service provider:
Menu::register(function (MenuManager $menu): void {
    $menu->push(['group' => 'Requests']);
    $menu->addItem(
        'admin.contact.index',
        'Requests',
        iconView: 'icons.phone',
        badge: fn () => Cache::remember(
            'admin-menu-unread-contacts',
            15,
            fn () => Contact::query()->where('status', '!=', Contact::STATUS_READ)->count()
        ),
    );
});
```

### Menu block order

Give each module an ID when registering its menu. Lower `priority` appears first; equal priorities keep registration order. Use `before` or `after` for an explicit dependency on another module ID:

```php
Menu::register(
    fn (MenuManager $menu) => $menu->addItem('admin.pages.index', 'Pages'),
    id: 'pages',
    before: 'blog',
);
```

The host application can swap installed modules in its own provider's `boot()`, without editing the packages:

```php
Menu::order('blog', before: 'pages');
// Or move the entire blog block toward the top:
Menu::order('blog', priority: -10);
```

Call `Menu::order()` during provider boot, before the menu renders. An order override can be registered before its module provider boots. Missing optional module IDs are ignored; circular `before`/`after` dependencies raise an exception. Positioning applies to the full callback block, including its group label and dynamically generated items. Individual items inside that block stay in the order added by its callback.

Cache shared source data and expensive counters independently; the package filters each item by the current user's `can()` permission before evaluating a badge callback. Badge callbacks run at most once per request. A Blog module and a Requests module may register separate callbacks without replacing each other. Include locale (and any other data dependency) in the cache key, and invalidate when categories change (for example, `Cache::forget('admin-menu-categories:'.app()->getLocale())` from the category write path). Invalidate `admin-menu-unread-contacts` when a request arrives or is marked read; the short TTL is a fallback. No query runs for this builder if the layout menu is not rendered.

For nested items:

```php
$menu->addItem('admin.blog.index', 'Articles', children: [
    Menu::createMenu('admin.blog.create', 'Create')->iconView('icons.plus'),
]);
```

`iconView` names an application Blade view such as `resources/views/icons/dashboard.blade.php` containing SVG. `icon` still accepts a CSS class; `badge` accepts static text or a lazy callback. Database icon identifiers must be mapped to trusted Blade views before being passed to `iconView`. Route names resolve to URLs; use `parameters` (or `->parameters([...])` for a child) for required route parameters. Literal URLs are also accepted. Parent sections expand when a descendant route is active. Links and badges are escaped.

Existing calls such as `Menu::addItem('admin', 'Dashboard', 'home', [Menu::createMenu('admin2', 'Child')])` remain valid. The `<x-lazy::layout>` component renders the menu and accepts `title`, `menu`, `header`, `footer` and `action` slots. Views under the `lazy::` namespace can be overridden in the application.

## Access

Protected routes use `lazy.admin.route.middleware` (default `web`, `auth`, `verified` and `LazyAdminMiddleware`). Replace the last middleware in the published configuration with an application-specific access check if needed. The default middleware checks `lazy.admin.roles`.

## Tests

```bash
composer test
composer analyse
```
