<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Controllers\SettingController;

Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
