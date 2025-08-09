<?php

namespace App\Http\Controllers;

use App\Services\SolarApiService;
use App\Services\WeatherService;
use App\Models\System;
use App\Models\ProductionData;
use App\Models\Alert;
use App\Models\ServiceSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $solarApiService;
    protected $weatherService;

    public function __construct(SolarApiService $solarApiService, WeatherService $weatherService)
    {
        $this->solarApiService = $solarApiService;
        $this->weatherService = $weatherService;
    }

    public function index()
    {
        // Get dashboard metrics
        $dashboardData = $this->solarApiService->getDashboardData();

        // Get recent alerts
        $recentAlerts = Alert::with('system')
            ->latest()
            ->limit(5)
            ->get();

        // Get upcoming service schedules
        $upcomingServices = ServiceSchedule::with('system')
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->get()
            ->groupBy(function ($item) {
                return $item->scheduled_date->format('Y-m-d');
            });

        // Get production trends (last 7 days)
        $productionTrends = ProductionData::selectRaw('DATE(date) as date, SUM(energy_today) as total_energy')
            ->where('date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get system status distribution
        $systemStatusCounts = System::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get weather data for systems
        $systems = System::with('latestProductionData')->get();
        $weatherData = $this->weatherService->getWeatherForSystems($systems);

        return view('backend.dashboard.index', compact(
            'dashboardData',
            'recentAlerts',
            'upcomingServices',
            'productionTrends',
            'systemStatusCounts',
            'weatherData'
        ));
    }

    /**
     * Get real-time dashboard data via AJAX
     */
    public function getRealTimeData()
    {
        $dashboardData = $this->solarApiService->getDashboardData();
        
        return response()->json([
            'total_systems' => $dashboardData['total_systems'],
            'active_systems' => $dashboardData['active_systems'],
            'api_enabled_systems' => $dashboardData['api_enabled_systems'],
            'total_energy_today' => $dashboardData['total_energy_today'],
            'total_power_current' => $dashboardData['total_power_current'],
            'total_lifetime' => $dashboardData['total_lifetime'],
            'avg_efficiency' => $dashboardData['avg_efficiency'],
            'avg_temperature' => $dashboardData['avg_temperature'],
            'last_updated' => now()->format('H:i:s'),
        ]);
    }
}
