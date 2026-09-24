<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Controllers\PermissionController;
use Step2dev\LazyAdmin\Controllers\RoleController;
use Step2dev\LazyAdmin\Controllers\SettingController;
use Step2dev\LazyAdmin\Controllers\UserController;

Route::resource('user', UserController::class);
Route::resource('role', RoleController::class)->except(['show']);
Route::resource('permission', PermissionController::class)->except(['show']);

Route::singletons([
    'setting' => SettingController::class,
], [
    'except' => [
        'show',
        'destroy',
    ],
]);
