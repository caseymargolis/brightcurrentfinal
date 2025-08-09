<?php

namespace App\Http\Controllers;

use App\Services\SolarApiService;
use App\Services\Api\EnphaseApiService;
use App\Services\Api\SolarEdgeApiService;
use App\Services\Api\TeslaApiService;
use App\Services\WeatherService;
use App\Jobs\SyncSolarDataJob;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SolarApiController extends Controller
{
    protected $solarApiService;

    public function __construct(SolarApiService $solarApiService)
    {
        $this->solarApiService = $solarApiService;
    }

    /**
     * Display API management dashboard
     */
    public function index()
    {
        $systems = System::with('latestProductionData')->get();
        $apiEnabledSystems = $systems->where('api_enabled', true)->count();
        $totalSystems = $systems->count();

        return view('backend.solar-api.index', compact(
            'systems',
            'apiEnabledSystems',
            'totalSystems'
        ));
    }

    /**
     * Test API connections
     */
    public function testConnections()
    {
        $results = [
            'enphase' => $this->testEnphaseConnection(),
            'solaredge' => $this->testSolarEdgeConnection(),
            'tesla' => $this->testTeslaConnection(),
            'weather' => $this->testWeatherConnection(),
        ];

        return response()->json($results);
    }

    /**
     * Sync data for specific system
     */
    public function syncSystem(Request $request, System $system)
    {
        try {
            if (!$system->api_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'API is not enabled for this system'
                ], 400);
            }

            SyncSolarDataJob::dispatch($system->id);

            return response()->json([
                'success' => true,
                'message' => 'Sync job dispatched successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to sync system {$system->id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to dispatch sync job'
            ], 500);
        }
    }

    /**
     * Sync all systems
     */
    public function syncAll()
    {
        try {
            SyncSolarDataJob::dispatch();

            return response()->json([
                'success' => true,
                'message' => 'Sync job dispatched for all systems'
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to sync all systems: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to dispatch sync job'
            ], 500);
        }
    }

    /**
     * Update system API settings
     */
    public function updateSystemApi(Request $request, System $system)
    {
        $request->validate([
            'api_enabled' => 'boolean',
            'external_system_id' => 'nullable|string',
            'manufacturer' => 'nullable|in:enphase,solaredge,tesla',
        ]);

        try {
            $system->update([
                'api_enabled' => $request->api_enabled ?? false,
                'external_system_id' => $request->external_system_id,
                'manufacturer' => $request->manufacturer,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'System API settings updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to update system API settings: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update system settings'
            ], 500);
        }
    }

    /**
     * Get available systems from APIs
     */
    public function getAvailableSystems(Request $request)
    {
        $manufacturer = $request->get('manufacturer');
        $systems = [];

        try {
            switch ($manufacturer) {
                case 'enphase':
                    $enphaseService = new EnphaseApiService();
                    $systems = $enphaseService->getSystems();
                    break;
                case 'solaredge':
                    $solarEdgeService = new SolarEdgeApiService();
                    $systems = $solarEdgeService->getSites();
                    break;
                case 'tesla':
                    $teslaService = new TeslaApiService();
                    $systems = $teslaService->getEnergySites();
                    break;
            }

            return response()->json([
                'success' => true,
                'systems' => $systems
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to fetch systems from {$manufacturer}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => "Failed to fetch systems from {$manufacturer} API"
            ], 500);
        }
    }

    protected function testEnphaseConnection()
    {
        try {
            $service = new EnphaseApiService();
            return $service->testConnection();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function testSolarEdgeConnection()
    {
        try {
            $service = new SolarEdgeApiService();
            return $service->testConnection();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function testTeslaConnection()
    {
        try {
            $service = new TeslaApiService();
            return $service->testConnection();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function testWeatherConnection()
    {
        try {
            $service = new WeatherService();
            return $service->testConnection();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
