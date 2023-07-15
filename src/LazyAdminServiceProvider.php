<?php

namespace Step2dev\LazyAdmin;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Step2dev\LazyAdmin\Commands\LazyAdminCommand;
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
            ->hasConfigFile('lazy/admin')
            ->hasViews('lazy')
            ->hasTranslations()
            ->hasViewComponents('lazy',
                ThemeSwitcher::class,
            )
            ->hasCommand(LazyAdminCommand::class);
    }
}
