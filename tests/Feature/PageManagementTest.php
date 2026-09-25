<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Controllers\PageController;
use Step2dev\LazyPage\Enums\PageStatus;
use Step2dev\LazyPage\Models\Page;

beforeEach(function (): void {
    $this->withoutVite();

    config()->set([
        'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
        'app.locale' => 'en',
        'app.fallback_locale' => 'en',
        'database.connections.testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
        'lazy.admin.permissions.enforce' => false,
        'lazy.admin.route.name' => 'admin-test.',
        'lazy.auth.guard' => 'web',
        'translatable.locales' => ['en', 'uk', 'pl', 'ru'],
        'translatable.fallback_locale' => 'en',
    ]);

    (include dirname(__DIR__, 2).'/vendor/step2dev/lazy-page/database/migrations/2026_09_25_000000_create_lazy_pages_table.php')->up();

    Route::middleware('web')
        ->prefix('admin-test')
        ->name('admin-test.')
        ->group(function (): void {
            Route::resource('page', PageController::class)->except(['show']);
            Route::get('page/{page}/preview', [PageController::class, 'preview'])->name('page.preview');
            Route::post('page/{page}/restore', [PageController::class, 'restore'])->name('page.restore');
        });

    Route::get('/logout', fn () => response()->noContent())->name('logout');
    Route::getRoutes()->refreshNameLookups();
});

it('creates a multilingual page', function (): void {
    $response = $this->post('/admin-test/page', [
        'slug' => 'privacy-policy',
        'key' => 'privacy-policy',
        'status' => PageStatus::Draft->value,
        'original_locale' => 'en',
        'position' => 0,
        'translations' => [
            'en' => [
                'title' => 'Privacy Policy',
                'description' => 'English description',
                'content' => '<p>English content</p>',
            ],
            'uk' => [
                'title' => 'Ukrainian Privacy Policy',
                'description' => 'Ukrainian description',
                'content' => '<p>Ukrainian content</p>',
            ],
        ],
    ]);

    $page = Page::query()->firstOrFail();

    $response->assertRedirect('/admin-test/page/'.$page->id.'/edit');

    expect($page->slug)->toBe('privacy-policy')
        ->and($page->status)->toBe(PageStatus::Draft)
        ->and($page->translate('en')?->title)->toBe('Privacy Policy')
        ->and($page->translate('uk')?->title)->toBe('Ukrainian Privacy Policy');
});

it('updates publishing state and hierarchy', function (): void {
    $parent = Page::factory()->create([
        'slug' => 'docs',
        'status' => PageStatus::Published,
        'original_locale' => 'en',
    ]);
    $parent->translateOrNew('en')->title = 'Documentation';
    $parent->save();

    $page = Page::factory()->create([
        'slug' => 'install',
        'status' => PageStatus::Draft,
        'original_locale' => 'en',
    ]);
    $page->translateOrNew('en')->title = 'Install';
    $page->save();

    $this->put('/admin-test/page/'.$page->id, [
        'parent_id' => $parent->id,
        'slug' => 'installation',
        'status' => PageStatus::Published->value,
        'original_locale' => 'en',
        'published_at' => now()->subMinute()->format('Y-m-d H:i:s'),
        'position' => 10,
        'translations' => [
            'en' => [
                'title' => 'Installation',
                'description' => null,
                'content' => '<p>Installation guide</p>',
            ],
        ],
    ])->assertRedirect();

    $page->refresh();

    expect($page->parent_id)->toBe($parent->id)
        ->and($page->path())->toBe('docs/installation')
        ->and($page->status)->toBe(PageStatus::Published)
        ->and($page->translate('en')?->title)->toBe('Installation');
});

it('prevents hierarchy cycles', function (): void {
    $parent = Page::factory()->create(['slug' => 'parent', 'original_locale' => 'en']);
    $parent->translateOrNew('en')->title = 'Parent';
    $parent->save();

    $child = Page::factory()->create([
        'slug' => 'child',
        'parent_id' => $parent->id,
        'original_locale' => 'en',
    ]);
    $child->translateOrNew('en')->title = 'Child';
    $child->save();

    $this->from('/admin-test/page/'.$parent->id.'/edit')
        ->put('/admin-test/page/'.$parent->id, [
            'parent_id' => $child->id,
            'slug' => 'parent',
            'status' => PageStatus::Draft->value,
            'original_locale' => 'en',
            'position' => 0,
            'translations' => [
                'en' => ['title' => 'Parent', 'description' => null, 'content' => null],
            ],
        ])
        ->assertRedirect('/admin-test/page/'.$parent->id.'/edit')
        ->assertSessionHasErrors('parent_id');
});

it('soft deletes and restores a page', function (): void {
    $page = Page::factory()->create([
        'slug' => 'about',
        'original_locale' => 'en',
    ]);

    $this->delete('/admin-test/page/'.$page->id)
        ->assertRedirect('/admin-test/page');

    expect(Page::query()->find($page->id))->toBeNull()
        ->and(Page::withTrashed()->find($page->id))->not->toBeNull();

    $this->post('/admin-test/page/'.$page->id.'/restore')
        ->assertRedirect('/admin-test/page/'.$page->id.'/edit');

    expect(Page::query()->find($page->id))->not->toBeNull();
});
