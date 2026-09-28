<?php

namespace Step2dev\LazyAdmin\Tests\Feature;

it('targets Lazy UI 2.x development branch', function (): void {
    $composer = json_decode(
        file_get_contents(dirname(__DIR__, 2).'/composer.json'),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    expect($composer['require']['step2dev/lazy-ui'] ?? null)->toBe('2.x-dev');
});

it('renders Lazy UI 2.x semantic controls used by admin views', function (): void {
    $this->blade('<x-lazy-btn primary sm href="/admin">Open</x-lazy-btn>')
        ->assertSee('btn-primary')
        ->assertSee('btn-sm')
        ->assertSee('href="/admin"', false);

    $this->blade('<x-lazy-alert success message="Saved" />')
        ->assertSee('alert-success')
        ->assertSee('Saved');

    $this->blade('<x-lazy-badge outline label="draft" />')
        ->assertSee('badge-outline')
        ->assertSee('draft');

    $this->blade('<x-lazy-breadcrumbs :items="[[\'label\' => \'Admin\', \'href\' => \'/admin\'], [\'label\' => \'Users\']]" />')
        ->assertSee('href="/admin"', false)
        ->assertSee('aria-current="page"', false);
});

it('keeps migrated admin views off manual component classes', function (): void {
    $root = dirname(__DIR__, 2).'/resources/views';

    $views = [
        'users/create.blade.php',
        'users/edit.blade.php',
        'users/show.blade.php',
        'users/partials/form.blade.php',
        'pages/edit.blade.php',
        'pages/index.blade.php',
        'pages/preview.blade.php',
        'breadcrumb-trail.blade.php',
    ];

    foreach ($views as $view) {
        $source = file_get_contents($root.'/'.$view);

        expect($source)
            ->not->toMatch('/class="[^"]*\bbtn(?:-|\s)/')
            ->not->toMatch('/class="[^"]*\balert(?:-|\s)/')
            ->not->toMatch('/class="[^"]*\bbadge(?:-|\s)/');
    }
});

it('keeps second-wave migrated views on Lazy UI controls', function (): void {
    $root = dirname(__DIR__, 2).'/resources/views';

    $views = [
        'seo/redirects/index.blade.php',
        'pages/users/table.blade.php',
        'pages/partials/form.blade.php',
        'language-switcher.blade.php',
        'menu-label.blade.php',
    ];

    foreach ($views as $view) {
        $source = file_get_contents($root.'/'.$view);

        expect($source)
            ->not->toMatch('/class="[^"]*\\bbtn(?:-|\\s)/')
            ->not->toMatch('/class="[^"]*\\binput(?:-|\\s)/')
            ->not->toMatch('/class="[^"]*\\bselect(?:-|\\s)/')
            ->not->toMatch('/class="[^"]*\\bcheckbox(?:-|\\s)/')
            ->not->toMatch('/class="[^"]*\\bbadge(?:-|\\s)/')
            ->not->toMatch('/class="[^"]*\\balert(?:-|\\s)/');
    }
});
