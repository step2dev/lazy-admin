<?php

namespace Step2dev\LazyAdmin;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Step2dev\LazyAdmin\Commands\LazyAdminCommand;
use Step2dev\LazyAdmin\Components\Footer;
use Step2dev\LazyAdmin\Components\ThemeSwitcher;
use Spatie\Permission\PermissionServiceProvider;

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
            ->hasConfigFile('lazy/admin')
            ->hasViews('lazy')
            ->hasTranslations()
            ->hasMigrations([
                'create_settings_table',
            ])
            ->hasInstallCommand(static function (InstallCommand $command) {
                $command
                    ->callSilently('vendor:publish', [
                        '--provider' => PermissionServiceProvider::class,
                    ]);

                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->publishAssets()
                    ->askToStarRepoOnGitHub('step2dev/lazy-admin');

            })
            ->hasViewComponents('lazy',
                ThemeSwitcher::class,
                Footer::class,
            )
            ->sharesDataWithAllViews('companyName', 'Step2Dev')
            ->sharesDataWithAllViews('companyUrl', 'https://step2.dev')
            ->sharesDataWithAllViews('githubUrl', 'https://github.com/step2dev/lazy-admin')
            ->hasCommand(LazyAdminCommand::class);
    }
}
