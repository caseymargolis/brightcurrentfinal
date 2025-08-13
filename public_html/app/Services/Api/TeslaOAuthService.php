<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TeslaOAuthService
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('solar.tesla.client_id');
        $this->clientSecret = config('solar.tesla.client_secret');
        $this->redirectUri = config('solar.tesla.redirect_uri', 'http://localhost:8001/api/tesla/callback');
        $this->baseUrl = 'https://auth.tesla.com';
    }

    /**
     * Generate OAuth authorization URL
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
            'scope' => 'openid offline_access energy_device_data',
            'state' => $state,
            'audience' => 'https://fleet-api.prd.na.vn.cloud.tesla.com'
        ];

        $authUrl = $this->baseUrl . '/oauth2/v3/authorize?' . http_build_query($params);
        
        return [
            'auth_url' => $authUrl,
            'state' => $state
        ];
    }

    /**
     * Exchange authorization code for tokens
     */
    public function exchangeCodeForTokens($code)
    {
        try {
            $response = Http::post('https://fleet-auth.prd.vn.cloud.tesla.com/oauth2/v3/token', [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'audience' => 'https://fleet-api.prd.na.vn.cloud.tesla.com'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $tokens = [
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'expires_at' => now()->addSeconds($data['expires_in'])->timestamp,
                    'token_type' => $data['token_type'] ?? 'Bearer'
                ];

                // Cache the tokens
                Cache::put('tesla_access_token', $tokens['access_token'], now()->addSeconds($data['expires_in'] - 300)); // 5 min buffer
                Cache::put('tesla_refresh_token', $tokens['refresh_token'], now()->addDays(365)); // Long-lived
                Cache::put('tesla_token_expires_at', $tokens['expires_at'], now()->addDays(365));

                return $tokens;
            } else {
                throw new \Exception('Token exchange failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Tesla OAuth token exchange failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshAccessToken($refreshToken = null)
    {
        try {
            if (!$refreshToken) {
                $refreshToken = Cache::get('tesla_refresh_token');
            }

            if (!$refreshToken) {
                throw new \Exception('No refresh token available');
            }

            $response = Http::post('https://fleet-auth.prd.vn.cloud.tesla.com/oauth2/v3/token', [
                'grant_type' => 'refresh_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $refreshToken,
                'audience' => 'https://fleet-api.prd.na.vn.cloud.tesla.com'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $tokens = [
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? $refreshToken, // Some implementations don't return new refresh token
                    'expires_at' => now()->addSeconds($data['expires_in'])->timestamp,
                    'token_type' => $data['token_type'] ?? 'Bearer'
                ];

                // Update cached tokens
                Cache::put('tesla_access_token', $tokens['access_token'], now()->addSeconds($data['expires_in'] - 300));
                Cache::put('tesla_refresh_token', $tokens['refresh_token'], now()->addDays(365));
                Cache::put('tesla_token_expires_at', $tokens['expires_at'], now()->addDays(365));

                return $tokens;
            } else {
                throw new \Exception('Token refresh failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Tesla OAuth token refresh failed: ' . $e->getMessage());
            // Clear invalid tokens
            $this->clearTokens();
            throw $e;
        }
    }

    /**
     * Get a valid access token (refresh if necessary)
     */
    public function getValidAccessToken()
    {
        $accessToken = Cache::get('tesla_access_token');
        $expiresAt = Cache::get('tesla_token_expires_at');

        // Check if token is expired or will expire in next 5 minutes
        if (!$accessToken || !$expiresAt || $expiresAt <= (time() + 300)) {
            try {
                $tokens = $this->refreshAccessToken();
                return $tokens['access_token'];
            } catch (\Exception $e) {
                Log::warning('Tesla token refresh failed: ' . $e->getMessage());
                return null;
            }
        }

        return $accessToken;
    }

    /**
     * Check if we have valid OAuth credentials configured
     */
    public function hasValidCredentials()
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Clear cached tokens
     */
    public function clearTokens()
    {
        Cache::forget('tesla_access_token');
        Cache::forget('tesla_refresh_token');
        Cache::forget('tesla_token_expires_at');
    }

    /**
     * Get authorization status
     */
    public function getAuthorizationStatus()
    {
        return [
            'credentials_configured' => $this->hasValidCredentials(),
            'access_token_cached' => !empty(Cache::get('tesla_access_token')),
            'refresh_token_cached' => !empty(Cache::get('tesla_refresh_token')),
            'token_expires_at' => Cache::get('tesla_token_expires_at'),
            'token_valid' => !empty($this->getValidAccessToken())
        ];
    }
}