<?php

namespace Step2dev\LazyAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;
use Step2dev\LazyAdmin\Commands\CreateAdminCommand;
use Step2dev\LazyAdmin\Commands\DbOptimize;
use Step2dev\LazyAdmin\Commands\LazyAdminCommand;
use Step2dev\LazyAdmin\Components\BaseLayout;
use Step2dev\LazyAdmin\Components\Card;
use Step2dev\LazyAdmin\Components\Dropdown;
use Step2dev\LazyAdmin\Components\EmptyState;
use Step2dev\LazyAdmin\Components\Footer;
use Step2dev\LazyAdmin\Components\Header;
use Step2dev\LazyAdmin\Components\LanguageSwitcher;
use Step2dev\LazyAdmin\Components\Layout;
use Step2dev\LazyAdmin\Components\Table as AdminTable;
use Step2dev\LazyAdmin\Controllers\AccessController;
use Step2dev\LazyAdmin\Controllers\PageController;
use Step2dev\LazyAdmin\Controllers\PermissionController;
use Step2dev\LazyAdmin\Controllers\RoleController;
use Step2dev\LazyAdmin\Controllers\SeoRedirectController;
use Step2dev\LazyAdmin\Dashboard\DashboardRegistry;
use Step2dev\LazyAdmin\Database\Seeders\DatabaseSeeder;
use Step2dev\LazyAdmin\Facades\Route as RouteFacade;
use Step2dev\LazyAdmin\Http\Livewire\Activity\Page as ActivityPage;
use Step2dev\LazyAdmin\Http\Livewire\Dashboard\Page as DashboardPage;
use Step2dev\LazyAdmin\Http\Livewire\Notifications\Bell as NotificationBell;
use Step2dev\LazyAdmin\Http\Livewire\Notifications\Page as NotificationsPage;
use Step2dev\LazyAdmin\Http\Livewire\Search\HeaderSearch;
use Step2dev\LazyAdmin\Http\Livewire\Search\Page as SearchPage;
use Step2dev\LazyAdmin\Http\Livewire\Settings\Page as SettingsPage;
use Step2dev\LazyAdmin\Http\Livewire\Settings\Setting;
use Step2dev\LazyAdmin\Http\Livewire\Users\Table;
use Step2dev\LazyAdmin\Integrations\SeoRedirectsIntegration;
use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;
use Step2dev\LazyAdmin\Localization\LocalizationManager;
use Step2dev\LazyAdmin\Middleware\LazyAdminMiddleware;
use Step2dev\LazyAdmin\Notifications\NotificationCenter;
use Step2dev\LazyAdmin\Routing\Router as AdminRouter;
use Step2dev\LazyAdmin\Search\SearchRegistry;
use Step2dev\LazyAdmin\Settings\SettingsRegistry;
use Step2Dev\LazyBreadcrumb\LazyBreadcrumbServiceProvider;
use Step2dev\LazyMenu\Facades\Menu as MenuFacade;
use Step2dev\LazyMenu\LazyMenuServiceProvider;
use Step2dev\LazyMenu\Navigation\Menu\Menu;
use Step2dev\LazyMenu\Navigation\Menu\MenuManager;
use Step2dev\LazyMenu\Navigation\Menu\MenuRegistry;
use Step2dev\LazyPage\Enums\PageStatus;
use Step2dev\LazyPage\Models\Page;
use Step2dev\LazyPage\Models\PageTranslation;

class LazyAdminServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('lazy-admin')
            ->hasConfigFile([
                'lazy/admin',
                'lazy/auth',
                'lazy/localization',
                'lazy/socialite',
            ])
            ->hasViews('lazy')
            ->hasTranslations()
            ->hasMigrations([
                'create_settings_table',
            ])
            ->runsMigrations()
            // ->publishesServiceProvider('LazyAsideServiceProvider')
            ->publishesServiceProvider('LazyAdminServiceProvider')
            //            ->hasAssets()
            ->hasInstallCommand(static function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->startWith(static function (InstallCommand $installCommand) {
                        $installCommand->info('Installing Lazy Admin...');
                        $installCommand->call('lazy-ui:install');
                        $installCommand->call('vendor:publish', [
                            '--tag' => 'permission-config',
                        ]);
                        $installCommand->call('vendor:publish', [
                            '--tag' => 'permission-migrations',
                        ]);
                        $installCommand->call('vendor:publish', [
                            '--provider' => 'Spatie\\Activitylog\\ActivitylogServiceProvider',
                            '--tag' => 'activitylog-migrations',
                        ]);
                        $installCommand->call('vendor:publish', [
                            '--provider' => 'Spatie\\Activitylog\\ActivitylogServiceProvider',
                            '--tag' => 'activitylog-config',
                        ]);
                    })
                    ->publish('lazy-admin', 'lazy', 'lazy-setting')
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
//                    ->publishAssets()
                    ->askToStarRepoOnGitHub('step2dev/lazy-admin')
                    ->endWith(static function (InstallCommand $installCommand) {
                        $installCommand->call('make:admin');
                        $installCommand->call('db:seed', [
                            '--class' => DatabaseSeeder::class,
                        ]);
                        $installCommand->call('config:clear');
                        $installCommand->info('Lazy Admin installed successfully. Enjoy!');
                    });
            })
            ->hasViewComponents('lazy',
                Footer::class,
                Header::class,
                Layout::class,
                BaseLayout::class,
                Card::class,
                Dropdown::class,
                EmptyState::class,
                AdminTable::class,
                LanguageSwitcher::class
            )
            ->sharesDataWithAllViews('companyName', 'Step2Dev')
            ->sharesDataWithAllViews('companyUrl', 'https://step2.dev')
            ->sharesDataWithAllViews('githubUrl', 'https://github.com/step2dev/lazy-admin')
            ->hasCommands([
                LazyAdminCommand::class,
                DbOptimize::class,
                CreateAdminCommand::class,
            ]);
    }

    public function registeringPackage(): void
    {
        if ($this->app->runningInConsole() && $this->app->environment('testing')) {
            $this->app->register(LivewireServiceProvider::class);
        }

        $this->app->singleton(AuthorizationManager::class);
        $this->app->singleton(SettingsRegistry::class);
        $this->app->singleton(DashboardRegistry::class);
        $this->app->singleton(SearchRegistry::class);
        $this->app->singleton(NotificationCenter::class);

        if (SeoRedirectsIntegration::available()) {
            $this->registerSeoRedirectPermissions();
        }

        $this->app->register(LazyMenuServiceProvider::class);
        $this->app->register(LazyBreadcrumbServiceProvider::class);
        $this->app->singleton(MenuRegistry::class, static function (): MenuRegistry {
            $registry = new MenuRegistry;
            $registry->useView('lazy::menu-generator');

            return $registry;
        });
        $this->app->scoped(MenuManager::class, fn () => new MenuManager(new Menu));

        $this->app->alias('setting', 'settings');
        $this->app->bind(LocalizationInterface::class, config('lazy.localization.localizationManager', LocalizationManager::class));
    }

    public function packageRegistered(): void
    {
        Livewire::addPersistentMiddleware([LazyAdminMiddleware::class]);

        Livewire::component('settings.setting', Setting::class);
        Livewire::component('lazy-admin.settings.page', SettingsPage::class);
        Livewire::component('lazy-admin.dashboard.page', DashboardPage::class);
        Livewire::component('lazy-admin.search.page', SearchPage::class);
        Livewire::component('lazy-admin.header-search', HeaderSearch::class);
        Livewire::component('lazy-admin.notification-bell', NotificationBell::class);
        Livewire::component('lazy-admin.notifications.page', NotificationsPage::class);
        Livewire::component('lazy-admin.activity.page', ActivityPage::class);
        Livewire::component('lazy-admin.users.table', Table::class);
    }

    public function bootingPackage(): void
    {
        Gate::before(static function ($user, string $ability): ?bool {
            $superAdminRole = (string) config('lazy.admin.permissions.super_admin_role', 'superadmin');

            return method_exists($user, 'hasRole') && $user->hasRole($superAdminRole)
                ? true
                : null;
        });
    }

    public function packageBooted(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->mixin(new AdminRouter);

        RouteFacade::admin(function (): void {
            RouteFacade::get('', DashboardPage::class)->name('dashboard');
            RouteFacade::get('search', SearchPage::class)->name('search');
            RouteFacade::get('notifications', NotificationsPage::class)->name('notifications.index');
            RouteFacade::get('activity', ActivityPage::class)->name('activity.index');
            RouteFacade::get('access', AccessController::class)->name('access.index');
            RouteFacade::resource('role', RoleController::class)->only(['store', 'update', 'destroy']);
            RouteFacade::resource('permission', PermissionController::class)->only(['store', 'update', 'destroy']);
            RouteFacade::resource('page', PageController::class)->except(['show']);
            RouteFacade::get('page/{page}/preview', [PageController::class, 'preview'])->name('page.preview');
            RouteFacade::post('page/{page}/restore', [PageController::class, 'restore'])->name('page.restore');

            if (SeoRedirectsIntegration::available()) {
                RouteFacade::prefix('seo')->name('seo.')->group(function (): void {
                    RouteFacade::get('redirects', [SeoRedirectController::class, 'index'])->name('redirects.index');
                    RouteFacade::post('redirects', [SeoRedirectController::class, 'store'])->name('redirects.store');
                    RouteFacade::put('redirects/{redirect}', [SeoRedirectController::class, 'update'])->name('redirects.update');
                    RouteFacade::delete('redirects/{redirect}', [SeoRedirectController::class, 'destroy'])->name('redirects.destroy');
                });
            }
        });

        $prefix = trim((string) config('lazy.admin.route.name', 'admin.'), '.');

        app(SettingsRegistry::class)->registerSection(
            id: 'general',
            label: __('General'),
            component: 'settings.setting',
            permission: 'settings.view',
            priority: 10,
            description: __('Site name, description and logo.'),
        );

        app(DashboardRegistry::class)->registerWidget(
            id: 'pages',
            label: __('Pages'),
            value: static fn (): int => Page::query()->count(),
            description: __('Total CMS pages'),
            route: $prefix.'.page.index',
            permission: 'pages.view',
            priority: 10,
        );
        app(DashboardRegistry::class)->registerWidget(
            id: 'draft-pages',
            label: __('Draft pages'),
            value: static fn (): int => Page::query()->where('status', PageStatus::Draft->value)->count(),
            description: __('Pages waiting to be published'),
            route: $prefix.'.page.index',
            permission: 'pages.view',
            priority: 20,
        );
        app(DashboardRegistry::class)->registerWidget(
            id: 'users',
            label: __('Users'),
            value: static function (): int {
                $guard = (string) config('lazy.auth.guard', 'web');
                $provider = config('lazy.auth.provider') ?: config("auth.guards.{$guard}.provider", 'users');
                $model = config("auth.providers.{$provider}.model");

                return is_string($model) && is_subclass_of($model, Model::class)
                    ? $model::query()->count()
                    : 0;
            },
            description: __('Registered users'),
            route: $prefix.'.user.index',
            permission: 'users.view',
            priority: 30,
        );
        app(DashboardRegistry::class)->registerWidget(
            id: 'activity-today',
            label: __('Activity today'),
            value: static function (): int {
                $modelClass = config('activitylog.activity_model', Activity::class);

                if (! is_string($modelClass) || ! is_a($modelClass, Activity::class, true)) {
                    return 0;
                }

                $model = new $modelClass;

                return Schema::hasTable($model->getTable())
                    ? $modelClass::query()->whereDate('created_at', today())->count()
                    : 0;
            },
            description: __('Recorded admin actions today'),
            route: $prefix.'.activity.index',
            permission: 'activity.view',
            priority: 40,
        );

        app(SearchRegistry::class)->register(
            id: 'pages',
            provider: static function (string $query, int $limit) use ($prefix): array {
                $pages = Page::query()
                    ->with('translations')
                    ->where(function ($builder) use ($query): void {
                        $builder->where('slug', 'like', '%'.$query.'%')
                            ->orWhere('key', 'like', '%'.$query.'%')
                            ->orWhereHas('translations', fn ($translations) => $translations->where('title', 'like', '%'.$query.'%'));
                    })
                    ->limit($limit)
                    ->get();

                $results = [];

                foreach ($pages as $page) {
                    $translation = $page->translate(app()->getLocale())
                        ?? $page->translate((string) $page->getAttribute('original_locale'));

                    $results[] = [
                        'title' => $translation instanceof PageTranslation
                            ? ((string) $translation->getAttribute('title') ?: $page->slug)
                            : $page->slug,
                        'description' => $page->path(),
                        'url' => route($prefix.'.page.index', ['search' => $page->slug]),
                        'type' => __('lazy-admin::search.types.page'),
                    ];
                }

                return $results;
            },
            permission: 'pages.view',
            priority: 10,
        );

        app(SearchRegistry::class)->register(
            id: 'users',
            provider: static function (string $query, int $limit) use ($prefix): array {
                $guard = (string) config('lazy.auth.guard', 'web');
                $provider = config('lazy.auth.provider') ?: config("auth.guards.{$guard}.provider", 'users');
                $model = config("auth.providers.{$provider}.model");

                if (! is_string($model) || ! is_subclass_of($model, Model::class)) {
                    return [];
                }

                return $model::query()
                    ->where(fn ($builder) => $builder
                        ->where('name', 'like', '%'.$query.'%')
                        ->orWhere('email', 'like', '%'.$query.'%'))
                    ->limit($limit)
                    ->get()
                    ->map(fn ($user): array => [
                        'title' => (string) $user->getAttribute('name'),
                        'description' => (string) $user->getAttribute('email'),
                        'url' => route($prefix.'.user.show', $user->getKey()),
                        'type' => __('lazy-admin::search.types.user'),
                    ])
                    ->all();
            },
            permission: 'users.view',
            priority: 20,
        );

        app(SearchRegistry::class)->register(
            id: 'settings',
            provider: static function (string $query, int $limit) use ($prefix): array {
                $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

                return collect(app(SettingsRegistry::class)->sectionsFor($user))
                    ->filter(fn (array $section): bool => str_contains(mb_strtolower($section['label'].' '.($section['description'] ?? '')), mb_strtolower($query)))
                    ->take($limit)
                    ->map(fn (array $section): array => [
                        'title' => $section['label'],
                        'description' => $section['description'],
                        'url' => route($prefix.'.setting.index', ['section' => $section['id']]),
                        'type' => __('lazy-admin::search.types.setting'),
                    ])
                    ->values()
                    ->all();
            },
            permission: 'settings.view',
            priority: 30,
        );

        MenuFacade::register(function (MenuManager $menu): void {
            $prefix = trim((string) config('lazy.admin.route.name', 'admin.'), '.');

            $menu->group(__('Overview'));
            $menu->addItem($prefix.'.dashboard', __('Dashboard'));
        }, id: 'lazy-admin-dashboard', priority: 10);

        MenuFacade::register(function (MenuManager $menu): void {
            $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();
            $enforce = (bool) config('lazy.admin.permissions.enforce', true);
            $prefix = trim((string) config('lazy.admin.route.name', 'admin.'), '.');

            if (! $enforce || $user?->can('settings.view') || $user?->can('activity.view')) {
                $menu->group(__('System'));

                if (! $enforce || $user?->can('settings.view')) {
                    $menu->addItem($prefix.'.setting.index', __('Settings'));
                }

                if (! $enforce || $user?->can('activity.view')) {
                    $menu->addItem($prefix.'.activity.index', __('Activity log'));
                }
            }
        }, id: 'lazy-admin-system', priority: 90);

        if (SeoRedirectsIntegration::available()) {
            MenuFacade::register(function (MenuManager $menu): void {
                $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();
                $enforce = (bool) config('lazy.admin.permissions.enforce', true);

                if ($enforce && ! $user?->can('seo_redirects.view')) {
                    return;
                }

                $prefix = trim((string) config('lazy.admin.route.name', 'admin.'), '.');
                $routeName = $prefix.'.seo.redirects.index';
                $routeTarget = RouteFacade::has($routeName)
                    ? $routeName
                    : url('/'.trim((string) config('lazy.admin.route.prefix', 'admin'), '/').'/seo/redirects');

                $menu->group(__('SEO'));
                $menu->addItem(
                    $routeTarget,
                    __('Redirects'),
                );
            }, id: 'lazy-admin-seo-redirects', priority: 70);
        }

        MenuFacade::register(function (MenuManager $menu): void {
            $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();
            $enforce = (bool) config('lazy.admin.permissions.enforce', true);

            if (! $enforce || $user?->can('pages.view')) {
                $prefix = trim((string) config('lazy.admin.route.name', 'admin.'), '.');

                $menu->group(__('Content'));
                $menu->addItem(
                    $prefix.'.page.index',
                    __('Pages'),
                );
            }
        }, id: 'lazy-admin-pages', priority: 60);

        MenuFacade::register(function (MenuManager $menu): void {
            $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

            if (
                config('lazy.admin.permissions.enforce', true)
                && ! ($user?->can('roles.view') || $user?->can('permissions.view'))
            ) {
                return;
            }

            $prefix = trim((string) config('lazy.admin.route.name', 'admin.'), '.');

            $menu->group(__('Access'));
            $menu->addItem(
                $prefix.'.access.index',
                __('Access'),
            );
        }, id: 'lazy-admin-access', priority: 80);
    }

    private function registerSeoRedirectPermissions(): void
    {
        $permissions = SeoRedirectsIntegration::permissions();
        $defaults = array_values(array_unique([
            ...(array) config('lazy.admin.permissions.defaults', []),
            ...$permissions,
        ]));

        config()->set('lazy.admin.permissions.defaults', $defaults);

        $rolePermissions = (array) config('lazy.admin.permissions.role_permissions', []);
        $rolePermissions['admin'] = array_values(array_unique([
            ...(array) ($rolePermissions['admin'] ?? []),
            ...$permissions,
        ]));
        $rolePermissions['manager'] = array_values(array_unique([
            ...(array) ($rolePermissions['manager'] ?? []),
            'seo_redirects.view',
        ]));

        config()->set('lazy.admin.permissions.role_permissions', $rolePermissions);
    }
}
