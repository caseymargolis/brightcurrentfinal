<?php

namespace App\Services;

use App\Services\Api\EnphaseApiService;
use App\Services\Api\SolarEdgeApiService;
use App\Services\Api\TeslaApiService;
use App\Services\WeatherService;
use App\Models\System;
use App\Models\ProductionData;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SolarApiService
{
    protected $enphaseService;
    protected $solarEdgeService;
    protected $teslaService;
    protected $weatherService;

    public function __construct()
    {
        $this->enphaseService = new EnphaseApiService();
        $this->solarEdgeService = new SolarEdgeApiService();
        $this->teslaService = new TeslaApiService();
        $this->weatherService = new WeatherService();
    }

    /**
     * Sync production data for all systems
     */
    public function syncAllSystems()
    {
        $systems = System::all();
        
        foreach ($systems as $system) {
            try {
                $this->syncSystemData($system);
            } catch (\Exception $e) {
                Log::error("Error syncing system {$system->system_id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Sync production data for a specific system
     */
    public function syncSystemData(System $system)
    {
        $productionData = null;
        $solarDataSuccess = false;

        // Get solar production data based on manufacturer
        try {
            switch (strtolower($system->manufacturer)) {
                case 'enphase':
                    $productionData = $this->enphaseService->getProductionData($system);
                    break;
                case 'solaredge':
                    $productionData = $this->solarEdgeService->getProductionData($system);
                    break;
                case 'tesla':
                    $productionData = $this->teslaService->getProductionData($system);
                    break;
                default:
                    Log::warning("Unsupported manufacturer: {$system->manufacturer}");
            }
            
            if ($productionData) {
                $solarDataSuccess = true;
            }
        } catch (\Exception $e) {
            Log::error("Failed to get solar data for system {$system->system_id}: " . $e->getMessage());
        }

        // Always try to get weather data, regardless of solar data success
        try {
            $weatherData = $this->weatherService->getWeatherData($system);
            
            if ($solarDataSuccess && $productionData) {
                // Merge weather data into production data
                $productionData = array_merge($productionData, [
                    'weather_temperature' => $weatherData['temperature'] ?? null,
                    'weather_condition' => $weatherData['condition'] ?? null,
                    'weather_humidity' => $weatherData['humidity'] ?? null,
                    'weather_wind_speed' => $weatherData['wind_speed'] ?? null,
                    'weather_wind_direction' => $weatherData['wind_direction'] ?? null,
                    'weather_pressure' => $weatherData['pressure'] ?? null,
                    'weather_uv_index' => $weatherData['uv_index'] ?? null,
                    'weather_cloud_cover' => $weatherData['cloud_cover'] ?? null,
                    'weather_solar_irradiance' => $weatherData['solar_irradiance'] ?? null,
                    'weather_icon_url' => $weatherData['icon_url'] ?? null,
                ]);

                $this->storeProductionData($system, $productionData);
                $this->updateSystemStatus($system, $productionData);
            } else {
                // No solar data, but sync weather data independently
                $this->syncWeatherOnly($system);
                
                // Update system to show it's offline/having issues
                $system->update([
                    'status' => 'offline',
                    'last_seen' => now(),
                ]);
                Log::warning("System {$system->system_id} marked as offline due to API failure");
            }
        } catch (\Exception $e) {
            Log::error("Failed to get weather data for system {$system->system_id}: " . $e->getMessage());
            
            // Still update last_seen to show we attempted sync
            $system->update([
                'last_seen' => now(),
            ]);
        }

        return $productionData;
    }

    /**
     * Store production data in database
     */
    protected function storeProductionData(System $system, array $data)
    {
        ProductionData::updateOrCreate(
            [
                'system_id' => $system->id,
                'date' => Carbon::parse($data['date'] ?? now()),
            ],
            [
                'energy_today' => $data['energy_today'] ?? null,
                'energy_yesterday' => $data['energy_yesterday'] ?? null,
                'power_current' => $data['power_current'] ?? null,
                'efficiency' => $data['efficiency'] ?? null,
                'energy_lifetime' => $data['energy_lifetime'] ?? null,
                'weather_temperature' => $data['weather_temperature'] ?? null,
                'weather_condition' => $data['weather_condition'] ?? null,
                'weather_humidity' => $data['weather_humidity'] ?? null,
                'weather_wind_speed' => $data['weather_wind_speed'] ?? null,
                'weather_wind_direction' => $data['weather_wind_direction'] ?? null,
                'weather_pressure' => $data['weather_pressure'] ?? null,
                'weather_uv_index' => $data['weather_uv_index'] ?? null,
                'weather_cloud_cover' => $data['weather_cloud_cover'] ?? null,
                'weather_solar_irradiance' => $data['weather_solar_irradiance'] ?? null,
                'weather_icon_url' => $data['weather_icon_url'] ?? null,
            ]
        );
    }

    /**
     * Update system status and last seen
     */
    protected function updateSystemStatus(System $system, array $data)
    {
        // Map API statuses to system statuses
        $statusMap = [
            'active' => 'active',
            'inactive' => 'offline',
            'communication_error' => 'warning',
            'power_issue' => 'warning',
            'maintenance' => 'offline',
            'pending' => 'warning',
        ];
        
        $apiStatus = strtolower($data['system_status'] ?? '');
        $mappedStatus = $statusMap[$apiStatus] ?? $system->status;
        
        $system->update([
            'status' => $mappedStatus,
            'last_seen' => now(),
        ]);
        
        Log::info("Updated system {$system->system_id} - Status: {$mappedStatus}, Last seen: " . now());
    }

    /**
     * Get real-time dashboard data with weather information
     */
    public function getDashboardData()
    {
        $systems = System::with(['productionData' => function ($query) {
            // Get the most recent production data instead of filtering by today
            // This handles timezone issues where the date might be off by a day
            $query->latest('created_at')->limit(1);
        }])->get();

        $totalSystems = $systems->count();
        $activeSystems = $systems->where('status', 'active')->count();
        $apiEnabledSystems = $systems->where('api_enabled', true)->count();
        
        $totalEnergyToday = $systems->sum(function ($system) {
            return $system->productionData->first()->energy_today ?? 0;
        });
        
        $totalPowerCurrent = $systems->sum(function ($system) {
            return $system->productionData->first()->power_current ?? 0;
        });
        
        $totalLifetime = $systems->sum(function ($system) {
            return $system->productionData->first()->energy_lifetime ?? 0;
        });
        
        // Calculate average efficiency (where efficiency data exists)
        $systemsWithEfficiency = $systems->filter(function ($system) {
            return $system->productionData->first() && $system->productionData->first()->efficiency;
        });
        
        $avgEfficiency = $systemsWithEfficiency->count() > 0 
            ? $systemsWithEfficiency->avg(function ($system) {
                return $system->productionData->first()->efficiency;
            }) 
            : 0;
        
        // Calculate average temperature (where weather data exists)
        $systemsWithWeather = $systems->filter(function ($system) {
            return $system->productionData->first() && $system->productionData->first()->weather_temperature;
        });
        
        $avgTemperature = $systemsWithWeather->count() > 0 
            ? $systemsWithWeather->avg(function ($system) {
                return $system->productionData->first()->weather_temperature;
            }) 
            : 0;

        // Get weather condition summary
        $weatherConditions = $systemsWithWeather->map(function ($system) {
            return $system->productionData->first()->weather_condition;
        })->filter()->countBy();

        $dominantWeatherCondition = $weatherConditions->count() > 0 
            ? $weatherConditions->sortDesc()->keys()->first() 
            : 'Unknown';

        return [
            'total_systems' => $totalSystems,
            'active_systems' => $activeSystems,
            'api_enabled_systems' => $apiEnabledSystems,
            'total_energy_today' => round($totalEnergyToday, 2),
            'total_power_current' => round($totalPowerCurrent, 2),
            'total_lifetime' => round($totalLifetime, 0),
            'avg_efficiency' => round($avgEfficiency, 1),
            'avg_temperature' => round($avgTemperature, 1),
            'dominant_weather_condition' => $dominantWeatherCondition,
            'systems' => $systems,
            'weather_summary' => [
                'avg_temperature' => round($avgTemperature, 1),
                'condition' => $dominantWeatherCondition,
                'systems_with_weather' => $systemsWithWeather->count(),
            ],
        ];
    }

    /**
     * Get weather data for all systems
     */
    public function getWeatherDataForAllSystems()
    {
        $systems = System::all();
        $weatherData = [];

        foreach ($systems as $system) {
            $weatherData[$system->id] = $this->weatherService->getWeatherData($system);
        }

        return $weatherData;
    }

    /**
     * Sync weather data for a specific system (independent of solar data)
     */
    public function syncWeatherOnly(System $system)
    {
        try {
            // Get weather data
            $weatherData = $this->weatherService->getWeatherData($system);
            
            if (!$weatherData || !isset($weatherData['temperature'])) {
                Log::warning("No weather data available for system {$system->system_id}");
                return false;
            }

            // Store weather data independently
            ProductionData::updateOrCreate(
                [
                    'system_id' => $system->id,
                    'date' => now(),
                ],
                [
                    'weather_temperature' => $weatherData['temperature'],
                    'weather_condition' => $weatherData['condition'],
                    'weather_humidity' => $weatherData['humidity'],
                    'weather_wind_speed' => $weatherData['wind_speed'],
                    'weather_wind_direction' => $weatherData['wind_direction'],
                    'weather_pressure' => $weatherData['pressure'],
                    'weather_uv_index' => $weatherData['uv_index'],
                    'weather_cloud_cover' => $weatherData['cloud_cover'],
                    'weather_solar_irradiance' => $weatherData['solar_irradiance'],
                    'weather_icon_url' => $weatherData['icon_url'],
                ]
            );

            Log::info("Weather data synced successfully for system {$system->system_id}");
            return $weatherData;

        } catch (\Exception $e) {
            Log::error("Error syncing weather for system {$system->system_id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync weather data for all systems
     */
    public function syncWeatherForAllSystems()
    {
        $systems = System::all();
        $results = [];

        foreach ($systems as $system) {
            $results[$system->system_id] = $this->syncWeatherOnly($system);
        }

        return $results;
    }
}