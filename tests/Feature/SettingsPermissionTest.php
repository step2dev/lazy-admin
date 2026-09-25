<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\PermissionServiceProvider;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;
use Step2dev\LazyAdmin\Http\Livewire\Settings\Page as SettingsPage;
use Step2dev\LazyAdmin\Http\Livewire\Settings\Setting;
use Step2dev\LazyAdmin\Middleware\LazyAdminMiddleware;
use Step2dev\LazyAdmin\Tests\Fixtures\User;

beforeEach(function (): void {
    $this->withoutVite();

    config()->set([
        'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
        'database.connections.testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        'auth.defaults.guard' => 'web',
        'auth.guards.web' => ['driver' => 'session', 'provider' => 'users'],
        'auth.providers.users' => ['driver' => 'eloquent', 'model' => User::class],
        'lazy.auth.guard' => 'web',
        'lazy.admin.route.name' => 'admin-test.',
        'lazy.admin.permissions.enforce' => true,
        'lazy.admin.permissions.guard' => 'web',
        'lazy.admin.permissions.super_admin_role' => 'superadmin',
        'lazy.admin.permissions.seed' => true,
    ]);

    $this->app->register(PermissionServiceProvider::class);

    Schema::create('users', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });

    $permissionPath = dirname((new ReflectionClass(PermissionRegistrar::class))->getFileName(), 2);
    $migration = require $permissionPath.'/database/migrations/create_permission_tables.php.stub';
    $migration->up();

    app(AuthorizationManager::class)->seedDefaults();

    $this->settingsStore = Mockery::mock();
    $this->settingsStore->shouldReceive('all')->andReturn(collect([
        (object) ['group' => 'admin', 'key' => 'name', 'value' => 'Example site'],
        (object) ['group' => 'admin', 'key' => 'description', 'value' => 'Example description'],
    ]));
    $this->settingsStore->shouldReceive('get')->with('admin.logo', null)->andReturn(null);
    $this->app->instance('setting', $this->settingsStore);

    Route::middleware(['web', 'auth', LazyAdminMiddleware::class])
        ->prefix('admin-test')
        ->name('admin-test.')
        ->group(function (): void {
            Route::get('setting', SettingsPage::class)->name('setting.index');
        });

    Route::post('/logout', fn () => response()->noContent())->name('logout');

    Route::getRoutes()->refreshNameLookups();
});

it('blocks settings for users without settings.view', function (): void {
    $user = User::factory()->create();
    $user->assignRole('moderator');

    $this->actingAs($user);

    $this->get('/admin-test/setting')->assertForbidden();
});

it('allows settings.view without settings.edit but keeps the form read only', function (): void {
    $user = User::factory()->create();
    $user->assignRole('moderator');
    $user->givePermissionTo('settings.view');

    $this->actingAs($user);

    $this->get('/admin-test/setting')
        ->assertOk()
        ->assertSee('Read only');

    Livewire::actingAs($user)
        ->test(Setting::class)
        ->call('save')
        ->assertForbidden();
});

it('allows admins to edit settings', function (): void {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->settingsStore->shouldReceive('set')->once()->with('admin.name', 'Updated');
    $this->settingsStore->shouldReceive('set')->once()->with('admin.description', 'Example description');

    Livewire::actingAs($user)
        ->test(Setting::class)
        ->set('settings.name', 'Updated')
        ->call('save')
        ->assertHasNoErrors();
});
