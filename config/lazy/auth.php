<?php

return [
    'guard' => env('LAZY_AUTH_GUARD', 'web'),

    'provider' => env('LAZY_AUTH_PROVIDER'),

    'login_fields' => [
        'email',
    ],

    'login' => [
        'enabled' => env('LAZY_AUTH_LOGIN_ENABLED', false),
        'redirect_route' => env('LAZY_AUTH_REDIRECT_ROUTE', 'admin.dashboard'),
    ],

    'password_timeout' => 10800,
];
