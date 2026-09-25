<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Step2dev\LazyAdmin\Localization\Contracts\LocalizationInterface;
use Step2dev\LazyAdmin\Support\AdminActivity;
use Step2dev\LazyPage\Enums\PageStatus;
use Step2dev\LazyPage\Models\Page;
use Step2dev\LazyPage\Models\PageTranslation;

class PageController extends Controller
{
    public function __construct(private readonly LocalizationInterface $localization) {}

    public function index(Request $request): View
    {
        $this->authorizePageAction('pages.view');

        $query = Page::query()->with('translations');

        if ($request->filled('search')) {
            $search = '%'.trim((string) $request->string('search')).'%';
            $query->where(function ($query) use ($search): void {
                $query->where('slug', 'like', $search)
                    ->orWhere('key', 'like', $search)
                    ->orWhereHas('translations', function ($translationQuery) use ($search): void {
                        $translationQuery->where('title', 'like', $search);
                    });
            });
        }

        if ($request->filled('status') && PageStatus::tryFrom((string) $request->string('status'))) {
            $query->where('status', (string) $request->string('status'));
        }

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        return lazyView('lazy::pages.index', [
            'pages' => $query->orderByDesc('id')->paginate(20)->withQueryString(),
            'statuses' => PageStatus::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorizePageAction('pages.create');

        return lazyView('lazy::pages.create', [
            'page' => new Page,
            'locales' => $this->locales(),
            'parents' => $this->parentOptions(),
            'statuses' => PageStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizePageAction('pages.create');

        $validated = $this->validatePage($request);

        $page = new Page;
        $this->fillPage($page, $validated);
        $this->fillTranslations($page, $validated['translations']);
        $page->save();

        AdminActivity::log('created', 'Page created', $page, new: $this->auditData($page));

        return redirect()
            ->route($this->routeName('page.edit'), $page)
            ->with('status', __('Page created successfully.'));
    }

    public function edit(Page $page): View
    {
        $this->authorizePageAction('pages.edit');

        return lazyView('lazy::pages.edit', [
            'page' => $page->load('translations'),
            'locales' => $this->locales(),
            'parents' => $this->parentOptions($page),
            'statuses' => PageStatus::cases(),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $this->authorizePageAction('pages.edit');

        $validated = $this->validatePage($request, $page);
        $this->validateParent($page, $validated['parent_id'] ?? null);
        $old = $this->auditData($page);

        $this->fillPage($page, $validated);
        $this->fillTranslations($page, $validated['translations']);
        $page->save();
        $page->refresh();

        AdminActivity::log('updated', 'Page updated', $page, old: $old, new: $this->auditData($page));

        return back()->with('status', __('Page updated successfully.'));
    }

    public function preview(Page $page): View
    {
        $this->authorizePageAction('pages.preview');

        return lazyView('lazy::pages.preview', [
            'page' => $page->load(['translations', 'parent']),
            'locales' => $this->locales(),
        ]);
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->authorizePageAction('pages.delete');

        $old = $this->auditData($page);
        $page->delete();

        AdminActivity::log('deleted', 'Page moved to trash', $page, old: $old);

        return redirect()
            ->route($this->routeName('page.index'))
            ->with('status', __('Page moved to trash.'));
    }

    public function restore(int $page): RedirectResponse
    {
        $this->authorizePageAction('pages.restore');

        $model = Page::withTrashed()->findOrFail($page);
        $model->restore();

        AdminActivity::log('restored', 'Page restored', $model, new: $this->auditData($model));

        return redirect()
            ->route($this->routeName('page.edit'), $model)
            ->with('status', __('Page restored successfully.'));
    }

    /**
     * @return list<string>
     */
    private function locales(): array
    {
        $locales = $this->localization->getSupportedLocales();

        if ($locales === []) {
            $configured = config('translatable.locales', []);
            $locales = is_array($configured) ? array_values(array_filter($configured, 'is_string')) : [];
        }

        if ($locales === []) {
            $locales = [app()->getLocale()];
        }

        return array_values(array_unique($locales));
    }

    /**
     * @return Collection<int, Page>
     */
    private function parentOptions(?Page $page = null): Collection
    {
        $query = Page::query()->with('parent')->orderBy('position')->orderBy('slug');

        if ($page !== null && $page->exists) {
            $excluded = [$page->getKey(), ...$page->children()->pluck('id')->all()];
            $query->whereNotIn('id', $excluded);
        }

        return $query->get();
    }

    private function validatePage(Request $request, ?Page $page = null): array
    {
        $pagesTable = (new Page)->getTable();
        $locales = $this->locales();
        $primaryLocale = $request->string('original_locale')->toString() ?: $locales[0];

        $slugRule = Rule::unique($pagesTable, 'slug');
        $keyRule = Rule::unique($pagesTable, 'key');

        if ($page !== null && $page->exists) {
            $slugRule->ignore($page->getKey());
            $keyRule->ignore($page->getKey());
        }

        $rules = [
            'parent_id' => ['nullable', 'integer', Rule::exists($pagesTable, 'id')],
            'key' => ['nullable', 'string', 'max:191', $keyRule],
            'slug' => ['required', 'string', 'max:191', 'not_regex:/\//', $slugRule],
            'status' => ['required', Rule::enum(PageStatus::class)],
            'template' => ['nullable', 'string', 'max:100'],
            'original_locale' => ['required', Rule::in($locales)],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:published_at'],
            'position' => ['nullable', 'integer', 'min:0'],
            'translations' => ['required', 'array'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.content' => ['nullable', 'string'],
            "translations.{$primaryLocale}.title" => ['required', 'string', 'max:255'],
        ];

        $validated = $request->validate($rules);

        if (
            config('lazy.admin.permissions.enforce', true)
            && in_array($validated['status'], [PageStatus::Published->value, PageStatus::Scheduled->value], true)
            && ! $this->can('pages.publish')
        ) {
            abort(403);
        }

        return $validated;
    }

    private function fillPage(Page $page, array $validated): void
    {
        $page->fill([
            'parent_id' => $validated['parent_id'] ?? null,
            'key' => ($validated['key'] ?? null) ?: null,
            'slug' => $validated['slug'],
            'status' => $validated['status'],
            'template' => ($validated['template'] ?? null) ?: null,
            'original_locale' => $validated['original_locale'],
            'published_at' => ($validated['published_at'] ?? null) ?: null,
            'expires_at' => ($validated['expires_at'] ?? null) ?: null,
            'position' => $validated['position'] ?? 0,
        ]);
    }

    private function fillTranslations(Page $page, array $translations): void
    {
        foreach ($this->locales() as $locale) {
            $data = $translations[$locale] ?? [];

            $page->translateOrNew($locale)->fill([
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'content' => $data['content'] ?? null,
            ]);
        }
    }

    private function validateParent(Page $page, mixed $parentId): void
    {
        if ($parentId === null || $parentId === '') {
            return;
        }

        $candidate = Page::query()->findOrFail((int) $parentId);

        while ($candidate !== null) {
            if ($candidate->is($page)) {
                throw ValidationException::withMessages([
                    'parent_id' => __('A page cannot be its own parent or descendant.'),
                ]);
            }

            $candidate = $candidate->parent;
        }
    }

    private function auditData(Page $page): array
    {
        $page->loadMissing('translations');

        $translations = [];

        foreach ($page->translations as $translation) {
            if (! $translation instanceof PageTranslation) {
                continue;
            }

            $translations[(string) $translation->getAttribute('locale')] = [
                'title' => $translation->getAttribute('title'),
                'description' => $translation->getAttribute('description'),
                'content' => $translation->getAttribute('content'),
            ];
        }

        return [
            'parent_id' => $page->parent_id,
            'key' => $page->key,
            'slug' => $page->slug,
            'status' => $page->status->value,
            'template' => $page->template,
            'original_locale' => $page->original_locale,
            'published_at' => $page->published_at?->toISOString(),
            'expires_at' => $page->expires_at?->toISOString(),
            'position' => $page->position,
            'translations' => $translations,
        ];
    }

    private function authorizePageAction(string $permission): void
    {
        if (! config('lazy.admin.permissions.enforce', true)) {
            return;
        }

        abort_unless($this->can($permission), 403);
    }

    private function can(string $permission): bool
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        return (bool) ($user?->can($permission));
    }

    private function routeName(string $route): string
    {
        return trim((string) config('lazy.admin.route.name', 'admin.'), '.').'.'.$route;
    }
}
