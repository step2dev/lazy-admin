<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
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

        return lazyView('lazy::access.index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'selectedRole' => $selectedRole,
        ]);
    }
}
