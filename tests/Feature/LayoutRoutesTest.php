<?php

declare(strict_types=1);

use Step2dev\LazyAdmin\Components\Layout;

it('merges custom layout route overrides with generated route keys', function (): void {
    $layout = new Layout([
        'show' => [
            'url' => 'https://example.com/article',
            'target' => '_blank',
        ],
    ]);

    expect($layout->routes)
        ->toHaveKeys(['index', 'create', 'show', 'edit', 'destroy'])
        ->and($layout->routes['show'])
        ->toBe([
            'url' => 'https://example.com/article',
            'target' => '_blank',
        ]);
});

it('declares routes as a blade prop so custom route attributes are not unset', function (): void {
    $view = file_get_contents(__DIR__.'/../../resources/views/layout.blade.php');

    expect($view)
        ->toBeString()
        ->toContain("'routes' => []");
});
