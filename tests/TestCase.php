<?php

namespace Step2dev\LazyAdmin\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Step2dev\LazyAdmin\LazyAdminServiceProvider;
use Step2dev\LazyPage\LazyPageServiceProvider;
use Step2Dev\LazySetting\LazySettingServiceProvider;
use Step2dev\LazyUI\LazyUiServiceProvider;

class TestCase extends Orchestra
{
    use InteractsWithViews { blade as frameworkBlade; }

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table): void {
                $table->id();
                $table->string('group')->index();
                $table->string('key')->index();
                $table->string('type', 75)->index();
                $table->mediumText('value')->nullable();
                $table->json('options')->nullable();
                $table->boolean('is_encrypted')->default(false)->index();
                $table->unique(['group', 'key']);
                $table->timestamps();
            });
        }

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Step2dev\\LazyAdmin\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function blade(string $template, $data = [])
    {
        $bufferLevel = ob_get_level();

        try {
            return $this->frameworkBlade($template, $data);
        } finally {
            if (str_contains($template, '<x-slot:')) {
                while (ob_get_level() > $bufferLevel) {
                    ob_end_clean();
                }
            }
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            ActivitylogServiceProvider::class,
            LazyUiServiceProvider::class,
            LazyPageServiceProvider::class,
            LazySettingServiceProvider::class,
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
