<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Api\TranslationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public routes
Route::post('/login', [ApiAuthController::class, 'login']);
Route::get('/translations/export', [TranslationController::class, 'export']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('translations/search', [TranslationController::class, 'search']);
    Route::apiResource('translations', TranslationController::class);
});
