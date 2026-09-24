<?php

namespace Step2dev\LazyAdmin\Authorization;

use Illuminate\Database\Eloquent\Collection;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AuthorizationManager
{
    public function guard(): string
    {
        return (string) config('lazy.admin.permissions.guard', config('lazy.auth.guard', 'web'));
    }

    /**
     * @return class-string<Role>
     */
    public function roleModel(): string
    {
        $model = config('permission.models.role');

        if (! is_string($model) || ($model !== Role::class && ! is_subclass_of($model, Role::class))) {
            throw new RuntimeException('Lazy Admin could not resolve the configured Spatie role model.');
        }

        return $model;
    }

    /**
     * @return class-string<Permission>
     */
    public function permissionModel(): string
    {
        $model = config('permission.models.permission');

        if (! is_string($model) || ($model !== Permission::class && ! is_subclass_of($model, Permission::class))) {
            throw new RuntimeException('Lazy Admin could not resolve the configured Spatie permission model.');
        }

        return $model;
    }

    /**
     * @return Collection<int, Role>
     */
    public function roles(): Collection
    {
        $model = $this->roleModel();

        return $model::query()
            ->where('guard_name', $this->guard())
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Permission>
     */
    public function permissions(): Collection
    {
        $model = $this->permissionModel();

        return $model::query()
            ->where('guard_name', $this->guard())
            ->orderBy('name')
            ->get();
    }

    public function seedDefaults(): void
    {
        if (! config('lazy.admin.permissions.seed', true)) {
            return;
        }

        $guard = $this->guard();
        $roleModel = $this->roleModel();
        $permissionModel = $this->permissionModel();

        $permissions = collect(config('lazy.admin.permissions.defaults', []))
            ->filter(static fn ($permission): bool => is_string($permission) && $permission !== '')
            ->unique()
            ->values();

        foreach ($permissions as $permission) {
            $permissionModel::findOrCreate($permission, $guard);
        }

        foreach ((array) config('lazy.admin.permissions.role_permissions', []) as $roleName => $rolePermissions) {
            $role = $roleModel::findOrCreate((string) $roleName, $guard);

            $names = $rolePermissions === ['*']
                ? $permissions->all()
                : collect($rolePermissions)
                    ->filter(static fn ($permission): bool => is_string($permission) && $permission !== '')
                    ->intersect($permissions)
                    ->values()
                    ->all();

            if (method_exists($role, 'syncPermissions')) {
                $role->syncPermissions($names);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
