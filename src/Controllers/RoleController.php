<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;
use Step2dev\LazyAdmin\Support\AdminActivity;

class RoleController extends Controller
{
    public function __construct(private readonly AuthorizationManager $authorization) {}

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('roles.create');

        $roleModel = $this->authorization->roleModel();
        $permissionModel = $this->authorization->permissionModel();
        $guard = $this->authorization->guard();
        $roleTable = (new $roleModel)->getTable();
        $permissionTable = (new $permissionModel)->getTable();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($roleTable, 'name')
                    ->where('guard_name', $guard),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists($permissionTable, 'name')
                    ->where('guard_name', $guard),
            ],
        ]);

        $role = $roleModel::findOrCreate($validated['name'], $guard);
        $role->syncPermissions($validated['permissions'] ?? []);

        AdminActivity::log('created', 'Role created', $role, new: $this->auditData($role));

        return redirect()
            ->route($this->routeName('access.index'), ['role' => $role->getRouteKey()])
            ->with('status', __('Role created successfully.'));
    }

    public function update(Request $request, string $role): RedirectResponse
    {
        $this->authorizeAction('roles.edit');

        $model = $this->findRole($role);
        $old = $this->auditData($model);
        $roleModel = $this->authorization->roleModel();
        $permissionModel = $this->authorization->permissionModel();
        $guard = $this->authorization->guard();
        $roleTable = (new $roleModel)->getTable();
        $permissionTable = (new $permissionModel)->getTable();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($roleTable, 'name')
                    ->where('guard_name', $guard)
                    ->ignore($model->getKey()),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists($permissionTable, 'name')
                    ->where('guard_name', $guard),
            ],
        ]);

        $model->name = $validated['name'];
        $model->save();
        $model->syncPermissions($validated['permissions'] ?? []);
        $model->refresh();

        AdminActivity::log('updated', 'Role updated', $model, old: $old, new: $this->auditData($model));

        return redirect()
            ->route($this->routeName('access.index'), ['role' => $model->getRouteKey()])
            ->with('status', __('Role updated successfully.'));
    }

    public function destroy(string $role): RedirectResponse
    {
        $this->authorizeAction('roles.delete');

        $model = $this->findRole($role);

        abort_if(
            $model->name === config('lazy.admin.permissions.super_admin_role', 'superadmin'),
            422,
            'The super admin role cannot be deleted.'
        );

        $old = $this->auditData($model);
        $model->delete();

        AdminActivity::log('deleted', 'Role deleted', $model, old: $old);

        return redirect()
            ->route($this->routeName('access.index'))
            ->with('status', __('Role deleted successfully.'));
    }

    private function auditData($role): array
    {
        return [
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $role->permissions()->pluck('name')->sort()->values()->all(),
        ];
    }

    private function findRole(string $value)
    {
        $roleModel = $this->authorization->roleModel();

        return $roleModel::query()
            ->where('guard_name', $this->authorization->guard())
            ->where((new $roleModel)->getRouteKeyName(), $value)
            ->firstOrFail();
    }

    private function authorizeAction(string $permission): void
    {
        if (! config('lazy.admin.permissions.enforce', true)) {
            return;
        }

        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_unless($user && $user->can($permission), 403);
    }

    private function routeName(string $route): string
    {
        return trim((string) config('lazy.admin.route.name', 'admin.'), '.').'.'.$route;
    }
}
