<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Controllers\SettingController;

Route::singletons([
    'setting' => SettingController::class,
], [
    'except' => [
        'show',
        'destroy',
    ],
]);
