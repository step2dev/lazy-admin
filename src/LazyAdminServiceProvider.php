<?php

namespace Step2dev\LazyAdmin;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Step2dev\LazyAdmin\Commands\DbOptimize;
use Step2dev\LazyAdmin\Commands\LazyAdminCommand;
use Step2dev\LazyAdmin\Components\Footer;
use Step2dev\LazyAdmin\Components\ThemeSwitcher;

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
            // ->publishesServiceProvider('LazyRouteServiceProvider')
            //            ->hasAssets()
            ->hasRoute('admin')
            ->hasInstallCommand(static function (InstallCommand $command) {
                $command
                    ->startWith(static function (InstallCommand $installCommand) {
                        $installCommand->info('Installing Lazy Admin...');
                    })
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
//                    ->publishAssets()
                    ->askToStarRepoOnGitHub('step2dev/lazy-admin')
                    ->endWith(static function (InstallCommand $installCommand) {
                        $installCommand->info('Lazy Admin installed successfully. Enjoy!');
                    });
            })
            ->hasViewComponents('lazy',
                ThemeSwitcher::class,
                Footer::class,
            )
            ->sharesDataWithAllViews('companyName', 'Step2Dev')
            ->sharesDataWithAllViews('companyUrl', 'https://step2.dev')
            ->sharesDataWithAllViews('githubUrl', 'https://github.com/step2dev/lazy-admin')
            ->hasCommands([
                LazyAdminCommand::class,
                DbOptimize::class,
            ]);

    }
}
