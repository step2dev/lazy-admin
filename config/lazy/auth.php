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

    'branding' => [
        'title' => env('LAZY_AUTH_TITLE', env('APP_NAME', 'Laravel')),
        'subtitle' => env('LAZY_AUTH_SUBTITLE', 'Sign in to access your account'),
        'logo' => env('LAZY_AUTH_LOGO'),
        'background' => env('LAZY_AUTH_BACKGROUND'),
    ],

    'ui' => [
        'theme_switcher' => env('LAZY_AUTH_THEME_SWITCHER', true),
        'back_button' => env('LAZY_AUTH_BACK_BUTTON', true),
        'register_link' => env('LAZY_AUTH_REGISTER_LINK', true),
        'forgot_password_link' => env('LAZY_AUTH_FORGOT_PASSWORD_LINK', true),
    ],

    'password_timeout' => 10800,
];
