<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\PermissionServiceProvider;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;
use Step2dev\LazyAdmin\Controllers\PermissionController;
use Step2dev\LazyAdmin\Controllers\RoleController;
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

    Route::middleware(['web', 'auth', LazyAdminMiddleware::class])
        ->prefix('admin-test')
        ->name('admin.')
        ->group(function (): void {
            Route::resource('role', RoleController::class)->except(['show']);
            Route::resource('permission', PermissionController::class)->except(['show']);
        });

    Route::getRoutes()->refreshNameLookups();
});

it('seeds default roles and permissions idempotently', function (): void {
    app(AuthorizationManager::class)->seedDefaults();

    expect(Role::where('guard_name', 'web')->pluck('name')->all())
        ->toContain('superadmin', 'admin', 'manager', 'moderator')
        ->and(Permission::where('guard_name', 'web')->pluck('name')->all())
        ->toContain(
            'admin_access',
            'user_view',
            'role_view',
            'permission_view',
            'permission_delete',
        );

    $superadmin = Role::findByName('superadmin', 'web');

    expect($superadmin->permissions()->count())
        ->toBe(count(config('lazy.admin.permissions.defaults')));
});

it('grants superadmin every gate ability', function (): void {
    $user = User::factory()->create();
    $user->assignRole('superadmin');

    $this->actingAs($user);

    expect(Gate::allows('permission_that_does_not_exist'))->toBeTrue();
});

it('allows admins with admin_access through middleware', function (): void {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user);

    $this->get('/admin-test/role')->assertOk();
});

it('rejects authenticated users without admin access', function (): void {
    $this->actingAs(User::factory()->create());

    $this->get('/admin-test/role')->assertForbidden();
});

it('creates and updates roles with permissions', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');
    $this->actingAs($admin);

    $this->post('/admin-test/role', [
        'name' => 'support',
        'permissions' => ['admin_access', 'user_view'],
    ])->assertRedirect();

    $role = Role::findByName('support', 'web');

    expect($role->hasPermissionTo('admin_access'))->toBeTrue()
        ->and($role->hasPermissionTo('user_view'))->toBeTrue();

    $this->put('/admin-test/role/'.$role->getKey(), [
        'name' => 'support-team',
        'permissions' => ['admin_access'],
    ])->assertRedirect();

    $role->refresh();

    expect($role->name)->toBe('support-team')
        ->and($role->permissions()->pluck('name')->all())->toBe(['admin_access']);
});

it('creates updates and deletes permissions', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');
    $this->actingAs($admin);

    $this->post('/admin-test/permission', [
        'name' => 'reports_view',
    ])->assertRedirect();

    $permission = Permission::findByName('reports_view', 'web');

    $this->put('/admin-test/permission/'.$permission->getKey(), [
        'name' => 'reports_manage',
    ])->assertRedirect();

    $permission->refresh();
    expect($permission->name)->toBe('reports_manage');

    $this->delete('/admin-test/permission/'.$permission->getKey())
        ->assertRedirect();

    expect(Permission::where('name', 'reports_manage')->exists())->toBeFalse();
});

it('does not allow deleting the superadmin role', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');
    $this->actingAs($admin);

    $role = Role::findByName('superadmin', 'web');

    $this->delete('/admin-test/role/'.$role->getKey())
        ->assertStatus(422);

    expect(Role::findByName('superadmin', 'web'))->not->toBeNull();
});

it('rejects permissions from a different guard when assigning a role', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('superadmin');
    $this->actingAs($admin);

    Permission::findOrCreate('api-only', 'api');

    $this->from('/admin-test/role/create')
        ->post('/admin-test/role', [
            'name' => 'invalid-role',
            'permissions' => ['api-only'],
        ])
        ->assertRedirect('/admin-test/role/create')
        ->assertSessionHasErrors('permissions.0');

    expect(Role::where('name', 'invalid-role')->exists())->toBeFalse();
});


it('persists Lazy Admin authorization across Livewire requests', function (): void {
    expect(app(PersistentMiddleware::class)->getPersistentMiddleware())
        ->toContain(LazyAdminMiddleware::class);
});
