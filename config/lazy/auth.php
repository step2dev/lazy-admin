<?php

return [
    'login_fields' => [
        'email',
        //        'username',
        //        'phone'
    ],
    'providers' => [
        'users' => [
            'model' => \App\Models\User::class,
        ],
    ],
    'password_timeout' => 10800,
];
