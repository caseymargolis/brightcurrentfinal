<?php

namespace App\Services\Api;

use App\Models\System;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SolarEdgeApiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('solar.solaredge.api_key');
        $this->baseUrl = 'https://monitoringapi.solaredge.com';
    }

    /**
     * Get production data for a SolarEdge system
     */
    public function getProductionData(System $system)
    {
        try {
            // Get site overview
            $overviewResponse = $this->makeRequest("/site/{$system->external_system_id}/overview");
            
            if (!$overviewResponse->successful()) {
                throw new \Exception("Failed to fetch SolarEdge site overview");
            }

            $overview = $overviewResponse->json()['overview'] ?? [];

            // Get energy details for today and yesterday
            $energyResponse = $this->makeRequest("/site/{$system->external_system_id}/energy", [
                'startDate' => now()->subDays(2)->format('Y-m-d'),
                'endDate' => now()->format('Y-m-d'),
                'timeUnit' => 'DAY',
            ]);

            $energyData = $energyResponse->successful() ? $energyResponse->json() : [];

            // Get current power
            $powerResponse = $this->makeRequest("/site/{$system->external_system_id}/currentPowerFlow");
            $powerData = $powerResponse->successful() ? $powerResponse->json() : [];

            return $this->formatProductionData($overview, $energyData, $powerData, $system);

        } catch (\Exception $e) {
            Log::error("SolarEdge API Error for system {$system->system_id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Format SolarEdge data to our standard format
     */
    protected function formatProductionData(array $overview, array $energy, array $power, System $system)
    {
        $energyToday = 0;
        $energyYesterday = 0;
        $powerCurrent = 0;

        // Extract energy data
        if (isset($energy['energy']['values']) && !empty($energy['energy']['values'])) {
            $values = collect($energy['energy']['values']);
            
            $todayValue = $values->firstWhere('date', now()->format('Y-m-d T H:i:s'));
            $yesterdayValue = $values->firstWhere('date', now()->subDay()->format('Y-m-d T H:i:s'));

            $energyToday = $todayValue['value'] ?? 0;
            $energyYesterday = $yesterdayValue['value'] ?? 0;
        }

        // Get current power from power flow
        if (isset($power['siteCurrentPowerFlow'])) {
            $powerFlow = $power['siteCurrentPowerFlow'];
            $powerCurrent = ($powerFlow['PV']['currentPower'] ?? 0) / 1000; // Convert W to kW
        }

        // Calculate efficiency
        $efficiency = $system->capacity > 0 ? min(($powerCurrent / $system->capacity) * 100, 100) : 0;

        return [
            'date' => now()->toDateTimeString(),
            'energy_today' => round($energyToday / 1000, 3), // Convert Wh to kWh
            'energy_yesterday' => round($energyYesterday / 1000, 3),
            'power_current' => round($powerCurrent, 3),
            'efficiency' => round($efficiency, 2),
            'energy_lifetime' => round(($overview['lifeTimeData']['energy'] ?? 0) / 1000000, 3), // Convert Wh to MWh
            'system_status' => $this->getSystemStatus($overview),
            'weather_temperature' => null,
            'weather_condition' => null,
        ];
    }

    /**
     * Determine system status from SolarEdge data
     */
    protected function getSystemStatus(array $overview)
    {
        $status = $overview['status'] ?? 'Unknown';
        
        return match(strtolower($status)) {
            'active' => 'active',
            'pending' => 'pending',
            'disabled' => 'inactive',
            'maintenance' => 'maintenance',
            default => 'inactive'
        };
    }

    /**
     * Make authenticated request to SolarEdge API
     */
    protected function makeRequest(string $endpoint, array $params = [])
    {
        $params['api_key'] = $this->apiKey;

        return Http::timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get($this->baseUrl . $endpoint, $params);
    }

    /**
     * Get all sites for the authenticated user
     */
    public function getSites()
    {
        try {
            $response = $this->makeRequest('/sites/list');
            
            if ($response->successful()) {
                return $response->json()['sites']['site'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch SolarEdge sites: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get site details
     */
    public function getSiteDetails($siteId)
    {
        try {
            $response = $this->makeRequest("/site/{$siteId}/details");
            
            if ($response->successful()) {
                return $response->json()['details'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch SolarEdge site details: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Test connection to SolarEdge API
     */
    public function testConnection()
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'SolarEdge API key not configured',
                    'details' => null
                ];
            }

            // Test with a simple sites list request
            $response = $this->makeRequest('/sites/list', ['size' => 1]);
            
            if ($response->successful()) {
                $data = $response->json();
                $siteCount = $data['sites']['count'] ?? 0;
                
                return [
                    'success' => true,
                    'message' => "Successfully connected to SolarEdge API. Found {$siteCount} sites.",
                    'details' => [
                        'api_version' => $data['sites']['version'] ?? null,
                        'site_count' => $siteCount,
                        'response_time_ms' => $response->transferStats?->getTransferTime() * 1000
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'SolarEdge API request failed: ' . $response->status() . ' ' . $response->reason(),
                    'details' => [
                        'status_code' => $response->status(),
                        'response_body' => $response->body()
                    ]
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'SolarEdge API connection error: ' . $e->getMessage(),
                'details' => [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]
            ];
        }
    }
}
