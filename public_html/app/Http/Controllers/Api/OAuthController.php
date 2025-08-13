<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\EnphaseOAuthService;
use App\Services\Api\TeslaOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OAuthController extends Controller
{
    protected $enphaseOAuth;
    protected $teslaOAuth;

    public function __construct(EnphaseOAuthService $enphaseOAuth, TeslaOAuthService $teslaOAuth)
    {
        $this->enphaseOAuth = $enphaseOAuth;
        $this->teslaOAuth = $teslaOAuth;
    }

    /**
     * Handle Enphase OAuth callback
     */
    public function enphaseCallback(Request $request)
    {
        $code = $request->get('code');
        $state = $request->get('state');
        $error = $request->get('error');

        if ($error) {
            Log::error('Enphase OAuth error: ' . $error);
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

        try {
            // Exchange code for tokens
            $result = $this->enphaseOAuth->exchangeCodeForTokens($code);

            if ($result['success']) {
                Log::info('Enphase OAuth successful');
                return response()->json([
                    'success' => true,
                    'message' => 'Enphase authentication successful!',
                    'code' => $code,
                    'redirect_instructions' => 'You can now close this page. Your Enphase account has been successfully connected.'
                ]);
            } else {
                Log::error('Enphase token exchange failed: ' . $result['message']);
                return response()->json([
                    'success' => false,
                    'message' => 'Token exchange failed: ' . $result['message'],
                    'code_for_manual_entry' => $code
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Enphase OAuth callback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage(),
                'code_for_manual_entry' => $code
            ], 500);
        }
    }

    /**
     * Handle Tesla OAuth callback
     */
    public function teslaCallback(Request $request)
    {
        $code = $request->get('code');
        $state = $request->get('state');
        $error = $request->get('error');

        if ($error) {
            Log::error('Tesla OAuth error: ' . $error);
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

        try {
            // Exchange code for tokens
            $tokens = $this->teslaOAuth->exchangeCodeForTokens($code);

            Log::info('Tesla OAuth successful');
            return response()->json([
                'success' => true,
                'message' => 'Tesla authentication successful!',
                'code' => $code,
                'redirect_instructions' => 'You can now close this page. Your Tesla account has been successfully connected.'
            ]);
        } catch (\Exception $e) {
            Log::error('Tesla OAuth callback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage(),
                'code_for_manual_entry' => $code
            ], 500);
        }
    }

    /**
     * Get Enphase authorization URL
     */
    public function enphaseAuth(Request $request)
    {
        try {
            $authData = $this->enphaseOAuth->getAuthorizationUrl();
            return response()->json([
                'success' => true,
                'auth_url' => $authData['auth_url'],
                'state' => $authData['state']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate authorization URL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Tesla authorization URL
     */
    public function teslaAuth(Request $request)
    {
        try {
            $authData = $this->teslaOAuth->getAuthorizationUrl();
            return response()->json([
                'success' => true,
                'auth_url' => $authData['auth_url'],
                'state' => $authData['state']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate authorization URL: ' . $e->getMessage()
            ], 500);
        }
    }
}