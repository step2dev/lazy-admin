<?php

// config for Step2dev/LazyAdmin
use Step2dev\LazyAdmin\Middleware\LazyAdminMiddleware;

return [
    'home' => env('LAZY_ADMIN_HOME', '/'),
    'logo' => env('LAZY_ADMIN_LOGO', '/main.svg'),
    'avatar' => env('LAZY_ADMIN_AVATAR', '/img/admin.png'),

    'scripts' => [
        'resources/lazy/js/lazy.js',
    ],
    'styles' => [
        'resources/lazy/scss/lazy.scss',
    ],
    'route' => [
        'name' => env('LAZY_ROUTE_NAME', 'admin.'),
        'prefix' => env('LAZY_ROUTE_PREFIX', 'admin'),
        'domain' => env('LAZY_ROUTE_DOMAIN'),
        'middleware' => [
            'web',
            'auth:'.env('LAZY_AUTH_GUARD', 'web'),
            'verified',
            LazyAdminMiddleware::class,
        ],
        'path' => 'routes/admin.php',
        'login' => [
            'prefix' => env('LAZY_LOGIN_PREFIX', ''),
            'uri' => env('LAZY_LOGIN_URI', 'login'),
        ],
    ],

    'roles' => [
        'superadmin',
        'admin',
        'manager',
        'moderator',
    ],
];
