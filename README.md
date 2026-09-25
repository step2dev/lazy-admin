# Lazy Admin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/step2dev/lazy-admin.svg?style=flat-square)](https://packagist.org/packages/step2dev/lazy-admin)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/step2dev/lazy-admin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/step2dev/lazy-admin/actions/workflows/run-tests.yml?query=branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/step2dev/lazy-admin/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/step2dev/lazy-admin/actions/workflows/fix-php-code-style-issues.yml?query=branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/step2dev/lazy-admin.svg?style=flat-square)](https://packagist.org/packages/step2dev/lazy-admin)

Laravel admin layout and configurable route group, with navigation provided by [lazy-menu](https://github.com/step2dev/lazy-menu). Requires PHP 8.4+ and Laravel 11–13. Page management is powered by [lazy-page](https://github.com/step2dev/lazy-page).

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

## Page management

Lazy Admin includes the administration UI for `step2dev/lazy-page`. The dependency direction is intentional: Lazy Admin consumes Lazy Page; Lazy Page does not depend on the admin package.

The Pages module provides:

- multilingual title, description and content editing
- locale tabs using the configured localization manager, with Laravel locale fallback
- draft, published, scheduled and archived states
- publication and expiration timestamps
- stable keys, slugs and page templates
- parent/child hierarchy
- admin preview
- soft delete and restore
- permission-aware navigation and actions

Default page permissions are `pages.view`, `pages.create`, `pages.edit`, `pages.delete`, `pages.restore`, `pages.publish` and `pages.preview`.

## Routes

Protected routes live in `lazy.admin.route.path` (default `routes/admin.php`); the package loads the file inside `Route::admin()`.

```php
Route::get('/', DashboardController::class)->name('dashboard');
Route::resource('posts', PostController::class);
```

Default names include `admin.dashboard` and `admin.posts.index`; the prefix defaults to `/admin`. Explicit attributes passed to `Route::admin($callback, $attributes)` override configuration. Localized prefixes use the configured localization manager. Lazy Admin uses the host application's Laravel authentication by default. Set `LAZY_AUTH_LOGIN_ENABLED=true` only when the package should expose its own login/logout routes.

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

Existing calls such as `Menu::addItem('admin', 'Dashboard', 'home', [Menu::createMenu('admin2', 'Child')])` remain valid. The `<x-lazy-layout>` component renders the menu and accepts `title`, `menu`, `header`, `footer` and `action` slots. Views under the `lazy::` namespace can be overridden in the application.

## Optional SEO redirects

Lazy Admin automatically exposes an **SEO → Redirects** workspace when `step2dev/lazy-seo-redirects` is installed. The redirect package remains optional and owns all redirect matching, normalization, caching and middleware behavior; Lazy Admin only provides the management UI.

```bash
composer require step2dev/lazy-seo-redirects
php artisan vendor:publish --tag=lazy-seo-redirects-config
php artisan vendor:publish --tag=lazy-seo-redirects-migrations
php artisan migrate
```

The integration registers `/admin/seo/redirects` and adds `seo_redirects.view`, `seo_redirects.create`, `seo_redirects.edit` and `seo_redirects.delete` to Lazy Admin's authorization defaults only while the package is available. Disable the admin integration without disabling redirect handling itself with:

```env
LAZY_ADMIN_SEO_REDIRECTS_ENABLED=false
```

## Authentication

Lazy Admin is built on Laravel authentication and does not own the application's user model. By default it uses the `web` guard and resolves the user provider/model from the host application's `config/auth.php`.

```env
LAZY_AUTH_GUARD=web
LAZY_AUTH_PROVIDER=users
LAZY_AUTH_LOGIN_ENABLED=false
LAZY_AUTH_REDIRECT_ROUTE=admin.dashboard
```

The package login screen is opt-in. When enabled, Lazy Admin registers guarded login, login submit and logout routes with session regeneration/invalidation. Applications that already use Fortify, a Laravel starter kit, Jetstream or custom authentication should keep `LAZY_AUTH_LOGIN_ENABLED=false`.

`spatie/laravel-permission` remains the admin authorization layer. Fortify, Socialite and Sanctum are optional host-application integrations and are not required by Lazy Admin.

## Access

Protected routes use `lazy.admin.route.middleware` (default `web`, `auth`, `verified` and `LazyAdminMiddleware`). Replace the last middleware in the published configuration with an application-specific access check if needed. The default middleware checks `lazy.admin.roles`.

## Tests

```bash
composer test
composer analyse
```


## Roles and permissions

Lazy Admin uses `spatie/laravel-permission` as its authorization layer.

The installer publishes Spatie's official permission config and migration before asking to run migrations:

```bash
php artisan lazy-admin:install
```

For an existing application, publish and migrate once:

```bash
php artisan vendor:publish --tag=permission-config
php artisan vendor:publish --tag=permission-migrations
php artisan migrate
```

The host application's authenticatable model must use either the Lazy Admin wrapper trait:

```php
use Step2dev\LazyAdmin\Authorization\HasLazyAdminPermissions;

class User extends Authenticatable
{
    use HasLazyAdminPermissions;
}
```

or Spatie's `HasRoles` trait directly.

Lazy Admin seeds these roles by default:

- `superadmin`
- `admin`
- `manager`
- `moderator`

The default permissions are:

- `users.view`, `users.create`, `users.edit`, `users.delete`
- `roles.view`, `roles.create`, `roles.edit`, `roles.delete`
- `permissions.view`, `permissions.create`, `permissions.edit`, `permissions.delete`

The role-to-permission mapping is configurable in `config/lazy/admin.php`. The `superadmin` role is granted every Gate ability through `Gate::before()`.

Roles and permissions are managed together in one access-management screen:

```text
/admin/access
```

The left side manages roles and their permission matrix. The permissions section manages reusable permission names. Role and permission write endpoints remain separate internally, while the UI and navigation use a single Access entry point.

The users CRUD can assign roles and validates every selected role against the configured Spatie guard.

Environment controls:

```env
LAZY_ADMIN_ENFORCE_PERMISSIONS=true
LAZY_ADMIN_PERMISSION_GUARD=web
LAZY_ADMIN_SUPER_ADMIN_ROLE=superadmin
LAZY_ADMIN_SEED_PERMISSIONS=true
```

To create a first super administrator:

```bash
php artisan make:admin
```

The command resolves the configured authentication user model, requires `HasLazyAdminPermissions` / `HasRoles`, seeds the default authorization data idempotently, and assigns the configured super-admin role.
