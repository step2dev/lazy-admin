<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Controllers\AccessController;
use Step2dev\LazyAdmin\Controllers\PermissionController;
use Step2dev\LazyAdmin\Controllers\RoleController;
use Step2dev\LazyAdmin\Controllers\SettingController;
use Step2dev\LazyAdmin\Controllers\UserController;

Route::resource('user', UserController::class);
Route::get('access', AccessController::class)->name('access.index');

Route::resource('role', RoleController::class)->only(['store', 'update', 'destroy']);
Route::resource('permission', PermissionController::class)->only(['store', 'update', 'destroy']);

Route::singletons([
    'setting' => SettingController::class,
], [
    'except' => [
        'show',
        'destroy',
    ],
]);
