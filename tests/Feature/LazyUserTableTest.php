<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\PermissionServiceProvider;
use Step2dev\LazyAdmin\Http\Livewire\Users\Table;
use Step2dev\LazyAdmin\Tests\Fixtures\User;

beforeEach(function (): void {
    config()->set([
        'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
        'database.connections.testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        'auth.providers.users.model' => User::class,
        'lazy.auth.providers.users.model' => User::class,
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

    Route::get('/admin/user/{user}/edit', fn () => 'Edit user')->name('admin.user.edit');
    Route::getRoutes()->refreshNameLookups();

    $this->admin = User::factory()->create(['name' => 'Administrator']);
    $this->admin->assignRole(Role::findOrCreate('admin', 'web'));
    $this->actingAs($this->admin);
});

it('registers and renders the packaged users component', function (): void {
    Livewire::test('lazy-admin.users.table')->assertOk()->assertSee($this->admin->email);
});

it('searches users by name and email', function (): void {
    $match = User::factory()->create(['name' => 'Unique Person', 'email' => 'needle@example.com']);
    $other = User::factory()->create(['name' => 'Other Person']);

    Livewire::test(Table::class)
        ->set('search', 'Unique Person')
        ->assertSee($match->email)->assertDontSee($other->email)
        ->set('search', 'needle@example.com')
        ->assertSee($match->name)->assertDontSee($other->email)
        ->set('search', 'no matching users')
        ->assertSee('No users found.');
});

it('filters inclusive creation dates and clears filters', function (): void {
    $match = User::factory()->create(['created_at' => '2025-06-15 23:59:59']);
    $older = User::factory()->create(['created_at' => '2025-06-14 23:59:59']);
    $newer = User::factory()->create(['created_at' => '2025-06-16 00:00:00']);

    Livewire::test(Table::class)
        ->set('createdFrom', '2025-06-15')->set('createdUntil', '2025-06-15')
        ->assertSee($match->email)->assertDontSee($older->email)->assertDontSee($newer->email)
        ->call('resetFilters')->assertSee($older->email)->assertSee($newer->email);
});

it('paginates and resets the page when searching', function (): void {
    User::factory()->count(21)->create();

    Livewire::test(Table::class)->set('perPage', 10)
        ->assertViewHas('users', fn ($users) => $users->count() === 10 && $users->total() === 22)
        ->call('setPage', 2)->assertSet('paginators.page', 2)
        ->set('search', 'Administrator')->assertSet('paginators.page', 1)
        ->assertSee($this->admin->email);
});

it('sorts by allowed columns and ignores tampered query properties', function (): void {
    User::factory()->create(['name' => 'Zzz Last']);

    Livewire::test(Table::class)->call('sortBy', 'name')
        ->assertSet('sortDirection', 'asc')
        ->assertViewHas('users', fn ($users) => $users->first()->name === 'Administrator')
        ->call('sortBy', 'name')->assertSet('sortDirection', 'desc')
        ->assertViewHas('users', fn ($users) => $users->first()->name === 'Zzz Last')
        ->set('sortField', 'password')->set('sortDirection', 'invalid')->set('perPage', -1)
        ->assertOk()->assertViewHas('users', fn ($users) => $users->perPage() === 20);

    Livewire::test(Table::class)->call('sortBy', 'password')->assertStatus(422);
});

it('shows edit links only with permission', function (): void {
    $user = User::factory()->create();
    $url = route('admin.user.edit', $user);

    Livewire::test(Table::class)->assertDontSee($url, false);

    $this->admin->givePermissionTo(Permission::findOrCreate('user_edit', 'web'));

    Livewire::test(Table::class)->assertSee($url, false);
});

it('rejects guests and users without an admin role', function (): void {
    auth()->logout();
    Livewire::test(Table::class)->assertForbidden();

    $this->actingAs(User::factory()->create());
    Livewire::test(Table::class)->assertForbidden();
});

it('rechecks access on subsequent requests', function (): void {
    $component = Livewire::test(Table::class)->assertOk();
    $this->admin->syncRoles([]);

    $component->set('search', 'Administrator')->assertForbidden();
});

it('escapes user supplied names', function (): void {
    User::factory()->create(['name' => '<script>alert(1)</script>']);

    Livewire::test(Table::class)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false);
});
