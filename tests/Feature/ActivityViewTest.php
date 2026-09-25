<?php

declare(strict_types=1);

use Illuminate\Pagination\LengthAwarePaginator;

it('renders array activity values without throwing html escaping errors', function (): void {
    $activity = (object) [
        'id' => 1,
        'created_at' => now(),
        'causer' => (object) [
            'name' => ['uk' => 'Адмін', 'en' => 'Admin'],
            'email' => 'admin@example.com',
        ],
        'event' => ['name' => 'updated'],
        'description' => ['uk' => 'Оновлено', 'en' => 'Updated'],
        'properties' => collect([
            'ip' => ['127.0.0.1'],
            'old' => ['slug' => 'terms'],
            'new' => ['slug' => 'terms2'],
        ]),
    ];

    $activities = new LengthAwarePaginator([$activity], 1, 25, 1, [
        'path' => '/admin/activity',
    ]);

    $html = view('lazy::activity.index', compact('activities'))->render();

    expect($html)
        ->toContain('&quot;uk&quot;')
        ->toContain('terms2')
        ->toContain('updated');
});
