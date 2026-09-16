<?php

use App\Models\User;

return [
    'login_fields' => [
        'email',
        //        'username',
        //        'phone'
    ],
    'providers' => [
        'users' => [
            'model' => User::class, // @phpstan-ignore-line
        ],
    ],
    'password_timeout' => 10800,
];
