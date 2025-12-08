<?php

use Cms\Http\Controllers\DashboardController;
use Cms\Http\Controllers\AuthController;
use Spark\Facades\Route;

Route::group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
})
    ->middleware('cms.guest');


Route::group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings/{setting}', [DashboardController::class, 'settings'])->name('settings');
    Route::get('/*', [DashboardController::class, 'menu']);
})
    ->middleware('cms.auth');
