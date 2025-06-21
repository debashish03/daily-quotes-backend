<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserPreferenceController;
use App\Http\Controllers\Api\ShareController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::prefix('v1')->group(function () {
    // Authentication routes (public)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // Quote routes
    Route::get('/quotes/today', [QuoteController::class, 'today']);
    Route::get('/quotes/random', [QuoteController::class, 'random']);
    Route::get('/quotes/by-category', [QuoteController::class, 'byCategory']);
    Route::get('/quotes/{id}', [QuoteController::class, 'show']);
    Route::get('/categories', [QuoteController::class, 'categories']);

    // Category routes
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    // Share routes
    Route::post('/shares', [ShareController::class, 'store']);
    Route::get('/shares/statistics/{quoteId}', [ShareController::class, 'statistics']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication routes (protected)
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);

        // User preferences
        Route::get('/preferences', [UserPreferenceController::class, 'index']);
        Route::put('/preferences', [UserPreferenceController::class, 'update']);
        Route::post('/preferences/device-token', [UserPreferenceController::class, 'updateDeviceToken']);

        // User share history
        Route::get('/shares/history', [ShareController::class, 'history']);
    });
});
