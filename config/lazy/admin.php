<?php

// config for Step2dev/LazyAdmin
use Step2dev\LazyAdmin\Middleware\LazyAdminMiddleware;

return [
    /*
     * This type of toggle will be used for the theme switcher.
     * Available options: multiple, single, toggle
     */
    'theme-toggle' => 'multiple',
    /*
     * This is the list of themes that will be available for the theme switcher.
     * You can add your own themes here.
     */
    'themes' => [
        'light',
        'dark',
        'acid',
        'aqua',
        'autumn',
        'black',
        'bumblebee',
        'business',
        'cmyk',
        'coffee',
        'corporate',
        'cupcake',
        'cyberpunk',
        'dracula',
        'emerald',
        'fantasy',
        'forest',
        'garden',
        'halloween',
        'lemonade',
        'lofi',
        'luxury',
        'night',
        'pastel',
        'retro',
        'synthwave',
        'valentine',
        'winter',
        'wireframe',
    ],

    /*
     * This is list to toggle themes.
     */
    'toggle_themes' => [
        'light',
        'dark',
    ],

    'route_settings' => [
        'name' => env('LAZY_ROUTE_NAME', 'admin.'),
        'prefix' => env('LAZY_ROUTE_PREFIX', 'admin'),
        'domain' => env('LAZY_ROUTE_DOMAIN'),
        'middleware' => [
            'web',
            'auth',
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
