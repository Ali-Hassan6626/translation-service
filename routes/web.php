<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TranslationController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// API Routes (with sanctum)
Route::middleware([
    EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
])->prefix('api')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Translation Routes (public for now)
Route::get('/translations', [TranslationController::class, 'index']);
Route::post('/translations', [TranslationController::class, 'store']);
Route::put('/translations/{id}', [TranslationController::class, 'update']);
Route::get('/translations/export/{locale}', [TranslationController::class, 'export']);

// Catch-all for frontend (must come LAST)
// Route::view('/{any}', 'app')->where('any', '.*');
