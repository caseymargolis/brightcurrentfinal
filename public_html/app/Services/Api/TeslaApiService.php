<?php

namespace App\Services\Api;

use App\Models\System;
use App\Services\Api\TeslaOAuthService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TeslaApiService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;
    protected $oauthService;

    public function __construct()
    {
        $this->clientId = config('solar.tesla.client_id');
        $this->clientSecret = config('solar.tesla.client_secret');
        $this->baseUrl = 'https://fleet-api.prd.na.vn.cloud.tesla.com';
        $this->oauthService = new TeslaOAuthService();
    }

    /**
     * Get production data for a Tesla system (Powerwall/Solar)
     */
    public function getProductionData(System $system)
    {
        try {
            $accessToken = $this->oauthService->getValidAccessToken();
            
            if (!$accessToken) {
                throw new \Exception("No valid Tesla access token available. OAuth authentication required.");
            }

            // Get energy site status
            $statusResponse = $this->makeRequest("/api/1/energy_sites/{$system->external_system_id}/live_status", [], $accessToken);
            
            if (!$statusResponse->successful()) {
                throw new \Exception("Failed to fetch Tesla energy site status: " . $statusResponse->body());
            }

            $status = $statusResponse->json()['response'] ?? [];

            // Get energy history
            $historyResponse = $this->makeRequest("/api/1/energy_sites/{$system->external_system_id}/calendar_history", [
                'kind' => 'energy',
                'start_date' => now()->subDays(2)->format('Y-m-d\TH:i:s'),
                'end_date' => now()->format('Y-m-d\TH:i:s'),
            ], $accessToken);

            $history = $historyResponse->successful() ? $historyResponse->json() : [];

            return $this->formatProductionData($status, $history, $system);

        } catch (\Exception $e) {
            Log::error("Tesla API Error for system {$system->system_id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Format Tesla data to our standard format
     */
    protected function formatProductionData(array $status, array $history, System $system)
    {
        $energyToday = 0;
        $energyYesterday = 0;
        $powerCurrent = 0;

        // Extract current power
        $powerCurrent = ($status['solar_power'] ?? 0) / 1000; // Convert W to kW

        // Extract energy data from history
        if (isset($history['response']) && !empty($history['response'])) {
            $timeSeriesData = collect($history['response']);
            
            $todayData = $timeSeriesData->firstWhere('timestamp', '>=', now()->startOfDay()->timestamp);
            $yesterdayData = $timeSeriesData->firstWhere('timestamp', '>=', now()->subDay()->startOfDay()->timestamp);

            $energyToday = ($todayData['solar_energy_exported'] ?? 0) / 1000; // Convert Wh to kWh
            $energyYesterday = ($yesterdayData['solar_energy_exported'] ?? 0) / 1000;
        }

        // Calculate efficiency
        $efficiency = $system->capacity > 0 ? min(($powerCurrent / $system->capacity) * 100, 100) : 0;

        return [
            'date' => now()->toDateTimeString(),
            'energy_today' => round($energyToday, 3),
            'energy_yesterday' => round($energyYesterday, 3),
            'power_current' => round($powerCurrent, 3),
            'efficiency' => round($efficiency, 2),
            'energy_lifetime' => null, // Tesla doesn't provide lifetime in this endpoint
            'system_status' => $this->getSystemStatus($status),
            'weather_temperature' => null,
            'weather_condition' => null,
        ];
    }

    /**
     * Determine system status from Tesla data
     */
    protected function getSystemStatus(array $status)
    {
        // Tesla status logic - adapt based on actual API response structure
        $isOnline = $status['timestamp'] ?? 0;
        $recentUpdate = $isOnline > (time() - 3600); // Updated within last hour

        return $recentUpdate ? 'active' : 'inactive';
    }

    /**
     * Get access token for Tesla API using OAuth
     */
    protected function getAccessToken()
    {
        return $this->oauthService->getValidAccessToken();
    }

    /**
     * Make authenticated request to Tesla API
     */
    protected function makeRequest(string $endpoint, array $params = [], string $accessToken = null)
    {
        if (!$accessToken) {
            $accessToken = $this->getAccessToken();
        }

        if (!$accessToken) {
            throw new \Exception("No valid Tesla access token");
        }

        $url = $this->baseUrl . $endpoint;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])
            ->get($url);
    }

    /**
     * Get all energy sites for the authenticated user
     */
    public function getEnergySites()
    {
        try {
            if (!$this->accessToken) {
                return [];
            }

            $response = $this->makeRequest('/energy_sites');
            
            if ($response->successful()) {
                return $response->json()['response'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch Tesla energy sites: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Authenticate with Tesla API (OAuth flow)
     * This would need to be implemented based on Tesla's OAuth requirements
     */
    public function authenticate($email, $password)
    {
        // TODO: Implement Tesla OAuth authentication
        // This is a complex process that requires handling multiple steps
        throw new \Exception("Tesla authentication not yet implemented");
    }

    /**
     * Test connection to Tesla API
     */
    public function testConnection()
    {
        try {
            if (empty($this->clientId) || empty($this->clientSecret)) {
                return [
                    'success' => false,
                    'message' => 'Tesla API credentials not configured',
                    'details' => null
                ];
            }

            if (!$this->accessToken) {
                return [
                    'success' => false,
                    'message' => 'Tesla API access token not available. Authentication required.',
                    'details' => [
                        'client_id_configured' => !empty($this->clientId),
                        'client_secret_configured' => !empty($this->clientSecret),
                        'access_token_cached' => !empty(cache('tesla_access_token'))
                    ]
                ];
            }

            // Test with a simple energy sites request
            $response = $this->makeRequest('/energy_sites');
            
            if ($response->successful()) {
                $data = $response->json();
                $siteCount = count($data['response'] ?? []);
                
                return [
                    'success' => true,
                    'message' => "Successfully connected to Tesla API. Found {$siteCount} energy sites.",
                    'details' => [
                        'site_count' => $siteCount,
                        'response_time_ms' => $response->transferStats?->getTransferTime() * 1000,
                        'authenticated' => true
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Tesla API request failed: ' . $response->status() . ' ' . $response->reason(),
                    'details' => [
                        'status_code' => $response->status(),
                        'response_body' => $response->body(),
                        'authenticated' => true
                    ]
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Tesla API connection error: ' . $e->getMessage(),
                'details' => [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]
            ];
        }
    }
}
