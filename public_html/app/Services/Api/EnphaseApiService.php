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
        $params['key'] = $this->apiKey;

        return Http::timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
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
            if (empty($this->apiKey) || $this->apiKey === 'demo_key_67890') {
                return [
                    'success' => false,
                    'message' => 'Enphase API key not configured. Please add valid ENPHASE_API_KEY, ENPHASE_CLIENT_ID, and ENPHASE_CLIENT_SECRET to your .env file.'
                ];
            }

            // For Enphase, we need OAuth authentication, not just an API key
            // This is a simplified test - in production, you'd need proper OAuth flow
            $response = $this->makeRequest('/systems');
            
            if ($response->successful()) {
                $data = $response->json();
                $systemCount = is_array($data) ? count($data) : 0;
                return [
                    'success' => true,
                    'message' => "Successfully connected to Enphase API. Found {$systemCount} accessible systems."
                ];
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Authentication failed';
                return [
                    'success' => false,
                    'message' => "Enphase API authentication failed: {$errorMessage}. Enphase requires OAuth 2.0 authentication. Please ensure your credentials are properly configured and authorized."
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Enphase API connection error: " . $e->getMessage()
            ];
        }
    }
}
