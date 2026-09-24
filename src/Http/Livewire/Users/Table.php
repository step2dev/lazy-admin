<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Users;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;

class Table extends Component
{
    use WithPagination;

    public string $search = '';

    public string $createdFrom = '';

    public string $createdUntil = '';

    public int $perPage = 20;

    public string $sortField = 'id';

    public string $sortDirection = 'desc';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'createdFrom', 'createdUntil', 'perPage', 'sortField', 'sortDirection'], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $field): void
    {
        abort_unless(in_array($field, ['id', 'name', 'email', 'created_at'], true), 422);

        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'createdFrom', 'createdUntil');
        $this->resetPage();
    }

    protected function usersQuery(): Builder
    {
        $guard = (string) config('lazy.auth.guard', 'web');
        $provider = config('lazy.auth.provider')
            ?: config("auth.guards.{$guard}.provider", 'users');
        $model = config("auth.providers.{$provider}.model");

        if (! is_string($model) || ! is_subclass_of($model, Model::class)) {
            throw new RuntimeException('Lazy Admin could not resolve the configured authentication user model.');
        }

        $query = $model::query();

        if (method_exists($query->getModel(), 'profile')) {
            $query->with('profile');
        }

        if (trim($this->search) !== '') {
            $search = '%'.trim($this->search).'%';
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', $search)->orWhere('email', 'like', $search);
            });
        }

        foreach (['createdFrom' => '>=', 'createdUntil' => '<='] as $property => $operator) {
            if ($this->{$property} !== '' && Validator::make(
                ['date' => $this->{$property}], ['date' => ['date_format:Y-m-d']]
            )->passes()) {
                $query->whereDate('created_at', $operator, $this->{$property});
            }
        }

        $sortField = in_array($this->sortField, ['id', 'name', 'email', 'created_at'], true) ? $this->sortField : 'id';
        $query->orderBy($sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc');

        if ($sortField !== 'id') {
            $query->orderBy('id');
        }

        return $query;
    }

    protected function canManageUsers(object $user, string $permission): bool
    {
        return ! config('lazy.admin.permissions.enforce', true) || $user->can($permission);
    }

    public function render(): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_unless(
            $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(config('lazy.admin.roles', [])),
            403
        );

        $routePrefix = trim(config('lazy.admin.route.name', 'admin.'), '.');
        $editRoute = $routePrefix.'.user.edit';
        $createRoute = $routePrefix.'.user.create';
        $perPage = in_array($this->perPage, [10, 20, 30, 50, 100], true) ? $this->perPage : 20;

        return view('lazy::pages.users.table', [
            'users' => $this->usersQuery()->paginate($perPage),
            'editRoute' => Route::has($editRoute) && $this->canManageUsers($user, 'user_edit') ? $editRoute : null,
            'createRoute' => Route::has($createRoute) && $this->canManageUsers($user, 'user_create') ? $createRoute : null,
        ]);
    }
}
