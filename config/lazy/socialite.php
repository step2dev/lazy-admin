<?php

return [
    'enable' => env('SOCIAL_LOGIN', false),
    'services' => [
        'facebook' => [
            'enable' => env('FACEBOOK_CLIENT_ID', false),
        ],
        'google' => [
            'enable' => env('GOOGLE_CLIENT_ID', false),
        ],
        'twitter' => [
            'enable' => env('TWITTER_CLIENT_ID', false),
        ],
        'github' => [
            'enable' => env('GITHUB_CLIENT_ID', false),
        ],
        'linkedin' => [
            'enable' => env('LINKEDIN_CLIENT_ID', false),
        ],
        'apple' => [
            'enable' => env('APPLE_CLIENT_ID', false),
        ],
    ],
];
