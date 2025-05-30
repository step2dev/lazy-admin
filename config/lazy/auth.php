<?php

return [
    'login_fields' => [
        'email',
        //        'username',
        //        'phone'
    ],
    'providers' => [
        'users' => [
            'model' => \App\Models\User::class, // @phpstan-ignore-line
        ],
    ],
    'password_timeout' => 10800,
];
