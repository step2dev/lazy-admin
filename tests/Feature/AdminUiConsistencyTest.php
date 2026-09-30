<?php

declare(strict_types=1);

it('keeps the second admin polish wave on Lazy UI primitives', function (): void {
    $root = dirname(__DIR__, 2).'/resources/views';

    $activity = file_get_contents($root.'/activity/index.blade.php');
    $access = file_get_contents($root.'/access/index.blade.php');
    $search = file_get_contents($root.'/search/index.blade.php');
    $notifications = file_get_contents($root.'/notifications/index.blade.php');
    $bell = file_get_contents($root.'/notifications/bell.blade.php');
    $pages = file_get_contents($root.'/pages/index.blade.php');

    expect($activity)
        ->toContain('<x-lazy-card')
        ->toContain('<x-lazy-table')
        ->toContain('<x-lazy-collapse');

    expect($access)
        ->toContain('<x-lazy-fieldset')
        ->toContain('<x-lazy-card')
        ->not->toContain('class="form-control');

    expect($search)
        ->toContain("groupBy('provider')")
        ->toContain('<x-lazy-card');

    expect($notifications)
        ->toContain('<x-lazy-card')
        ->toContain('<x-lazy-badge');

    expect($bell)
        ->toContain('<x-lazy-indicator')
        ->not->toContain('class="indicator');

    expect($pages)
        ->toContain('<x-lazy-table')
        ->not->toContain('<table class="table');
});

it('keeps sidebar behavior intact while polishing layout', function (): void {
    $root = dirname(__DIR__, 2).'/resources/views';

    $layout = file_get_contents($root.'/layout.blade.php');
    $menu = file_get_contents($root.'/menu-generator.blade.php');
    $item = file_get_contents($root.'/menu-item.blade.php');

    expect($layout)
        ->toContain("localStorage.getItem('lazy-admin-sidebar-compact')")
        ->toContain('@lazy-sidebar-toggle.window="toggleSidebar()"')
        ->toContain("sidebarCompact ? 'md:w-20' : 'md:w-64'")
        ->toContain('md:sticky md:top-16');

    expect($menu)
        ->toContain("sidebarCompact ? 'menu-compact' : ''")
        ->toContain('<x-lazy-menu-item');

    expect($item)
        ->toContain('toggleSidebar()')
        ->toContain('x-data="{ open:');
});

it('removes remaining legacy manual controls from migrated views', function (): void {
    $root = dirname(__DIR__, 2).'/resources/views';

    $login = file_get_contents($root.'/auth/login.blade.php');
    $settings = file_get_contents($root.'/pages/settings/settings.blade.php');

    expect($login)
        ->toContain('<x-lazy-checkbox')
        ->toContain('<x-lazy-btn primary block')
        ->toContain("Route::has('auth.social.login')")
        ->not->toContain('form-check-input')
        ->not->toContain('input-group');

    $authLayout = file_get_contents($root.'/auth/layout.blade.php');

    expect($authLayout)
        ->toContain('lg:w-2/3')
        ->toContain('lg:w-1/3')
        ->toContain('<x-lazy-language-switcher')
        ->toContain("__('Back')")
        ->toContain("config('lazy.auth.ui.back_button', true)")
        ->not->toContain('rounded-2xl border border-base-300 bg-base-100 p-6');

    expect($settings)
        ->not->toContain('class="input-bordered"')
        ->not->toContain('class="textarea textarea-bordered')
        ->not->toContain('class="file-input file-input-bordered');
});

it('exposes activity date filters and reset without changing the activity backend contract', function (): void {
    $source = file_get_contents(dirname(__DIR__, 2).'/src/Http/Livewire/Activity/Page.php');

    expect($source)
        ->toContain('public string $dateFrom')
        ->toContain('public string $dateUntil')
        ->toContain('public function resetFilters(): void')
        ->toContain("whereDate('created_at', '>=', \$dateFrom)")
        ->toContain("whereDate('created_at', '<=', \$dateUntil)");
});
