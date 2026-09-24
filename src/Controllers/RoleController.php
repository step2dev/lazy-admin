<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;

class RoleController extends Controller
{
    public function __construct(private readonly AuthorizationManager $authorization) {}

    public function index(): View
    {
        $this->authorizeAction('role_view');

        return view('lazy::roles.index', [
            'roles' => $this->authorization->roles(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeAction('role_create');

        return view('lazy::roles.create', [
            'role' => null,
            'permissions' => $this->authorization->permissions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('role_create');

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

        return redirect()
            ->route($this->routeName('role.edit'), $role)
            ->with('status', __('Role created successfully.'));
    }

    public function edit(string $role): View
    {
        $this->authorizeAction('role_edit');

        return view('lazy::roles.edit', [
            'role' => $this->findRole($role),
            'permissions' => $this->authorization->permissions(),
        ]);
    }

    public function update(Request $request, string $role): RedirectResponse
    {
        $this->authorizeAction('role_edit');

        $model = $this->findRole($role);
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

        return back()->with('status', __('Role updated successfully.'));
    }

    public function destroy(string $role): RedirectResponse
    {
        $this->authorizeAction('role_delete');

        $model = $this->findRole($role);

        abort_if(
            $model->name === config('lazy.admin.permissions.super_admin_role', 'superadmin'),
            422,
            'The super admin role cannot be deleted.'
        );

        $model->delete();

        return redirect()
            ->route($this->routeName('role.index'))
            ->with('status', __('Role deleted successfully.'));
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
