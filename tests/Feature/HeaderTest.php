<?php

it('renders the cloned step2.dev admin header for guests', function (): void {
    $html = view('lazy::header')->render();

    expect($html)
        ->toContain('menu-toggle')
        ->toContain('placeholder="'.__('lazy-admin::search.short_placeholder').'"')
        ->toContain(config('lazy.admin.logo', '/main.svg'));
});
