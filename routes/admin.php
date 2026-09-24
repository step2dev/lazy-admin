<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Controllers\SettingController;

Route::view('user', 'lazy::users.index')->name('user.index');

Route::singletons([
    'setting' => SettingController::class,
], [
    'except' => [
        'show',
        'destroy',
    ],
]);
