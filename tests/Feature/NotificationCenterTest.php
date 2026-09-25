<?php

declare(strict_types=1);

use Step2dev\LazyAdmin\Notifications\NotificationCenter;

it('falls back when a notification title is not a string', function (): void {
    $notification = (object) [
        'data' => [
            'title' => [
                'uk' => 'Повідомлення',
                'en' => 'Notification',
            ],
        ],
    ];

    expect((new NotificationCenter)->title($notification))->toBe('Notification');
});
