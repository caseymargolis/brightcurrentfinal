<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OAuthController;

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

// OAuth routes
Route::prefix('oauth')->group(function () {
    // Authorization URL endpoints
    Route::get('/enphase/auth', [OAuthController::class, 'enphaseAuth'])->name('oauth.enphase.auth');
    Route::get('/tesla/auth', [OAuthController::class, 'teslaAuth'])->name('oauth.tesla.auth');
    
    // OAuth callback endpoints
    Route::get('/enphase/callback', [OAuthController::class, 'enphaseCallback'])->name('oauth.enphase.callback');
    Route::get('/tesla/callback', [OAuthController::class, 'teslaCallback'])->name('oauth.tesla.callback');
});

// Legacy callback routes (for backward compatibility)
Route::get('/enphase/callback', [OAuthController::class, 'enphaseCallback'])->name('enphase.callback');
Route::get('/tesla/callback', [OAuthController::class, 'teslaCallback'])->name('tesla.callback');
