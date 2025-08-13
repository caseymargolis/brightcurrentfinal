<?php

namespace App\Http\Controllers;

use App\Services\SolarApiService;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    protected $solarApiService;

    public function __construct(SolarApiService $solarApiService)
    {
        $this->solarApiService = $solarApiService;
    }

    public function index()
    {
        try {
            $dashboard_data = $this->solarApiService->getDashboardData();
        } catch (\Exception $e) {
            // If there's an error getting dashboard data, provide defaults
            $dashboard_data = [
                'total_systems' => 0,
                'active_systems' => 0,
                'total_energy_today' => 0,
                'total_power_current' => 0,
            ];
        }

        return view('landing', compact('dashboard_data'));
    }
}