<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Controllers\SettingController;

Route::name('admin.')
    ->domain(config('lazy.admin.domain'))
    ->middleware(config('lazy.admin.middleware'))
    ->group(static function () {
        Route::prefix(config('lazy.admin.prefix'))->group(function () {
            Route::get('setting', SettingController::class)->name('setting.index');
        });
    });
