<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Api\EnphaseApiService;
use App\Services\Api\SolarEdgeApiService;
use App\Services\Api\TeslaApiService;
use App\Services\WeatherService;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Test Weather Service
echo "=== Testing Weather Service ===\n";
$weatherService = new WeatherService();
$result = $weatherService->testConnection();
echo "Weather API: " . ($result['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $result['message'] . "\n\n";

// Test SolarEdge Service
echo "=== Testing SolarEdge Service ===\n";
$solarEdgeService = new SolarEdgeApiService();
$result = $solarEdgeService->testConnection();
echo "SolarEdge API: " . ($result['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $result['message'] . "\n\n";

// Test Enphase Service
echo "=== Testing Enphase Service ===\n";
$enphaseService = new EnphaseApiService();
$result = $enphaseService->testConnection();
echo "Enphase API: " . ($result['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $result['message'] . "\n\n";

// Test Tesla Service
echo "=== Testing Tesla Service ===\n";
$teslaService = new TeslaApiService();
$result = $teslaService->testConnection();
echo "Tesla API: " . ($result['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $result['message'] . "\n\n";

echo "=== API Test Complete ===\n";