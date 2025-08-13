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

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API working!']);
});

// OAuth callback routes (simple closures for now)
Route::get('/enphase/callback', function (Request $request) {
    $code = $request->get('code');
    $state = $request->get('state');
    $error = $request->get('error');
    
    if ($error) {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => 'Authentication failed. Please try again.'
        ], 400);
    }
    
    if (!$code) {
        return response()->json([
            'success' => false,
            'error' => 'missing_code',
            'message' => 'Authorization code not provided'
        ], 400);
    }
    
    // Try to exchange code for tokens
    try {
        $oauthService = new \App\Services\Api\EnphaseOAuthService();
        $result = $oauthService->exchangeCodeForTokens($code);
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => '🎉 Enphase authentication successful! Your account has been connected.',
                'code' => $code,
                'instructions' => 'You can now close this page and test your solar data.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Token exchange failed: ' . $result['message'],
                'code_received' => $code,
                'instructions' => 'You can use this code manually with the artisan command.'
            ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Authentication failed: ' . $e->getMessage(),
            'code_received' => $code,
            'instructions' => 'You can use this code manually with the artisan command.'
        ], 500);
    }
})->name('enphase.callback');

Route::get('/tesla/callback', function (Request $request) {
    $code = $request->get('code');
    $state = $request->get('state');
    $error = $request->get('error');
    
    if ($error) {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => 'Authentication failed. Please try again.'
        ], 400);
    }
    
    if (!$code) {
        return response()->json([
            'success' => false,
            'error' => 'missing_code',
            'message' => 'Authorization code not provided'
        ], 400);
    }
    
    // Try to exchange code for tokens
    try {
        $oauthService = new \App\Services\Api\TeslaOAuthService();
        $tokens = $oauthService->exchangeCodeForTokens($code);
        
        return response()->json([
            'success' => true,
            'message' => '🎉 Tesla authentication successful! Your account has been connected.',
            'code' => $code,
            'instructions' => 'You can now close this page and test your solar data.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Authentication failed: ' . $e->getMessage(),
            'code_received' => $code,
            'instructions' => 'You can use this code manually with the artisan command.'
        ], 500);
    }
})->name('tesla.callback');
