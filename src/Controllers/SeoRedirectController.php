<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Step2dev\LazyAdmin\Integrations\SeoRedirectsIntegration;
use Step2dev\LazyAdmin\Support\AdminActivity;

class SeoRedirectController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAbility('seo_redirects.view');

        $model = $this->modelClass();
        $search = trim((string) $request->query('search', ''));

        $redirects = $model::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('old_url', 'like', '%'.$search.'%')
                        ->orWhere('new_url', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return lazyView('lazy::seo.redirects.index', [
            'redirects' => $redirects,
            'search' => $search,
            'allowedStatusCodes' => $this->allowedStatusCodes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAbility('seo_redirects.create');

        $model = $this->modelClass();
        $redirect = $model::query()->create($this->validatedData($request));

        AdminActivity::log('created', 'SEO redirect created', $redirect, new: $this->auditData($redirect));

        return $this->redirectToIndex(__('Redirect created successfully.'));
    }

    public function update(Request $request, int $redirect): RedirectResponse
    {
        $this->authorizeAbility('seo_redirects.edit');

        $model = $this->findRedirect($redirect);
        $old = $this->auditData($model);
        $model->fill($this->validatedData($request));
        $model->save();

        AdminActivity::log('updated', 'SEO redirect updated', $model, old: $old, new: $this->auditData($model));

        return $this->redirectToIndex(__('Redirect updated successfully.'));
    }

    public function destroy(int $redirect): RedirectResponse
    {
        $this->authorizeAbility('seo_redirects.delete');

        $model = $this->findRedirect($redirect);
        $old = $this->auditData($model);
        $model->delete();

        AdminActivity::log('deleted', 'SEO redirect deleted', $model, old: $old);

        return $this->redirectToIndex(__('Redirect deleted successfully.'));
    }

    private function auditData(Model $redirect): array
    {
        return [
            'old_url' => $redirect->getAttribute('old_url'),
            'new_url' => $redirect->getAttribute('new_url'),
            'status_code' => $redirect->getAttribute('status_code'),
            'enabled' => $redirect->getAttribute('enabled'),
            'is_regex' => $redirect->getAttribute('is_regex'),
        ];
    }

    /**
     * @return array{old_url:string,new_url:?string,status_code:int,enabled:bool,is_regex:bool}
     */
    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'old_url' => ['required', 'string', 'max:2048'],
            'new_url' => ['nullable', 'string', 'max:2048', 'required_unless:status_code,410'],
            'status_code' => ['required', 'integer', Rule::in($this->allowedStatusCodes())],
            'enabled' => ['nullable', 'boolean'],
            'is_regex' => ['nullable', 'boolean'],
        ]);

        $statusCode = (int) $data['status_code'];

        return [
            'old_url' => (string) $data['old_url'],
            'new_url' => $statusCode === 410 ? null : (string) $data['new_url'],
            'status_code' => $statusCode,
            'enabled' => $request->boolean('enabled'),
            'is_regex' => $request->boolean('is_regex'),
        ];
    }

    /**
     * @return list<int>
     */
    private function allowedStatusCodes(): array
    {
        $configured = array_values(array_unique(array_map(
            static fn ($status): int => (int) $status,
            (array) config('lazy-seo-redirects.allowed_status_codes', [301, 302, 307, 308, 410]),
        )));

        return $configured === [] ? [301, 302, 307, 308, 410] : $configured;
    }

    /**
     * @return class-string<Model>
     */
    private function modelClass(): string
    {
        $model = SeoRedirectsIntegration::modelClass();

        abort_if($model === null, 404);

        return $model;
    }

    private function findRedirect(int $id): Model
    {
        $model = $this->modelClass();

        return $model::query()->findOrFail($id);
    }

    private function authorizeAbility(string $ability): void
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();
        $enforce = (bool) config('lazy.admin.permissions.enforce', true);

        abort_unless($user && (! $enforce || $user->can($ability)), 403);
    }

    private function redirectToIndex(string $status): RedirectResponse
    {
        $prefix = trim((string) config('lazy.admin.route.name', 'admin.'), '.');

        return redirect()
            ->route($prefix.'.seo.redirects.index')
            ->with('status', $status);
    }
}
