<?php

namespace Step2dev\LazyAdmin\Tests\Feature;

use Step2dev\LazyAdmin\Components\Card;
use Step2dev\LazyAdmin\Components\Dropdown;

it('targets Lazy UI 2.x development branch', function (): void {
    $composer = json_decode(
        file_get_contents(dirname(__DIR__, 2).'/composer.json'),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    expect($composer['require']['step2dev/lazy-ui'] ?? null)->toBe('dev-2.x-dev');
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
            ->not->toMatch('/class="[^"]*\bbtn(?=\s|")/')
            ->not->toMatch('/class="[^"]*\balert(?=\s|")/')
            ->not->toMatch('/class="[^"]*\bbadge(?=\s|")/');
    }
});

it('keeps second-wave migrated views on Lazy UI controls', function (): void {
    $root = dirname(__DIR__, 2).'/resources/views';

    $views = [
        'seo/redirects/index.blade.php',
        'pages/users/table.blade.php',
        'pages/partials/form.blade.php',
        'menu-label.blade.php',
    ];

    foreach ($views as $view) {
        $source = file_get_contents($root.'/'.$view);

        expect($source)
            ->not->toMatch('/(?<![:\\w-])class="[^"]*\\bbtn(?:-|\\s)/')
            ->not->toMatch('/(?<![:\\w-])class="[^"]*\\binput(?:-|\\s)/')
            ->not->toMatch('/(?<![:\\w-])class="[^"]*\\bselect(?:-|\\s)/')
            ->not->toMatch('/(?<![:\\w-])class="[^"]*\\bcheckbox(?:-|\\s)/')
            ->not->toMatch('/(?<![:\\w-])class="[^"]*\\bbadge(?:-|\\s)/')
            ->not->toMatch('/(?<![:\\w-])class="[^"]*\\balert(?:-|\\s)/');
    }
});

it('uses Lazy UI Card and Dropdown without admin shadow components', function (): void {
    expect(class_exists(Card::class))->toBeFalse()
        ->and(class_exists(Dropdown::class))->toBeFalse();

    $this
        ->blade('<x-lazy-card href="/admin" hover title="Dashboard">42</x-lazy-card>')
        ->assertSee('<a', false)
        ->assertSee('href="/admin"', false)
        ->assertSee('hover:shadow-md', false)
        ->assertSee('card-body');

    $this
        ->blade('<x-lazy-dropdown end width="w-80" :content-defaults="false" content-class="z-50 border border-base-300">Menu</x-lazy-dropdown>')
        ->assertSee('dropdown-end')
        ->assertSee('w-80')
        ->assertSee('z-50')
        ->assertSee('border-base-300');
});

it('fully migrates admin dropdown consumers to Lazy UI', function (): void {
    $root = dirname(__DIR__, 2).'/resources/views';

    foreach ([
        'language-switcher.blade.php',
        'notifications/bell.blade.php',
    ] as $view) {
        $source = file_get_contents($root.'/'.$view);

        expect($source)
            ->toContain('<x-lazy-dropdown')
            ->not->toMatch('/(?<![:\\w-])class="[^"]*\\bdropdown(?:-|\\s)/');
    }

    $dashboard = file_get_contents($root.'/dashboard/index.blade.php');

    expect($dashboard)
        ->toContain('<x-lazy-card')
        ->not->toContain('class="card-body');
});

it('keeps third-wave migrated views free of server-side Blade logic', function (): void {
    $root = dirname(__DIR__, 2).'/resources/views';

    foreach ([
        'header.blade.php',
        'menu-item.blade.php',
        'menu-label.blade.php',
        'access/index.blade.php',
        'breadcrumb-trail.blade.php',
        'pages/preview.blade.php',
        'pages/partials/form.blade.php',
        'users/partials/form.blade.php',
        'seo/redirects/index.blade.php',
    ] as $view) {
        $source = file_get_contents($root.'/'.$view);

        expect($source)
            ->not->toContain('@php')
            ->not->toMatch('/(?<![:\\w-])class="[^"]*\\bdropdown(?:-|\\s)/')
            ->not->toMatch('/(?<![:\\w-])class="[^"]*\\bcollapse(?:-|\\s)/')
            ->not->toMatch('/(?<![:\\w-])class="[^"]*\\bswap(?:-|\\s)/');
    }
});

it('renders PHP-backed menu components', function (): void {
    $this
        ->blade('<x-lazy-menu-item :item="[\'url\' => \'https://example.com/docs\', \'label\' => \'Docs\']" />')
        ->assertSee('https://example.com/docs', false)
        ->assertSee('Docs')
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);

    $this
        ->blade('<x-lazy-menu-label :item="[\'label\' => \'Inbox\', \'badge\' => 120]" />')
        ->assertSee('Inbox')
        ->assertSee('99+');
});

it('renders Lazy UI collapse and swap in migrated admin chrome', function (): void {
    $this
        ->blade('<x-lazy-collapse summary-class="font-semibold"><x-slot:summary>Catalog</x-slot:summary>Body</x-lazy-collapse>')
        ->assertSee('collapse-arrow')
        ->assertSee('Catalog')
        ->assertSee('Body');

    $this
        ->blade('<x-lazy-swap controlled rotate><x-slot:on>On</x-slot:on><x-slot:off>Off</x-slot:off></x-lazy-swap>')
        ->assertSee('swap-rotate')
        ->assertSee('swap-on')
        ->assertSee('swap-off')
        ->assertDontSee('type="checkbox"', false);
});

it('keeps admin chrome compatible with daisyUI 5 layout utilities', function (): void {
    $root = dirname(__DIR__, 2).'/resources/views';

    $header = file_get_contents($root.'/header.blade.php');
    $footer = file_get_contents($root.'/footer.blade.php');
    $layout = file_get_contents($root.'/layout.blade.php');

    expect($header)
        ->toContain('class="flex flex-auto items-center justify-start px-4"')
        ->toContain('class="flex flex-none items-center gap-2"')
        ->toContain(':placeholder-enabled="blank($avatarUrl)"')
        ->toContain('$avatarInitials');

    expect($footer)
        ->toContain('sm:footer-horizontal');

    expect($layout)
        ->toContain('sm:footer-horizontal');
});

it('does not depend on application-specific logo or avatar files', function (): void {
    $config = require dirname(__DIR__, 2).'/config/lazy/admin.php';
    $header = file_get_contents(dirname(__DIR__, 2).'/resources/views/header.blade.php');

    expect($config['logo'])->toBeNull()
        ->and($config['avatar'])->toBeNull()
        ->and($header)->toContain('@if($logoUrl)')
        ->and($header)->toContain('aria-label="{{ __(\'Lazy Admin\') }}"');
});

it('prefers the saved admin logo over config fallback', function (): void {
    $source = file_get_contents(dirname(__DIR__, 2).'/src/LazyAdminServiceProvider.php');

    expect($source)
        ->toContain("setting('admin.logo')")
        ->toContain("Storage::disk('public')->url($stored)")
        ->toContain("config('lazy.admin.logo')");
});
