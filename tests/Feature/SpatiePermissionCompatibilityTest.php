<?php

namespace Step2dev\LazyAdmin\Tests\Feature;

use Composer\InstalledVersions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;

it('supports Spatie permission 7 and 8 without changing the admin authorization contract', function (): void {
    $version = InstalledVersions::getPrettyVersion('spatie/laravel-permission');

    expect($version)->not->toBeNull()
        ->and(version_compare(ltrim((string) $version, 'v'), '7.0.0', '>='))->toBeTrue()
        ->and(app(AuthorizationManager::class)->roleModel())->toBe(Role::class)
        ->and(app(AuthorizationManager::class)->permissionModel())->toBe(Permission::class);
});

it('keeps the package constraint compatible with existing v7 installs while allowing v8', function (): void {
    $composer = json_decode(
        file_get_contents(dirname(__DIR__, 2).'/composer.json'),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    expect($composer['require']['spatie/laravel-permission'] ?? null)->toBe('^7.0|^8.3');
});
