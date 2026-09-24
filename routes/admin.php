<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Controllers\SettingController;
use Step2dev\LazyAdmin\Controllers\UserController;

Route::resource('user', UserController::class);
Route::singletons([
    'setting' => SettingController::class,
], [
    'except' => [
        'show',
        'destroy',
    ],
]);
