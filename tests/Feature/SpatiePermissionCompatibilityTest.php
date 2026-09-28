<?php

namespace Step2dev\LazyAdmin\Tests\Feature;

use Composer\InstalledVersions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;

it('supports Spatie permission 7 and 8 without changing the admin authorization contract', function (): void {
    config()->set([
        'permission.models.role' => Role::class,
        'permission.models.permission' => Permission::class,
    ]);

    $version = InstalledVersions::getPrettyVersion('spatie/laravel-permission');

    expect($version)->not->toBeNull()
        ->and(version_compare(ltrim((string) $version, 'v'), '7.0.0', '>='))->toBeTrue()
        ->and(app(AuthorizationManager::class)->roleModel())->toBe(Role::class)
        ->and(app(AuthorizationManager::class)->permissionModel())->toBe(Permission::class);
});

