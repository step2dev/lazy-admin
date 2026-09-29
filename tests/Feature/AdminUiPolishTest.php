<?php

declare(strict_types=1);

it('keeps polished admin surfaces on existing Lazy UI components', function (): void {
    $root = dirname(__DIR__, 2).'/resources/views';

    $users = file_get_contents($root.'/pages/users/table.blade.php');
    $dashboard = file_get_contents($root.'/dashboard/index.blade.php');
    $settings = file_get_contents($root.'/settings/index.blade.php');

    expect($users)
        ->toContain('<x-lazy-table')
        ->toContain('<x-lazy-avatar')
        ->not->toContain('<table class="table');

    expect($dashboard)
        ->toContain('<x-lazy-card')
        ->toContain('group-hover:bg-primary');

    expect($settings)
        ->toContain('<x-lazy-card')
        ->toContain('@livewire($activeSection[\'component\']');
});

it('adds keyboard access to global search without changing the search registry', function (): void {
    $search = file_get_contents(dirname(__DIR__, 2).'/resources/views/search/header.blade.php');

    expect($search)
        ->toContain('@keydown.meta.k.window.prevent="focusSearch()"')
        ->toContain('@keydown.ctrl.k.window.prevent="focusSearch()"')
        ->toContain('@keydown.escape.window')
        ->toContain("groupBy('provider')")
        ->toContain('<x-lazy-kbd');
});

it('keeps the admin header sticky while preserving the existing sidebar toggle contract', function (): void {
    $header = file_get_contents(dirname(__DIR__, 2).'/resources/views/header.blade.php');

    expect($header)
        ->toContain('sticky top-0 z-40')
        ->toContain('lazy-sidebar-toggle')
        ->toContain('sidebarCompact')
        ->toContain('<livewire:lazy-admin.header-search />');
});
