<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SolarApiService;
use App\Models\System;

class SyncWeatherData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solar:sync-weather {system_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync weather data for solar systems';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $systemId = $this->argument('system_id');
        $solarApiService = new SolarApiService();

        if ($systemId) {
            $system = System::where('system_id', $systemId)->first();
            if (!$system) {
                $this->error("System {$systemId} not found");
                return 1;
            }
            
            $this->info("Syncing weather data for system {$systemId}...");
            $result = $solarApiService->syncWeatherOnly($system);
            
            if ($result) {
                $this->info("✅ Weather data synced successfully");
                $this->line("Temperature: " . ($result['temperature'] ?? 'N/A') . "°C");
                $this->line("Condition: " . ($result['condition'] ?? 'N/A'));
                $this->line("Humidity: " . ($result['humidity'] ?? 'N/A') . "%");
                $this->line("Wind Speed: " . ($result['wind_speed'] ?? 'N/A') . " m/s");
                $this->line("Solar Irradiance: " . ($result['solar_irradiance'] ?? 'N/A') . " W/m²");
                $this->line("UV Index: " . ($result['uv_index'] ?? 'N/A'));
            } else {
                $this->error("❌ Failed to sync weather data");
            }
        } else {
            $this->info("Syncing weather data for all systems...");
            $results = $solarApiService->syncWeatherForAllSystems();
            $successCount = 0;
            
            foreach ($results as $systemId => $result) {
                $this->line("Processing {$systemId}...");
                if ($result !== false) {
                    $successCount++;
                    $this->info("  ✅ Success - {$result['temperature']}°C, {$result['condition']}");
                } else {
                    $this->warn("  ⚠️ Failed to sync weather data");
                }
            }
            
            $this->info("Completed! {$successCount}/" . count($results) . " systems synced successfully");
        }
    }
}