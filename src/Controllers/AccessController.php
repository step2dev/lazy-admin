<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;

class AccessController extends Controller
{
    public function __construct(private readonly AuthorizationManager $authorization) {}

    public function __invoke(Request $request): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();
        $enforce = (bool) config('lazy.admin.permissions.enforce', true);

        abort_unless(
            $user && (
                ! $enforce
                || $user->can('roles.view')
                || $user->can('permissions.view')
            ),
            403
        );

        $roles = $this->authorization->roles()->load('permissions');
        $permissions = $this->authorization->permissions()->load('roles');
        $selectedRoleKey = $request->query('role');

        $selectedRole = $roles->first(
            static fn ($role): bool => $selectedRoleKey !== null
                && (string) $role->getRouteKey() === (string) $selectedRoleKey
        ) ?? $roles->first();

        $routePrefix = trim((string) config('lazy.admin.route.name', 'admin.'), '.');
        $superAdminRole = (string) config('lazy.admin.permissions.super_admin_role', 'superadmin');

        $canViewRoles = ! $enforce || $user->can('roles.view');
        $canCreateRoles = ! $enforce || $user->can('roles.create');
        $canEditRoles = ! $enforce || $user->can('roles.edit');
        $canDeleteRoles = ! $enforce || $user->can('roles.delete');

        $canViewPermissions = ! $enforce || $user->can('permissions.view');
        $canCreatePermissions = ! $enforce || $user->can('permissions.create');
        $canEditPermissions = ! $enforce || $user->can('permissions.edit');
        $canDeletePermissions = ! $enforce || $user->can('permissions.delete');

        $selectedIsSuperAdmin = $selectedRole?->name === $superAdminRole;
        $permissionsEditable = $canEditRoles && ! $selectedIsSuperAdmin;

        $roleItems = $roles->map(function ($role) use ($routePrefix, $selectedRole, $superAdminRole): array {
            $active = $selectedRole
                && (string) $selectedRole->getRouteKey() === (string) $role->getRouteKey();

            return [
                'role' => $role,
                'href' => route($routePrefix.'.access.index', ['role' => $role->getRouteKey()]),
                'active' => $active,
                'superAdmin' => $role->name === $superAdminRole,
                'classes' => 'block rounded-2xl border px-5 py-4 transition duration-150 '.(
                    $active
                        ? 'border-primary bg-base-100 shadow-sm ring-1 ring-primary/20'
                        : 'border-base-300 bg-base-200/40 hover:border-base-content/25 hover:bg-base-200/70'
                ),
            ];
        });

        $permissionGroups = [];

        foreach ($permissions as $permission) {
            $group = str_contains($permission->name, '.')
                ? str($permission->name)->before('.')->headline()->toString()
                : __('Other');

            $permissionGroups[$group][] = [
                'permission' => $permission,
                'label' => str_contains($permission->name, '.')
                    ? str($permission->name)->after('.')->replace('.', ' · ')->toString()
                    : $permission->name,
                'checked' => $selectedIsSuperAdmin
                    || ($selectedRole && $selectedRole->hasPermissionTo($permission)),
                'classes' => 'flex min-h-20 items-center gap-4 rounded-2xl border p-4 transition '.(
                    $permissionsEditable
                        ? 'cursor-pointer border-base-300 bg-base-200/20 hover:border-primary/40'
                        : 'cursor-default border-base-300 bg-base-200/10 opacity-75'
                ),
            ];
        }

        return lazyView('lazy::access.index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'selectedRole' => $selectedRole,
            'routePrefix' => $routePrefix,
            'superAdminRole' => $superAdminRole,
            'canViewRoles' => $canViewRoles,
            'canCreateRoles' => $canCreateRoles,
            'canEditRoles' => $canEditRoles,
            'canDeleteRoles' => $canDeleteRoles,
            'canViewPermissions' => $canViewPermissions,
            'canCreatePermissions' => $canCreatePermissions,
            'canEditPermissions' => $canEditPermissions,
            'canDeletePermissions' => $canDeletePermissions,
            'selectedIsSuperAdmin' => $selectedIsSuperAdmin,
            'permissionsEditable' => $permissionsEditable,
            'roleItems' => $roleItems,
            'permissionGroups' => $permissionGroups,
            'layoutClass' => $canViewRoles
                ? 'grid gap-8 xl:grid-cols-[380px_minmax(0,1fr)]'
                : 'grid gap-8 grid-cols-1',
        ]);
    }
}
