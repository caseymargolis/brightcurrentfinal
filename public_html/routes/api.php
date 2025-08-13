<?php

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

// OAuth callback routes
Route::get('/enphase/callback', function (Request $request) {
    $code = $request->get('code');
    $state = $request->get('state');
    
    if (!$code) {
        return response()->json(['error' => 'Authorization code not provided'], 400);
    }
    
    return response()->json([
        'message' => 'Enphase OAuth callback received successfully!',
        'code' => $code,
        'state' => $state,
        'instructions' => 'Copy the code above and use it in the authentication command.'
    ]);
})->name('enphase.callback');

Route::get('/tesla/callback', function (Request $request) {
    $code = $request->get('code');
    $state = $request->get('state');
    
    if (!$code) {
        return response()->json(['error' => 'Authorization code not provided'], 400);
    }
    
    return response()->json([
        'message' => 'Tesla OAuth callback received successfully!',
        'code' => $code,
        'state' => $state,
        'instructions' => 'Copy the code above and use it in the authentication command.'
    ]);
})->name('tesla.callback');
