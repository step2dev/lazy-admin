<?php

namespace Step2dev\LazyAdmin;

use Illuminate\Routing\Router;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Step2dev\LazyAdmin\Commands\CreateAdminCommand;
use Step2dev\LazyAdmin\Commands\DbOptimize;
use Step2dev\LazyAdmin\Commands\LazyAdminCommand;
use Step2dev\LazyAdmin\Components\Footer;
use Step2dev\LazyAdmin\Components\Header;
use Step2dev\LazyAdmin\Components\LanguageSwitcher;
use Step2dev\LazyAdmin\Components\Layout;
use Step2dev\LazyAdmin\Database\Seeders\DatabaseSeeder;
use Step2dev\LazyAdmin\Http\Livewire\Settings\Setting;
use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;
use Step2dev\LazyAdmin\Localization\LocalizationManager;
use Step2dev\LazyAdmin\Routing\ConfigProvider;
use Step2dev\LazyAdmin\Routing\Router as AdminRouter;
use Step2dev\LazyAdmin\Services\SettingService;

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
            ->hasRoute('admin')
            ->hasInstallCommand(static function (InstallCommand $command) {
                $command
                    ->startWith(static function (InstallCommand $installCommand) {
                        $installCommand->info('Installing Lazy Admin...');
                        $installCommand->call('lazy-ui:install');
                    })
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->publish('lazy-admin', 'lazy')
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
        $this->app->singleton('settings', fn ($app) => new SettingService());
        $this->app->bind(LocalizationInterface::class, config('lazy.localization.localizationManager', LocalizationManager::class));
    }

    public function packageRegistered(): void
    {
        Livewire::component('settings.setting', Setting::class);
    }

    public function bootingPackage(): void
    {
    }

    public function packageBooted(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router?->mixin(new AdminRouter(new ConfigProvider()));

    }
}
