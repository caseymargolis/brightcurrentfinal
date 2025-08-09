<?php

namespace App\Services;

use App\Models\System;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('solar.weather.api_key');
        $this->baseUrl = config('solar.weather.base_url', 'https://api.weatherapi.com/v1');
    }

    /**
     * Get weather data for a system location
     */
    public function getWeatherData(System $system)
    {
        try {
            if (!$system->latitude || !$system->longitude) {
                throw new \Exception("System location coordinates not available");
            }

            $response = Http::timeout(15)->get($this->baseUrl . '/current.json', [
                'key' => $this->apiKey,
                'q' => $system->latitude . ',' . $system->longitude,
                'aqi' => 'yes',
            ]);

            if (!$response->successful()) {
                throw new \Exception("Weather API request failed: " . $response->body());
            }

            $data = $response->json();
            $current = $data['current'] ?? [];

            return [
                'temperature' => round($current['temp_c'] ?? 0, 1),
                'condition' => $current['condition']['text'] ?? 'Unknown',
                'description' => $current['condition']['text'] ?? '',
                'humidity' => $current['humidity'] ?? 0,
                'wind_speed' => round(($current['wind_kph'] ?? 0) / 3.6, 1), // Convert kph to m/s
                'wind_direction' => $current['wind_dir'] ?? '',
                'pressure' => $current['pressure_mb'] ?? 0,
                'visibility' => $current['vis_km'] ?? 0,
                'uv_index' => $current['uv'] ?? 0,
                'feels_like' => round($current['feelslike_c'] ?? 0, 1),
                'cloud_cover' => $current['cloud'] ?? 0,
                'solar_irradiance' => $this->calculateSolarIrradiance($current),
                'icon_url' => 'https:' . ($current['condition']['icon'] ?? ''),
                'last_updated' => $current['last_updated'] ?? now()->toDateTimeString(),
            ];

        } catch (\Exception $e) {
            Log::error("Weather API Error for system {$system->system_id}: " . $e->getMessage());
            return [
                'temperature' => null,
                'condition' => null,
                'description' => 'Weather data unavailable',
                'humidity' => null,
                'wind_speed' => null,
                'wind_direction' => null,
                'pressure' => null,
                'visibility' => null,
                'uv_index' => null,
                'feels_like' => null,
                'cloud_cover' => null,
                'solar_irradiance' => null,
                'icon_url' => null,
                'last_updated' => now()->toDateTimeString(),
            ];
        }
    }

    /**
     * Get weather forecast for a system location
     */
    public function getWeatherForecast(System $system, int $days = 7)
    {
        try {
            if (!$system->latitude || !$system->longitude) {
                throw new \Exception("System location coordinates not available");
            }

            $response = Http::timeout(15)->get($this->baseUrl . '/forecast.json', [
                'key' => $this->apiKey,
                'q' => $system->latitude . ',' . $system->longitude,
                'days' => min($days, 10), // WeatherAPI.com supports up to 10 days
                'aqi' => 'yes',
                'alerts' => 'yes',
            ]);

            if (!$response->successful()) {
                throw new \Exception("Weather forecast API request failed: " . $response->body());
            }

            $data = $response->json();
            $forecasts = [];

            foreach ($data['forecast']['forecastday'] ?? [] as $day) {
                $dayData = $day['day'] ?? [];
                $forecasts[] = [
                    'date' => $day['date'],
                    'max_temp' => round($dayData['maxtemp_c'] ?? 0, 1),
                    'min_temp' => round($dayData['mintemp_c'] ?? 0, 1),
                    'avg_temp' => round($dayData['avgtemp_c'] ?? 0, 1),
                    'condition' => $dayData['condition']['text'] ?? 'Unknown',
                    'icon_url' => 'https:' . ($dayData['condition']['icon'] ?? ''),
                    'max_wind_speed' => round(($dayData['maxwind_kph'] ?? 0) / 3.6, 1),
                    'avg_humidity' => $dayData['avghumidity'] ?? 0,
                    'precipitation' => $dayData['totalprecip_mm'] ?? 0,
                    'uv_index' => $dayData['uv'] ?? 0,
                    'sunrise' => $day['astro']['sunrise'] ?? '',
                    'sunset' => $day['astro']['sunset'] ?? '',
                ];
            }

            return $forecasts;

        } catch (\Exception $e) {
            Log::error("Weather Forecast API Error for system {$system->system_id}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get weather data for multiple systems
     */
    public function getWeatherForSystems($systems)
    {
        $weatherData = [];

        foreach ($systems as $system) {
            $weatherData[$system->id] = $this->getWeatherData($system);
        }

        return $weatherData;
    }

    /**
     * Get weather by location name or coordinates
     */
    public function getWeatherByLocation($location)
    {
        try {
            $response = Http::timeout(15)->get($this->baseUrl . '/current.json', [
                'key' => $this->apiKey,
                'q' => $location,
                'aqi' => 'yes',
            ]);

            if (!$response->successful()) {
                throw new \Exception("Weather API request failed: " . $response->body());
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error("Weather API Error for location {$location}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Test connection to WeatherAPI.com
     */
    public function testConnection()
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Weather API key not configured',
                    'details' => null
                ];
            }

            // Test with a simple location request
            $response = Http::timeout(10)->get($this->baseUrl . '/current.json', [
                'key' => $this->apiKey,
                'q' => 'London',
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'message' => 'Successfully connected to WeatherAPI.com',
                    'details' => [
                        'location' => $data['location']['name'] ?? 'Unknown',
                        'country' => $data['location']['country'] ?? 'Unknown',
                        'temperature' => $data['current']['temp_c'] ?? 'N/A',
                        'condition' => $data['current']['condition']['text'] ?? 'N/A',
                        'response_time_ms' => $response->transferStats?->getTransferTime() * 1000
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'WeatherAPI.com request failed: ' . $response->status() . ' ' . $response->reason(),
                    'details' => [
                        'status_code' => $response->status(),
                        'response_body' => $response->body()
                    ]
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'WeatherAPI.com connection error: ' . $e->getMessage(),
                'details' => [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]
            ];
        }
    }

    /**
     * Calculate solar irradiance based on weather conditions
     */
    protected function calculateSolarIrradiance($currentWeather)
    {
        // This is an estimation based on cloud cover, UV index, and time of day
        $cloudCover = $currentWeather['cloud'] ?? 0;
        $uvIndex = $currentWeather['uv'] ?? 0;
        
        // Base irradiance (W/m²)
        $baseIrradiance = 1000; // Peak solar irradiance
        
        // Reduce based on cloud cover (0-100%)
        $cloudReduction = (100 - $cloudCover) / 100;
        
        // Adjust based on UV index (0-11+)
        $uvMultiplier = min($uvIndex / 10, 1);
        
        $estimatedIrradiance = $baseIrradiance * $cloudReduction * $uvMultiplier;
        
        return round($estimatedIrradiance, 0);
    }
}
