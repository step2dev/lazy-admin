<?php

namespace Step2dev\LazyAdmin\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Step2dev\LazyAdmin\LazyAdminServiceProvider;
use Step2dev\LazyPage\LazyPageServiceProvider;
use Step2dev\LazyUI\LazyUiServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Step2dev\\LazyAdmin\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ActivitylogServiceProvider::class,
            LazyUiServiceProvider::class,
            LazyPageServiceProvider::class,
            LazyAdminServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_lazy-admin_table.php.stub';
        $migration->up();
        */
    }
}
