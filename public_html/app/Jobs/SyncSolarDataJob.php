<?php

namespace App\Jobs;

use App\Services\SolarApiService;
use App\Services\WeatherService;
use App\Models\System;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncSolarDataJob implements ShouldQueue
{
    use Queueable;

    protected $systemId;

    /**
     * Create a new job instance.
     */
    public function __construct($systemId = null)
    {
        $this->systemId = $systemId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $solarService = new SolarApiService();
            $weatherService = new WeatherService();

            if ($this->systemId) {
                // Sync specific system
                $system = System::find($this->systemId);
                if ($system && $system->api_enabled) {
                    $this->syncSystem($system, $solarService, $weatherService);
                }
            } else {
                // Sync all API-enabled systems
                $systems = System::apiEnabled()->get();
                foreach ($systems as $system) {
                    $this->syncSystem($system, $solarService, $weatherService);
                }
            }

            Log::info('Solar data sync completed successfully');

        } catch (\Exception $e) {
            Log::error('Solar data sync failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sync individual system data
     */
    protected function syncSystem(System $system, SolarApiService $solarService, WeatherService $weatherService)
    {
        try {
            // Sync production data
            $productionData = $solarService->syncSystemData($system);

            // Get weather data and update production record
            if ($productionData) {
                $weatherData = $weatherService->getWeatherData($system);
                
                // Update the production data with weather information
                $system->todayProductionData()->updateOrCreate(
                    ['date' => today()],
                    [
                        'weather_temperature' => $weatherData['temperature'],
                        'weather_condition' => $weatherData['condition'],
                    ]
                );
            }

        } catch (\Exception $e) {
            Log::error("Failed to sync system {$system->system_id}: " . $e->getMessage());
        }
    }
}
