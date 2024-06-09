<?php

// config for Step2dev/LazyAdmin
use Step2dev\LazyAdmin\Middleware\LazyAdminMiddleware;

return [
    'route' => [
        'name' => env('LAZY_ROUTE_NAME', 'admin.'),
        'prefix' => env('LAZY_ROUTE_PREFIX', 'admin'),
        'domain' => env('LAZY_ROUTE_DOMAIN'),
        'middleware' => [
            'web',
            'auth',
            'verified',
            LazyAdminMiddleware::class,
        ],
        'path' => 'routes/admin.php',
    ],

    'roles' => [
        'superadmin',
        'admin',
        'manager',
        'moderator',
    ],
];
