<?php

return [
    'enable' => env('SOCIAL_LOGIN', false),
    'services' => [
        'facebook' => [
            'enable' => env('FACEBOOK_CLIENT_ID', false),
            'client_id' => env('FACEBOOK_CLIENT_ID'),
            'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
            'redirect' => env('FACEBOOK_REDIRECT'),
        ],
        'google' => [
            'enable' => env('GOOGLE_CLIENT_ID', false),
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect' => env('GOOGLE_REDIRECT'),
        ],
        'twitter' => [
            'enable' => env('TWITTER_CLIENT_ID', false),
            'client_id' => env('TWITTER_CLIENT_ID'),
            'client_secret' => env('TWITTER_CLIENT_SECRET'),
            'redirect' => env('TWITTER_REDIRECT'),
        ],
        'github' => [
            'enable' => env('GITHUB_CLIENT_ID', false),
            'client_id' => env('GITHUB_CLIENT_ID'),
            'client_secret' => env('GITHUB_CLIENT_SECRET'),
            'redirect' => env('GITHUB_REDIRECT'),
        ],
        'linkedin' => [
            'enable' => env('LINKEDIN_CLIENT_ID', false),
            'client_id' => env('LINKEDIN_CLIENT_ID'),
            'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
            'redirect' => env('LINKEDIN_REDIRECT'),
        ],
        'apple' => [
            'enable' => env('APPLE_CLIENT_ID', false),
            'client_id' => env('APPLE_CLIENT_ID'),
            'client_secret' => env('APPLE_CLIENT_SECRET'),
            'redirect' => env('APPLE_REDIRECT'),
        ],
    ]
];
