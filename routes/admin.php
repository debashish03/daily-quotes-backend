<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin API routes for your application.
|
*/

// Logout route (outside middleware so it can be accessed without auth)
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin/api')->middleware(['admin.api.auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.api.dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'index'])->name('admin.api.dashboard.data');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('admin.api.analytics');
    Route::get('/dashboard/user-analytics', [DashboardController::class, 'userAnalytics'])->name('admin.api.user.analytics');
    Route::get('/dashboard/quote-analytics', [DashboardController::class, 'quoteAnalytics'])->name('admin.api.quote.analytics');

    // Users management
    Route::get('/users', [DashboardController::class, 'users'])->name('admin.api.users');
    Route::get('/users/{id}', [DashboardController::class, 'userDetail'])->name('admin.api.users.detail');
    Route::put('/users/{id}', [DashboardController::class, 'updateUser'])->name('admin.api.users.update');
    Route::get('/users/export', [DashboardController::class, 'exportUsers'])->name('admin.api.users.export');

    // Quotes management
    Route::apiResource('quotes', QuoteController::class)->names([
        'index' => 'admin.api.quotes.index',
        'create' => 'admin.api.quotes.create',
        'store' => 'admin.api.quotes.store',
        'show' => 'admin.api.quotes.show',
        'edit' => 'admin.api.quotes.edit',
        'update' => 'admin.api.quotes.update',
        'destroy' => 'admin.api.quotes.destroy',
    ]);
    Route::get('/quotes/categories', [QuoteController::class, 'categories'])->name('admin.api.quotes.categories');
    Route::get('/quotes/statistics', [QuoteController::class, 'statistics'])->name('admin.api.quotes.statistics');

    // Categories management
    Route::apiResource('categories', CategoryController::class)->names([
        'index' => 'admin.api.categories.index',
        'create' => 'admin.api.categories.create',
        'store' => 'admin.api.categories.store',
        'show' => 'admin.api.categories.show',
        'edit' => 'admin.api.categories.edit',
        'update' => 'admin.api.categories.update',
        'destroy' => 'admin.api.categories.destroy',
    ]);
    Route::get('/categories/statistics', [CategoryController::class, 'statistics'])->name('admin.api.categories.statistics');
});
