<?php

namespace Step2dev\LazyAdmin;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route as RouteFacade;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;
use Step2dev\LazyAdmin\Commands\CreateAdminCommand;
use Step2dev\LazyAdmin\Commands\DbOptimize;
use Step2dev\LazyAdmin\Commands\LazyAdminCommand;
use Step2dev\LazyAdmin\Components\BaseLayout;
use Step2dev\LazyAdmin\Components\Footer;
use Step2dev\LazyAdmin\Components\Header;
use Step2dev\LazyAdmin\Components\LanguageSwitcher;
use Step2dev\LazyAdmin\Components\Layout;
use Step2dev\LazyAdmin\Controllers\AccessController;
use Step2dev\LazyAdmin\Controllers\PermissionController;
use Step2dev\LazyAdmin\Controllers\RoleController;
use Step2dev\LazyAdmin\Controllers\SeoRedirectController;
use Step2dev\LazyAdmin\Database\Seeders\DatabaseSeeder;
use Step2dev\LazyAdmin\Http\Livewire\Settings\Setting;
use Step2dev\LazyAdmin\Http\Livewire\Users\Table;
use Step2dev\LazyAdmin\Integrations\SeoRedirectsIntegration;
use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;
use Step2dev\LazyAdmin\Localization\LocalizationManager;
use Step2dev\LazyAdmin\Middleware\LazyAdminMiddleware;
use Step2dev\LazyAdmin\Routing\Router as AdminRouter;
use Step2Dev\LazyBreadcrumb\LazyBreadcrumbServiceProvider;
use Step2dev\LazyMenu\Facades\Menu as MenuFacade;
use Step2dev\LazyMenu\LazyMenuServiceProvider;
use Step2dev\LazyMenu\Navigation\Menu\Menu;
use Step2dev\LazyMenu\Navigation\Menu\MenuManager;
use Step2dev\LazyMenu\Navigation\Menu\MenuRegistry;

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
            RouteFacade::get('access', AccessController::class)->name('access.index');
            RouteFacade::resource('role', RoleController::class)->only(['store', 'update', 'destroy']);
            RouteFacade::resource('permission', PermissionController::class)->only(['store', 'update', 'destroy']);

            if (SeoRedirectsIntegration::available()) {
                RouteFacade::prefix('seo')->name('seo.')->group(function (): void {
                    RouteFacade::get('redirects', [SeoRedirectController::class, 'index'])->name('redirects.index');
                    RouteFacade::post('redirects', [SeoRedirectController::class, 'store'])->name('redirects.store');
                    RouteFacade::put('redirects/{redirect}', [SeoRedirectController::class, 'update'])->name('redirects.update');
                    RouteFacade::delete('redirects/{redirect}', [SeoRedirectController::class, 'destroy'])->name('redirects.destroy');
                });
            }
        });

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
