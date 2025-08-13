<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WeatherService;

class TestApiConnections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solar:test-apis {api?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connections to all solar and weather APIs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $api = $this->argument('api');
        
        if ($api) {
            $this->testSpecificApi($api);
        } else {
            $this->testAllApis();
        }
    }

    private function testAllApis()
    {
        $this->info('🌤️  Testing Weather API...');
        $weatherService = new WeatherService();
        $weatherResult = $weatherService->testConnection();
        $this->displayResult('Weather API', $weatherResult);

        $this->info('⚡ Testing SolarEdge API...');
        $solarEdgeService = new \App\Services\Api\SolarEdgeApiService();
        $solarEdgeResult = $solarEdgeService->testConnection();
        $this->displayResult('SolarEdge API', $solarEdgeResult);

        $this->info('🔆 Testing Enphase API...');
        $enphaseService = new \App\Services\Api\EnphaseApiService();
        $enphaseResult = $enphaseService->testConnection();
        $this->displayResult('Enphase API', $enphaseResult);

        $this->info('🚗 Testing Tesla API...');
        $teslaService = new \App\Services\Api\TeslaApiService();
        $teslaResult = $teslaService->testConnection();
        $this->displayResult('Tesla API', $teslaResult);

        $this->newLine();
        $this->info('API connection tests completed!');
    }

    private function testSpecificApi($api)
    {
        $this->info("Testing {$api} API...");
        
        switch (strtolower($api)) {
            case 'weather':
                $service = new WeatherService();
                $result = $service->testConnection();
                $this->displayResult('Weather API', $result);
                break;
            case 'tesla':
                $service = new \App\Services\Api\TeslaApiService();
                $result = $service->testConnection();
                $this->displayResult('Tesla API', $result);
                break;
            default:
                $this->error("Unknown API: {$api}");
                return 1;
        }
    }

    private function displayResult($apiName, $result)
    {
        if ($result['success'] === true) {
            $this->info("✅ {$apiName}: " . $result['message']);
        } else {
            $this->error("❌ {$apiName}: " . $result['message']);
        }
        
        // Show additional details if available
        if (isset($result['details']) && $result['details'] && $this->output->isVerbose()) {
            $this->line('   Details: ' . json_encode($result['details'], JSON_PRETTY_PRINT));
        }
        
        $this->newLine();
    }
}
