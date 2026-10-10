<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\PermissionServiceProvider;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;
use Step2dev\LazyAdmin\Support\UserSecurity;
use Step2dev\LazyAdmin\Tests\Fixtures\User;

beforeEach(function (): void {
    $this->withoutVite();
    Route::get('/login', fn () => 'Login')->name('login');
    Route::getRoutes()->refreshNameLookups();
    config()->set([
        'database.connections.testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        'auth.providers.users.model' => User::class,
    ]);
    $this->app->register(PermissionServiceProvider::class);
    Schema::create('users', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
    $path = dirname((new ReflectionClass(PermissionRegistrar::class))->getFileName(), 2);
    (require $path.'/database/migrations/create_permission_tables.php.stub')->up();
    app(AuthorizationManager::class)->seedDefaults();
    $this->owner = User::factory()->create([
        'password' => Hash::make('old-password'),
        'email_verified_at' => now(),
        'remember_token' => 'old-token',
    ]);
});

it('allows a non-admin to open only their own profile', function (): void {
    $this->actingAs($this->owner)->get(route('admin.profile.show'))
        ->assertOk()->assertSee($this->owner->email)
        ->assertDontSee('name="roles[]"', false);
});

it('requires authentication for profile writes', function (): void {
    $this->put(route('admin.profile.password'), [])->assertRedirect();
});

it('requires the current password and matching confirmation', function (): void {
    $this->actingAs($this->owner)->put(route('admin.profile.password'), [
        'current_password' => 'wrong',
        'password' => 'new-password',
        'password_confirmation' => 'mismatch',
    ])->assertSessionHasErrors(['current_password', 'password'], errorBag: 'password');
    expect(Hash::check('old-password', $this->owner->fresh()->password))->toBeTrue();
});

it('changes only the authenticated account and rotates the remember token', function (): void {
    $target = User::factory()->create(['password' => Hash::make('target-password')]);
    $this->actingAs($this->owner)->put(route('admin.profile.password'), [
        'user' => $target->getKey(),
        'current_password' => 'old-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertSessionHasNoErrors();
    expect(Hash::check('new-password', $this->owner->fresh()->password))->toBeTrue()
        ->and($this->owner->fresh()->remember_token)->not->toBe('old-token')
        ->and(Hash::check('target-password', $target->fresh()->password))->toBeTrue();
});

it('rejects role injection through the profile without saving changes', function (): void {
    $original = $this->owner->name;
    $this->actingAs($this->owner)->put(route('admin.profile.update'), [
        'name' => 'Injected', 'email' => $this->owner->email,
        'current_password' => 'old-password', 'roles' => ['superadmin'],
    ])->assertSessionHasErrors(['roles'], errorBag: 'profile');
    expect($this->owner->fresh()->name)->toBe($original);
});

it('clears email verification only when the email changes', function (): void {
    $this->actingAs($this->owner)->put(route('admin.profile.update'), [
        'name' => 'Updated', 'email' => 'updated@example.com',
        'current_password' => 'old-password',
    ])->assertSessionHasNoErrors();
    expect($this->owner->fresh()->email_verified_at)->toBeNull();
});

it('allows an authorized admin to change another account password using their own password', function (): void {
    $this->owner->assignRole('admin');
    $target = User::factory()->create(['password' => Hash::make('target-password')]);
    $this->actingAs($this->owner)->put(route('admin.user.password', $target->getKey()), [
        'current_password' => 'old-password',
        'password' => 'new-password', 'password_confirmation' => 'new-password',
    ])->assertSessionHasNoErrors();
    expect(Hash::check('new-password', $target->fresh()->password))->toBeTrue()
        ->and(Hash::check('old-password', $this->owner->fresh()->password))->toBeTrue();
});

it('denies password administration without users edit permission', function (): void {
    $this->owner->assignRole('moderator');
    $target = User::factory()->create();
    $this->actingAs($this->owner)->put(route('admin.user.password', $target->getKey()), [
        'current_password' => 'old-password',
        'password' => 'new-password', 'password_confirmation' => 'new-password',
    ])->assertForbidden();
});

it('denies role escalation for managers before writing any profile fields', function (): void {
    $this->owner->assignRole('manager');
    $target = User::factory()->create();
    $original = $target->name;
    $this->actingAs($this->owner)->put(route('admin.user.update', $target->getKey()), [
        'name' => 'Injected', 'email' => $target->email, 'roles' => ['superadmin'],
    ])->assertForbidden();
    expect($target->fresh()->name)->toBe($original);
});

it('keeps Fortify controls unavailable without a configured integration', function (): void {
    expect(UserSecurity::twoFactorAvailable($this->owner))->toBeFalse();
});
