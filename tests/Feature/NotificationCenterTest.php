<?php

declare(strict_types=1);

use Step2dev\LazyAdmin\Notifications\NotificationCenter;

it('uses the current locale for localized notification titles', function (): void {
    app()->setLocale('uk');

    $notification = (object) [
        'data' => [
            'title' => [
                'uk' => 'Повідомлення',
                'en' => 'Notification',
            ],
        ],
    ];

    expect((new NotificationCenter)->title($notification))->toBe('Повідомлення');
});

it('uses a human readable notification type when a title is missing', function (): void {
    $notification = (object) [
        'type' => 'App\\Notifications\\NewCommentNotification',
        'data' => [
            'message' => 'У вас новий коментар',
        ],
    ];

    $center = new NotificationCenter;

    expect($center->title($notification))
        ->toBe('New Comment Notification')
        ->and($center->message($notification))
        ->toBe('У вас новий коментар');
});

it('supports localized notification messages', function (): void {
    app()->setLocale('uk');

    $notification = (object) [
        'data' => [
            'message' => [
                'uk' => 'У вас новий коментар',
                'en' => 'You have a new comment',
            ],
        ],
    ];

    expect((new NotificationCenter)->message($notification))->toBe('У вас новий коментар');
});
