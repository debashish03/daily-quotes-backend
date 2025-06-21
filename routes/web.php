<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Admin authentication routes
Route::prefix('admin')->group(function () {
    // Login routes (public)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Protected admin web views
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/', fn() => view('admin.dashboard'))->name('admin.dashboard');
        Route::get('/users', fn() => view('admin.users'))->name('admin.users.view');
        Route::get('/quotes', fn() => view('admin.quotes'))->name('admin.quotes.view');
        Route::get('/categories', fn() => view('admin.categories'))->name('admin.categories.view');
        Route::get('/analytics', fn() => view('admin.analytics'))->name('admin.analytics.view');
    });
});

// Include admin API routes
require __DIR__ . '/admin.php';
