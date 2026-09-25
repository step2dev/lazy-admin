<?php

declare(strict_types=1);

use Step2dev\LazyAdmin\Notifications\NotificationCenter;

it('renders notification page actions without leaking loop variables into child components', function (): void {
    $notification = (object) [
        'id' => 'notification-1',
        'data' => [
            'title' => 'Test notification',
            'message' => 'Test message',
        ],
        'read_at' => null,
        'created_at' => now(),
    ];

    $html = view('lazy::notifications.index', [
        'notifications' => collect([$notification]),
        'notificationCenter' => new NotificationCenter,
    ])->render();

    expect($html)
        ->toContain('markRead')
        ->toContain('notification-1')
        ->toContain('Test notification');
});
