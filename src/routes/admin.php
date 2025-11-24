<?php

use Cms\Http\Controllers\DashboardController;
use Cms\Http\Controllers\AuthController;
use Spark\Facades\Route;

Route::group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/*', [DashboardController::class, 'menu']);
})
    ->middleware('cms.auth');


Route::group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
})
    ->middleware('cms.guest');
