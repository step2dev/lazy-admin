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

    'permissions' => [
        'enforce' => env('LAZY_ADMIN_ENFORCE_PERMISSIONS', true),
        'guard' => env('LAZY_ADMIN_PERMISSION_GUARD', env('LAZY_AUTH_GUARD', 'web')),
        'super_admin_role' => env('LAZY_ADMIN_SUPER_ADMIN_ROLE', 'superadmin'),
        'seed' => env('LAZY_ADMIN_SEED_PERMISSIONS', true),

        'defaults' => [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
        ],

        'role_permissions' => [
            'superadmin' => ['*'],
            'admin' => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'roles.view',
                'roles.create',
                'roles.edit',
                'permissions.view',
                'permissions.create',
                'permissions.edit',
            ],
            'manager' => [
                'admin_access',
                'users.view',
                'users.create',
                'users.edit',
                'roles.view',
                'permissions.view',
            ],
            'moderator' => [
                'admin_access',
                'users.view',
            ],
        ],
    ],
];
