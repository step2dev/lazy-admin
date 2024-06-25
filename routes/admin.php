<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Controllers\LoginController;
use Step2dev\LazyAdmin\Controllers\SettingController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::singletons([
    'setting' => SettingController::class,
], [
    'except' => [
        'show',
        'destroy',
    ],
]);
