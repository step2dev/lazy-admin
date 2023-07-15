<?php

use Illuminate\Support\Facades\Route;

Route::name('admin.')
    ->domain(config('lazy.admin.domain'))
    ->middleware(config('lazy.admin.middleware'))
    ->group(static function () {
        Route::prefix(config('lazy.admin.prefix'))->group(function () {
            Route::get('test', function () {
                return 'Hello World!';
            })->name('www');
        });
    });
