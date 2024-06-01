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

    /*
     * This is the path where the admin panel will be accessible from.
     * Change this to whatever you want.
     */
    'prefix' => env('LAZY_PATH', 'admin'),

    /*
     * This is the domain where the admin panel will be accessible from.
     * Change this to whatever you want.
     */
    'domain' => env('LAZY_DOMAIN'),

    'route_path' => 'routes/admin.php',

    /*
     * This is the prefix for the admin panel's route names.
     * Change this to whatever you want.
     */
    'middleware' => [
        'web',
        'auth',
        LazyAdminMiddleware::class,
    ],

    'roles' => [
        'superadmin',
        'admin',
        'manager',
        'moderator',
    ],
];
