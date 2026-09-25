<?php

use Illuminate\Support\Facades\Route;
use Step2dev\LazyAdmin\Controllers\UserController;
use Step2dev\LazyAdmin\Http\Livewire\Settings\Page as SettingsPage;

Route::resource('user', UserController::class);
Route::get('setting', SettingsPage::class)->name('setting.index');
