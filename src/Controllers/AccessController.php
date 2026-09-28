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
        $enforcePermissions = (bool) config('lazy.admin.permissions.enforce', true);

        abort_unless(
            $user && (
                ! $enforcePermissions
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

        $canViewRoles = ! $enforcePermissions || $user->can('roles.view');
        $canCreateRoles = ! $enforcePermissions || $user->can('roles.create');
        $canEditRoles = ! $enforcePermissions || $user->can('roles.edit');
        $canDeleteRoles = ! $enforcePermissions || $user->can('roles.delete');

        $canViewPermissions = ! $enforcePermissions || $user->can('permissions.view');
        $canCreatePermissions = ! $enforcePermissions || $user->can('permissions.create');
        $canEditPermissions = ! $enforcePermissions || $user->can('permissions.edit');
        $canDeletePermissions = ! $enforcePermissions || $user->can('permissions.delete');

        $selectedIsSuperAdmin = $selectedRole?->name === $superAdminRole;
        $rolePermissionsEditable = $canEditRoles && ! $selectedIsSuperAdmin;

        $roleItems = $roles->map(function ($role) use ($routePrefix, $selectedRole, $superAdminRole): array {
            $active = $selectedRole
                && (string) $selectedRole->getRouteKey() === (string) $role->getRouteKey();

            return [
                'model' => $role,
                'name' => (string) $role->name,
                'guard' => (string) $role->guard_name,
                'superAdmin' => $role->name === $superAdminRole,
                'url' => route($routePrefix.'.access.index', ['role' => $role->getRouteKey()]),
                'classes' => implode(' ', array_filter([
                    'block rounded-2xl border px-5 py-4 transition duration-150',
                    $active
                        ? 'border-primary bg-base-100 shadow-sm ring-1 ring-primary/20'
                        : 'border-base-300 bg-base-200/40 hover:border-base-content/25 hover:bg-base-200/70',
                ])),
            ];
        })->values();

        $permissionGroups = $permissions
            ->groupBy(
                static fn ($permission) => str_contains($permission->name, '.')
                    ? str($permission->name)->before('.')->headline()->toString()
                    : __('Other')
            )
            ->map(function ($groupPermissions, string $group) use (
                $selectedRole,
                $selectedIsSuperAdmin,
                $rolePermissionsEditable,
            ): array {
                return [
                    'label' => $group,
                    'count' => $groupPermissions->count(),
                    'items' => $groupPermissions->map(function ($permission) use (
                        $selectedRole,
                        $selectedIsSuperAdmin,
                        $rolePermissionsEditable,
                    ): array {
                        $name = (string) $permission->name;

                        return [
                            'model' => $permission,
                            'name' => $name,
                            'label' => str_contains($name, '.')
                                ? str($name)->after('.')->replace('.', ' · ')->toString()
                                : $name,
                            'checked' => $selectedIsSuperAdmin
                                || ($selectedRole?->hasPermissionTo($permission) ?? false),
                            'disabled' => ! $rolePermissionsEditable,
                            'classes' => implode(' ', array_filter([
                                'flex min-h-20 items-center gap-4 rounded-2xl border p-4 transition',
                                $rolePermissionsEditable
                                    ? 'cursor-pointer border-base-300 bg-base-200/20 hover:border-primary/40'
                                    : 'cursor-default border-base-300 bg-base-200/10 opacity-75',
                            ])),
                        ];
                    })->values(),
                ];
            })
            ->values();

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
            'rolePermissionsEditable' => $rolePermissionsEditable,
            'roleItems' => $roleItems,
            'permissionGroups' => $permissionGroups,
            'layoutClass' => $canViewRoles
                ? 'grid gap-8 xl:grid-cols-[380px_minmax(0,1fr)]'
                : 'grid grid-cols-1 gap-8',
        ]);
    }
}
