<?php

use Cms\Http\Controllers\DashboardController;
use Spark\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
