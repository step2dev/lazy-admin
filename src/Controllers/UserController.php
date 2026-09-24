<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;

class UserController extends Controller
{
    public function __construct(private readonly AuthorizationManager $authorization) {}

    public function index(): View
    {
        $this->authorizeUserAction('user_view');

        return view('lazy::users.index');
    }

    public function create(): View
    {
        $this->authorizeUserAction('user_create');

        $model = $this->newUserModel();

        return view('lazy::users.create', [
            'user' => $model,
            'roles' => $this->availableRoles($model),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeUserAction('user_create');

        $model = $this->newUserModel();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique($model->getTable(), 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => $this->roleRules(),
        ]);

        $model->setAttribute('name', $validated['name']);
        $model->setAttribute('email', $validated['email']);
        $model->setAttribute('password', Hash::make($validated['password']));
        $model->save();

        $this->syncRoles($model, $validated['roles'] ?? []);

        return redirect()
            ->route($this->routeName('user.edit'), $model)
            ->with('status', __('User created successfully.'));
    }

    public function show(string $user): View
    {
        $this->authorizeUserAction('user_view');

        return view('lazy::users.show', [
            'user' => $this->findUser($user),
        ]);
    }

    public function edit(string $user): View
    {
        $this->authorizeUserAction('user_edit');

        $model = $this->findUser($user);

        return view('lazy::users.edit', [
            'user' => $model,
            'roles' => $this->availableRoles($model),
        ]);
    }

    public function update(Request $request, string $user): RedirectResponse
    {
        $this->authorizeUserAction('user_edit');

        $model = $this->findUser($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique($model->getTable(), 'email')->ignore($model->getKey(), $model->getKeyName()),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => $this->roleRules(),
        ]);

        $model->setAttribute('name', $validated['name']);
        $model->setAttribute('email', $validated['email']);

        if (! empty($validated['password'])) {
            $model->setAttribute('password', Hash::make($validated['password']));
        }

        $model->save();

        $this->syncRoles($model, $validated['roles'] ?? []);

        return back()->with('status', __('User updated successfully.'));
    }

    public function destroy(Request $request, string $user): RedirectResponse
    {
        $this->authorizeUserAction('user_delete');

        $model = $this->findUser($user);
        $authenticatedUser = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_if(
            $authenticatedUser instanceof Model && $authenticatedUser->is($model),
            422,
            'You cannot delete your own account from Lazy Admin.'
        );

        $model->delete();

        return redirect()
            ->route($this->routeName('user.index'))
            ->with('status', __('User deleted successfully.'));
    }

    protected function findUser(string $value): Model
    {
        $model = $this->newUserModel();

        return $model->newQuery()
            ->where($model->getRouteKeyName(), $value)
            ->firstOrFail();
    }

    protected function newUserModel(): Model
    {
        $guard = (string) config('lazy.auth.guard', 'web');
        $provider = config('lazy.auth.provider') ?: config("auth.guards.{$guard}.provider", 'users');
        $modelClass = config("auth.providers.{$provider}.model");

        if (! is_string($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
            throw new RuntimeException('Lazy Admin could not resolve the configured authentication user model.');
        }

        return new $modelClass;
    }

    protected function availableRoles(Model $user): array
    {
        if (! method_exists($user, 'roles')) {
            return [];
        }

        return $this->authorization->roles()
            ->pluck('name')
            ->all();
    }

    protected function syncRoles(Model $user, array $roles): void
    {
        if ($roles !== [] && ! method_exists($user, 'syncRoles')) {
            abort(422, 'The configured user model must use HasLazyAdminPermissions or Spatie HasRoles.');
        }

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles($roles);
        }
    }

    protected function roleRules(): array
    {
        $roleModel = $this->authorization->roleModel();

        return [
            'string',
            Rule::exists((new $roleModel)->getTable(), 'name')
                ->where('guard_name', $this->authorization->guard()),
        ];
    }

    protected function authorizeUserAction(string $permission): void
    {
        if (! config('lazy.admin.permissions.enforce', true)) {
            return;
        }

        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_unless($user && $user->can($permission), 403);
    }

    protected function routeName(string $route): string
    {
        return trim((string) config('lazy.admin.route.name', 'admin.'), '.').'.'.$route;
    }
}
