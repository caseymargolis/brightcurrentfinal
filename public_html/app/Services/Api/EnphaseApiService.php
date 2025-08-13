<?php

namespace App\Services\Api;

use App\Models\System;
use App\Services\Api\EnphaseOAuthService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EnphaseApiService
{
    protected $apiKey;
    protected $oauthService;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('solar.enphase.api_key');
        $this->oauthService = new EnphaseOAuthService();
        $this->baseUrl = 'https://api.enphaseenergy.com/api/v4';
    }

    /**
     * Get production data for an Enphase system
     */
    public function getProductionData(System $system)
    {
        try {
            // Get system summary
            $summaryResponse = $this->makeRequest("/systems/{$system->external_system_id}/summary");
            
            if (!$summaryResponse->successful()) {
                throw new \Exception("Failed to fetch Enphase system summary");
            }

            $summaryData = $summaryResponse->json();

            // Get production stats
            $productionResponse = $this->makeRequest("/systems/{$system->external_system_id}/production_meter_readings", [
                'start_date' => now()->subDays(2)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
            ]);

            $productionData = $productionResponse->successful() ? $productionResponse->json() : [];

            return $this->formatProductionData($summaryData, $productionData, $system);

        } catch (\Exception $e) {
            Log::error("Enphase API Error for system {$system->system_id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Format Enphase data to our standard format
     */
    protected function formatProductionData(array $summary, array $production, System $system)
    {
        $energyToday = 0;
        $energyYesterday = 0;
        $powerCurrent = 0;

        // Extract today's and yesterday's energy from production data
        if (isset($production['meter_readings']) && !empty($production['meter_readings'])) {
            $readings = collect($production['meter_readings']);
            
            $todayReading = $readings->where('end_date', '>=', now()->startOfDay()->timestamp)->first();
            $yesterdayReading = $readings->where('end_date', '>=', now()->subDay()->startOfDay()->timestamp)
                                       ->where('end_date', '<', now()->startOfDay()->timestamp)->first();

            $energyToday = $todayReading['wh_delivered'] ?? 0;
            $energyYesterday = $yesterdayReading['wh_delivered'] ?? 0;
        }

        // Get current power from summary
        $powerCurrent = ($summary['current_power'] ?? 0) / 1000; // Convert W to kW

        // Calculate efficiency (this is a simplified calculation)
        $efficiency = $system->capacity > 0 ? min(($powerCurrent / $system->capacity) * 100, 100) : 0;

        return [
            'date' => now()->toDateTimeString(),
            'energy_today' => round($energyToday / 1000, 3), // Convert Wh to kWh
            'energy_yesterday' => round($energyYesterday / 1000, 3),
            'power_current' => round($powerCurrent, 3),
            'efficiency' => round($efficiency, 2),
            'energy_lifetime' => round(($summary['energy_lifetime'] ?? 0) / 1000000, 3), // Convert Wh to MWh
            'system_status' => $this->getSystemStatus($summary),
            'weather_temperature' => null, // Will be populated by weather service
            'weather_condition' => null,
        ];
    }

    /**
     * Determine system status from Enphase data
     */
    protected function getSystemStatus(array $summary)
    {
        $status = $summary['status'] ?? 'unknown';
        
        return match(strtolower($status)) {
            'normal' => 'active',
            'comm' => 'communication_error',
            'power' => 'power_issue',
            default => 'inactive'
        };
    }

    /**
     * Make authenticated request to Enphase API
     */
    protected function makeRequest(string $endpoint, array $params = [])
    {
        $accessToken = $this->oauthService->getValidAccessToken();
        
        if (!$accessToken) {
            throw new \Exception("No valid Enphase access token available");
        }

        return Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
                'key' => $this->apiKey, // Enphase also requires the API key
            ])
            ->get($this->baseUrl . $endpoint, $params);
    }

    /**
     * Get all systems for the authenticated user
     */
    public function getSystems()
    {
        try {
            $response = $this->makeRequest('/systems');
            
            if ($response->successful()) {
                return $response->json()['systems'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch Enphase systems: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Test connection to Enphase API
     */
    public function testConnection()
    {
        try {
            if (!$this->oauthService->hasValidCredentials()) {
                return [
                    'success' => false,
                    'message' => 'Enphase OAuth credentials not configured. Please add valid ENPHASE_CLIENT_ID and ENPHASE_CLIENT_SECRET to your .env file.',
                    'guidance' => 'To get Enphase credentials: 1) Register at developer-v4.enphase.com 2) Create a Partner application 3) Get Client ID, Client Secret, and API Key'
                ];
            }

            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Enphase API key not configured. Please add valid ENPHASE_API_KEY to your .env file.',
                    'guidance' => 'The API key is provided along with your OAuth credentials in the Enphase developer portal.'
                ];
            }

            // Check if we have a valid access token
            $accessToken = $this->oauthService->getValidAccessToken();
            
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'No valid Enphase access token available. OAuth authentication required.',
                    'details' => [
                        'credentials_configured' => true,
                        'access_token_cached' => false,
                        'refresh_token_available' => !empty(\Illuminate\Support\Facades\Cache::get('enphase_refresh_token'))
                    ],
                    'guidance' => 'Run the OAuth authentication process: php artisan enphase:authenticate --username=your_email --password=your_password'
                ];
            }

            // Test API connection with systems endpoint
            $response = $this->makeRequest('/systems');
            
            if ($response->successful()) {
                $data = $response->json();
                $systemCount = count($data['systems'] ?? []);
                
                return [
                    'success' => true,
                    'message' => "Successfully connected to Enphase API. Found {$systemCount} accessible systems.",
                    'details' => [
                        'system_count' => $systemCount,
                        'authenticated' => true,
                        'api_key_valid' => true,
                        'access_token_valid' => true
                    ]
                ];
            } else {
                $statusCode = $response->status();
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? $errorData['error'] ?? 'Authentication failed';
                
                $message = "Enphase API request failed (HTTP {$statusCode}): {$errorMessage}";
                
                if ($statusCode === 401) {
                    $message .= ". Access token may be expired or invalid.";
                    // Clear the token so it can be refreshed on next attempt
                    $this->oauthService->clearTokens();
                } elseif ($statusCode === 403) {
                    $message .= ". This usually means insufficient permissions or rate limit exceeded.";
                }
                
                return [
                    'success' => false,
                    'message' => $message,
                    'details' => [
                        'status_code' => $statusCode,
                        'error_response' => $errorData,
                        'authenticated' => $statusCode !== 401
                    ],
                    'guidance' => $statusCode === 401 ? 'Re-run OAuth authentication or check if your Enphase account credentials are correct.' : 'Check your API permissions and rate limits.'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Enphase API connection error: " . $e->getMessage(),
                'details' => [
                    'exception' => get_class($e),
                    'trace' => substr($e->getTraceAsString(), 0, 500)
                ]
            ];
        }
    }

    /**
     * Authenticate with Enphase API using username/password
     */
    public function authenticate($username, $password)
    {
        return $this->oauthService->getAccessToken($username, $password);
    }
}
