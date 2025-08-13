<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EnphaseOAuthService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;
    protected $redirectUri;

    public function __construct()
    {
        $this->clientId = config('solar.enphase.client_id');
        $this->clientSecret = config('solar.enphase.client_secret');
        $this->redirectUri = config('solar.enphase.redirect_uri', 'http://localhost:8001/api/enphase/callback');
        $this->baseUrl = 'https://api.enphaseenergy.com';
    }

    /**
     * Generate OAuth authorization URL for Authorization Code Grant
     */
    public function getAuthorizationUrl($state = null)
    {
        if (!$state) {
            $state = bin2hex(random_bytes(16));
        }

        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'read_production',
            'state' => $state
        ];

        $authUrl = 'https://auth.enphase.com/oauth2/authorize?' . http_build_query($params);
        
        return [
            'auth_url' => $authUrl,
            'state' => $state
        ];
    }

    /**
     * Exchange authorization code for tokens (Authorization Code Grant)
     */
    public function exchangeCodeForTokens($code)
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->asForm()
                ->post($this->baseUrl . '/oauth/token', [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Cache the tokens
                $expiresIn = $data['expires_in'] ?? 86400; // Default 24 hours
                Cache::put('enphase_access_token', $data['access_token'], now()->addSeconds($expiresIn - 300)); // 5 min buffer
                Cache::put('enphase_refresh_token', $data['refresh_token'] ?? null, now()->addDays(30));
                Cache::put('enphase_token_expires_at', now()->addSeconds($expiresIn)->timestamp, now()->addDays(30));
                
                return [
                    'success' => true,
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? null,
                    'expires_in' => $expiresIn,
                    'token_type' => $data['token_type'] ?? 'bearer'
                ];
            } else {
                $error = $response->json();
                return [
                    'success' => false,
                    'message' => $error['error_description'] ?? $error['error'] ?? 'Token exchange failed',
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Enphase OAuth token exchange error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Token exchange failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get access token using password grant (DEPRECATED - kept for backward compatibility)
     * Note: Enphase API v4 no longer supports password grant type
     */
    public function getAccessToken($username, $password)
    {
        Log::warning('Enphase password grant is deprecated. Use Authorization Code Grant instead.');
        
        return [
            'success' => false,
            'message' => 'Password grant type is no longer supported by Enphase API v4. Please use Authorization Code Grant flow.',
            'deprecated' => true,
            'solution' => 'Use the authorization code flow by calling getAuthorizationUrl() and then exchangeCodeForTokens()'
        ];
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshAccessToken()
    {
        try {
            $refreshToken = Cache::get('enphase_refresh_token');
            
            if (!$refreshToken) {
                return [
                    'success' => false,
                    'message' => 'No refresh token available'
                ];
            }

            $credentials = base64_encode($this->clientId . ':' . $this->clientSecret);
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->asForm() // This ensures form encoding
                ->post($this->baseUrl . '/oauth/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Cache the new access token
                $expiresIn = $data['expires_in'] ?? 86400;
                Cache::put('enphase_access_token', $data['access_token'], now()->addSeconds($expiresIn - 300));
                
                if (isset($data['refresh_token'])) {
                    Cache::put('enphase_refresh_token', $data['refresh_token'], now()->addDays(30));
                }
                
                return [
                    'success' => true,
                    'access_token' => $data['access_token'],
                    'expires_in' => $expiresIn
                ];
            } else {
                // If refresh fails, clear the cached tokens
                Cache::forget('enphase_access_token');
                Cache::forget('enphase_refresh_token');
                
                return [
                    'success' => false,
                    'message' => 'Token refresh failed',
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Enphase token refresh error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Token refresh failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get valid access token (with automatic refresh if needed)
     */
    public function getValidAccessToken()
    {
        $accessToken = Cache::get('enphase_access_token');
        
        if ($accessToken) {
            return $accessToken;
        }

        // Try to refresh token
        $refreshResult = $this->refreshAccessToken();
        
        if ($refreshResult['success']) {
            return $refreshResult['access_token'];
        }

        return null;
    }

    /**
     * Clear all cached tokens (for logout/reset)
     */
    public function clearTokens()
    {
        Cache::forget('enphase_access_token');
        Cache::forget('enphase_refresh_token');
    }

    /**
     * Check if we have valid credentials configured
     */
    public function hasValidCredentials()
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }
}