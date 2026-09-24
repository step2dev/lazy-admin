<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;

class PermissionController extends Controller
{
    public function __construct(private readonly AuthorizationManager $authorization) {}

    public function index(): View
    {
        $this->authorizeAction('permissions.view');

        return view('lazy::permissions.index', [
            'permissions' => $this->authorization->permissions(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeAction('permissions.create');

        return view('lazy::permissions.create', [
            'permission' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('permissions.create');

        $permissionModel = $this->authorization->permissionModel();
        $guard = $this->authorization->guard();
        $table = (new $permissionModel)->getTable();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($table, 'name')->where('guard_name', $guard),
            ],
        ]);

        $permission = $permissionModel::findOrCreate($validated['name'], $guard);

        return redirect()
            ->route($this->routeName('permission.edit'), $permission)
            ->with('status', __('Permission created successfully.'));
    }

    public function edit(string $permission): View
    {
        $this->authorizeAction('permissions.edit');

        return view('lazy::permissions.edit', [
            'permission' => $this->findPermission($permission),
        ]);
    }

    public function update(Request $request, string $permission): RedirectResponse
    {
        $this->authorizeAction('permissions.edit');

        $model = $this->findPermission($permission);
        $permissionModel = $this->authorization->permissionModel();
        $guard = $this->authorization->guard();
        $table = (new $permissionModel)->getTable();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($table, 'name')
                    ->where('guard_name', $guard)
                    ->ignore($model->getKey()),
            ],
        ]);

        $model->name = $validated['name'];
        $model->save();

        return back()->with('status', __('Permission updated successfully.'));
    }

    public function destroy(string $permission): RedirectResponse
    {
        $this->authorizeAction('permissions.delete');

        $model = $this->findPermission($permission);
        $model->delete();

        return redirect()
            ->route($this->routeName('permission.index'))
            ->with('status', __('Permission deleted successfully.'));
    }

    private function findPermission(string $value)
    {
        $permissionModel = $this->authorization->permissionModel();

        return $permissionModel::query()
            ->where('guard_name', $this->authorization->guard())
            ->where((new $permissionModel)->getRouteKeyName(), $value)
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
