@extends('backend.layouts.master')
@section('title') {{'Dashboard'}} @endsection

@section('breadcrumb') Pages / Dashboard @endsection
@section('page-title') Main Dashboard @endsection

@section('content')
@include('backend.partials.alert')

<!-- Real-time Solar Statistics -->
<div class="row gy-4 mb-5">
    <div class="col-lg-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary-light mb-1">Total Systems</h6>
                        <h2 class="mb-0" id="totalSystems">{{ $dashboardData['total_systems'] ?? 0 }}</h2>
                    </div>
                    <div class="w-50-px h-50-px bg-primary-100 d-flex justify-content-center align-items-center rounded-circle border border-primary-200">
                        <iconify-icon icon="solar:solarpanel-bold" class="h5 mb-0 text-primary"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary-light mb-1">Active Systems</h6>
                        <h2 class="mb-0 text-success" id="activeSystems">{{ $dashboardData['active_systems'] ?? 0 }}</h2>
                    </div>
                    <div class="w-50-px h-50-px bg-success-100 d-flex justify-content-center align-items-center rounded-circle border border-success-200">
                        <iconify-icon icon="solar:power-bold" class="h5 mb-0 text-success"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary-light mb-1">Today's Energy</h6>
                        <h2 class="mb-0 text-warning" id="energyToday">{{ number_format($dashboardData['total_energy_today'] ?? 0, 1) }}</h2>
                        <span class="text-sm text-secondary-light">kWh</span>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-100 d-flex justify-content-center align-items-center rounded-circle border border-warning-200">
                        <iconify-icon icon="solar:battery-charge-bold" class="h5 mb-0 text-warning"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary-light mb-1">Current Power</h6>
                        <h2 class="mb-0 text-info" id="currentPower">{{ number_format($dashboardData['total_power_current'] ?? 0, 1) }}</h2>
                        <span class="text-sm text-secondary-light">kW</span>
                    </div>
                    <div class="w-50-px h-50-px bg-info-100 d-flex justify-content-center align-items-center rounded-circle border border-info-200">
                        <iconify-icon icon="solar:lightbulb-bolt-bold" class="h5 mb-0 text-info"></iconify-icon>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted" id="lastUpdated">Last updated: {{ now()->format('H:i:s') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Energy Metrics -->
<div class="row gy-4 mb-5">
    <div class="col-lg-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary-light mb-1">Avg Efficiency</h6>
                        <h2 class="mb-0 text-success" id="avgEfficiency">{{ number_format($dashboardData['avg_efficiency'] ?? 0, 1) }}%</h2>
                    </div>
                    <div class="w-50-px h-50-px bg-success-100 d-flex justify-content-center align-items-center rounded-circle border border-success-200">
                        <iconify-icon icon="solar:chart-2-bold" class="h5 mb-0 text-success"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary-light mb-1">Total Lifetime</h6>
                        <h2 class="mb-0 text-info" id="totalLifetime">{{ number_format($dashboardData['total_lifetime'] ?? 0, 0) }}</h2>
                        <span class="text-sm text-secondary-light">kWh</span>
                    </div>
                    <div class="w-50-px h-50-px bg-info-100 d-flex justify-content-center align-items-center rounded-circle border border-info-200">
                        <iconify-icon icon="solar:history-bold" class="h5 mb-0 text-info"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary-light mb-1">API Connected</h6>
                        <h2 class="mb-0 text-primary" id="apiConnected">{{ $dashboardData['api_enabled_systems'] ?? 0 }}</h2>
                        <span class="text-sm text-secondary-light">of {{ $dashboardData['total_systems'] ?? 0 }}</span>
                    </div>
                    <div class="w-50-px h-50-px bg-primary-100 d-flex justify-content-center align-items-center rounded-circle border border-primary-200">
                        <iconify-icon icon="solar:link-bold" class="h5 mb-0 text-primary"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary-light mb-1">Avg Temperature</h6>
                        <h2 class="mb-0 text-warning" id="avgTemperature">{{ number_format($dashboardData['avg_temperature'] ?? 0, 1) }}°C</h2>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-100 d-flex justify-content-center align-items-center rounded-circle border border-warning-200">
                        <iconify-icon icon="solar:thermometer-bold" class="h5 mb-0 text-warning"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Weather Dashboard -->
@php
    $weatherSummary = $dashboardData['weather_summary'] ?? null;
    $firstSystemWithWeather = ($dashboardData['systems'] ?? collect())->first(function($system) {
        return $system->productionData->first() && $system->productionData->first()->weather_temperature;
    });
    $currentWeather = $firstSystemWithWeather ? $firstSystemWithWeather->productionData->first() : null;
@endphp

@if($currentWeather || $weatherSummary)
<div class="row gy-4 mb-5">
    <div class="col-12">
        <div class="card weather-history-card">
            <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 d-flex align-items-center">
                        <iconify-icon icon="solar:cloud-sun-bold" class="me-2 text-primary fs-4"></iconify-icon>
                        Weather History
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary active weather-period-btn" data-period="daily">Daily</button>
                        <button class="btn btn-sm btn-outline-primary weather-period-btn" data-period="weekly">Weekly</button>
                        <button class="btn btn-sm btn-outline-primary weather-period-btn" data-period="monthly">Monthly</button>
                    </div>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="row g-4">
                    <!-- Current Weather Summary - Compact Left Sidebar -->
                    <div class="col-xl-2 col-lg-3 col-md-4">
                        <div class="weather-summary-sidebar bg-gradient-primary text-white rounded-3 p-3 h-100">
                            <!-- Main Temperature Display -->
                            <div class="text-center mb-3">
                                @if($currentWeather && $currentWeather->weather_icon_url)
                                    <img src="{{ $currentWeather->weather_icon_url }}" alt="Weather Icon" class="mb-2" style="width: 60px; height: 60px;">
                                @else
                                    <iconify-icon icon="solar:sun-bold" class="text-warning mb-2" style="font-size: 60px;"></iconify-icon>
                                @endif
                                <div class="h2 mb-0 fw-bold">{{ number_format($currentWeather->weather_temperature ?? $weatherSummary['avg_temperature'] ?? 0, 0) }}°C</div>
                                <div class="small opacity-75">{{ $currentWeather->weather_condition ?? $weatherSummary['condition'] ?? 'Unknown' }}</div>
                            </div>
                            
                            <!-- Location -->
                            <div class="text-center mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="d-flex align-items-center justify-content-center text-white text-opacity-75">
                                    <iconify-icon icon="solar:map-point-bold" class="me-1"></iconify-icon>
                                    <span class="small">{{ strlen($firstSystemWithWeather->location ?? 'Multiple Locations') > 20 ? substr($firstSystemWithWeather->location ?? 'Multiple Locations', 0, 17) . '...' : ($firstSystemWithWeather->location ?? 'Multiple Locations') }}</span>
                                </div>
                                <div class="text-white text-opacity-50" style="font-size: 0.7rem;">
                                    {{ now()->format('g:i A') }}
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="text-center">
                                        <iconify-icon icon="solar:drop-outline" class="d-block mb-1 text-info"></iconify-icon>
                                        <div class="fw-semibold small">{{ $currentWeather->weather_humidity ?? 0 }}%</div>
                                        <div class="text-white text-opacity-75" style="font-size: 0.65rem;">Humidity</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <iconify-icon icon="solar:wind-outline" class="d-block mb-1 text-success"></iconify-icon>
                                        <div class="fw-semibold small">{{ number_format($currentWeather->weather_wind_speed ?? 0, 1) }}m/s</div>
                                        <div class="text-white text-opacity-75" style="font-size: 0.65rem;">Wind</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Temperature Chart Section - Much Wider -->
                    <div class="col-xl-10 col-lg-9 col-md-8">
                        <div class="temperature-chart-container">
                            <!-- Chart Header with Period Controls -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <iconify-icon icon="solar:thermometer-outline" class="me-2 text-primary fs-5"></iconify-icon>
                                    <span class="fw-semibold fs-5">Temperature Trends</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center">
                                        <iconify-icon icon="solar:drop-outline" class="me-1 text-info"></iconify-icon>
                                        <span class="small text-muted">Humidity: {{ $currentWeather->weather_humidity ?? 0 }}%</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <iconify-icon icon="solar:wind-outline" class="me-1 text-success"></iconify-icon>
                                        <span class="small text-muted">Wind: {{ number_format($currentWeather->weather_wind_speed ?? 0, 1) }}m/s</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <iconify-icon icon="solar:sun-outline" class="me-1 text-warning"></iconify-icon>
                                        <span class="small text-muted">Solar: {{ $currentWeather->weather_solar_irradiance ?? 0 }}W/m²</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Enhanced Temperature Chart Container -->
                            <div class="temperature-chart-wrapper position-relative bg-gradient-chart rounded-3 p-4 mb-4" style="height: 280px;">
                                <canvas id="temperatureChart" class="w-100" height="240"></canvas>
                            </div>
                            
                            <!-- Weather Details Grid - Improved Layout -->
                            <div class="row g-3">
                                <div class="col-xl-3 col-md-6">
                                    <div class="weather-stat-card d-flex align-items-center p-3 bg-light rounded-3 border-0">
                                        <div class="weather-stat-icon me-3">
                                            <iconify-icon icon="solar:cloud-outline" class="text-secondary fs-3"></iconify-icon>
                                        </div>
                                        <div>
                                            <div class="fw-bold h6 mb-0">{{ $currentWeather->weather_cloud_cover ?? 0 }}%</div>
                                            <div class="small text-muted">Cloud Cover</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="weather-stat-card d-flex align-items-center p-3 bg-light rounded-3 border-0">
                                        <div class="weather-stat-icon me-3">
                                            <iconify-icon icon="solar:eye-outline" class="text-info fs-3"></iconify-icon>
                                        </div>
                                        <div>
                                            <div class="fw-bold h6 mb-0">{{ $currentWeather->weather_uv_index ?? 0 }}</div>
                                            <div class="small text-muted">UV Index</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="weather-stat-card d-flex align-items-center p-3 bg-light rounded-3 border-0">
                                        <div class="weather-stat-icon me-3">
                                            <iconify-icon icon="solar:compass-outline" class="text-primary fs-3"></iconify-icon>
                                        </div>
                                        <div>
                                            <div class="fw-bold h6 mb-0">{{ $currentWeather->weather_pressure ?? 0 }}mb</div>
                                            <div class="small text-muted">Pressure</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="weather-stat-card d-flex align-items-center p-3 bg-light rounded-3 border-0">
                                        <div class="weather-stat-icon me-3">
                                            <iconify-icon icon="solar:windsock-outline" class="text-success fs-3"></iconify-icon>
                                        </div>
                                        <div>
                                            <div class="fw-bold h6 mb-0">{{ $currentWeather->weather_wind_direction ?? 'N/A' }}</div>
                                            <div class="small text-muted">Wind Direction</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- System Performance Chart -->
<div class="row gy-4 mt-5">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                    <h6 class="mb-2 fw-bold text-2xl">System Performance Overview</h6>
                    <div class="d-flex justify-content-center gap-36 align-items-center">
                        <ul class="d-flex flex-wrap align-items-center gap-24">
                            <li class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-12-px rounded-circle" style="background-color: #2B3674"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Total Production</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-12-px rounded-circle" style="background-color: #D9D9D9"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Expected Output</span>
                            </li>
                        </ul>
                        <select id="periodSelect" class="form-select form-select-sm w-auto border-primary text-secondary-light radius-8" style="padding: 0.25rem 1.5rem !important;">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>
                <div class="mt-40">
                    <div id="systemPerformanceAreaChart" class="margin-16-minus"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="col-12">
        <h4 class="mb-16">Quick Links</h4>
        <div class="row gy-4">
            <div class="col-lg-3 col-sm-6">
                <div class="card px-24 py-36 shadow-none radius-12 border" style="height: 134px;">
                    <a href="/dashboard/service-schedules">
                        <div class="card-body p-0">
                            <i class="ri-calendar-todo-line text-xxl me-14 w-auto"></i>
                            <h6 class="fw-semibold mb-0">Schedule Service</h6>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card px-24 py-36 shadow-none radius-12 border" style="height: 134px;">
                    <a href="/dashboard/reports">
                        <div class="card-body p-0">
                            <i class="ri-upload-2-line text-xxl me-14 w-auto"></i>
                            <h6 class="fw-semibold mb-0">Export Reports</h6>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card px-24 py-36 shadow-none radius-12 border" style="height: 134px;">
                    <a href="/dashboard/system">
                        <div class="card-body p-0">
                            <i class="ri-add-fill text-xxl me-14 w-auto"></i>
                            <h6 class="fw-semibold mb-0">Add System</h6>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card px-24 py-36 shadow-none radius-12 border" style="height: 134px;">
                    <a href="/dashboard/solar-api">
                        <div class="card-body p-0">
                            <i class="ri-settings-3-line text-xxl me-14 w-auto"></i>
                            <h6 class="fw-semibold mb-0">API Settings</h6>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Systems Overview -->
    <div class="col-xxl-8">
        <div class="card h-100">
            <div class="card-header">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                    <h6 class="fw-bold mb-0" style="font-size: 22px;">Live Solar Systems</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm d-flex align-items-center" onclick="refreshData()">
                            <iconify-icon icon="solar:refresh-outline" class="me-1"></iconify-icon>
                            <span>Refresh</span>
                        </button>
                        <button class="btn btn-success btn-sm d-flex align-items-center" onclick="syncAllSystems()">
                            <iconify-icon icon="solar:download-outline" class="me-1"></iconify-icon>
                            <span>Sync All</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 0 !important">
                <div class="table-responsive scroll-sm custom-scrollbar">
                    <table class="table bordered-table mb-0">
                        <thead>
                        <tr>
                            <th scope="col" style="padding: 16px 16px 16px 42px !important;">SYSTEM ID</th>
                            <th scope="col">CUSTOMER</th>
                            <th scope="col">MANUFACTURER</th>
                            <th scope="col">STATUS</th>
                            <th scope="col">CURRENT POWER</th>
                            <th scope="col">TODAY'S ENERGY</th>
                            <th scope="col">YESTERDAY</th>
                            <th scope="col">WEATHER</th>
                            <th scope="col">LAST SEEN</th>
                            <th scope="col">ACTIONS</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($dashboardData['systems'] ?? [] as $system)
                        <tr>
                            <td style="padding: 16px 16px 16px 42px !important;">
                                <span class="text-secondary-light fw-semibold">{{ $system->system_id }}</span>
                            </td>
                            <td>
                                <div>
                                    <span class="text-secondary-light fw-medium">{{ $system->customer_name }}</span>
                                    <br><small class="text-muted">{{ $system->location }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $system->manufacturer === 'enphase' ? 'primary' : ($system->manufacturer === 'solaredge' ? 'success' : 'info') }} text-white px-12 py-4 radius-8 fw-bold text-sm">
                                    {{ strtoupper($system->manufacturer) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'active' => 'success',
                                        'warning' => 'warning', 
                                        'critical' => 'danger',
                                        'offline' => 'secondary',
                                        'maintenance' => 'info'
                                    ];
                                    $statusColor = $statusColors[$system->status] ?? 'secondary';
                                @endphp
                                <span class="bg-{{ $statusColor }} text-white px-16 py-4 radius-12 fw-bold text-sm">
                                    {{ strtoupper($system->status) }}
                                </span>
                            </td>
                            <td>
                                @if($system->productionData->first())
                                    <span class="text-primary fw-semibold">
                                        {{ number_format($system->productionData->first()->power_current ?? 0, 1) }} kW
                                    </span>
                                @else
                                    <span class="text-muted">No data</span>
                                @endif
                            </td>
                            <td>
                                @if($system->productionData->first())
                                    <span class="text-warning fw-semibold">
                                        {{ number_format($system->productionData->first()->energy_today ?? 0, 1) }} kWh
                                    </span>
                                @else
                                    <span class="text-muted">No data</span>
                                @endif
                            </td>
                            <td>
                                @if($system->productionData->first())
                                    <span class="text-muted fw-medium">
                                        {{ number_format($system->productionData->first()->energy_yesterday ?? 0, 1) }} kWh
                                    </span>
                                @else
                                    <span class="text-muted">No data</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $productionWeather = $system->productionData->first();
                                    $liveWeather = $weatherData[$system->id] ?? null;
                                    $temperature = $productionWeather->weather_temperature ?? $liveWeather['temperature'] ?? null;
                                    $condition = $productionWeather->weather_condition ?? $liveWeather['condition'] ?? null;
                                @endphp
                                
                                @if($temperature)
                                    <div class="d-flex align-items-center gap-1">
                                        @if(strtolower($condition ?? '') === 'clear' || strpos(strtolower($condition ?? ''), 'sun') !== false)
                                            <iconify-icon icon="solar:sun-outline" class="text-warning"></iconify-icon>
                                        @elseif(strpos(strtolower($condition ?? ''), 'cloud') !== false)
                                            <iconify-icon icon="solar:cloud-outline" class="text-muted"></iconify-icon>
                                        @elseif(strpos(strtolower($condition ?? ''), 'rain') !== false)
                                            <iconify-icon icon="solar:cloud-rain-outline" class="text-primary"></iconify-icon>
                                        @else
                                            <iconify-icon icon="solar:thermometer-outline" class="text-info"></iconify-icon>
                                        @endif
                                        <span class="text-sm fw-medium">{{ $temperature }}°C</span>
                                    </div>
                                    @if($condition)
                                        <small class="text-muted">{{ ucwords(strtolower($condition)) }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">No weather data</span>
                                @endif
                            </td>
                            <td>
                                @if($system->last_seen)
                                    <div>
                                        <span class="text-sm fw-medium">{{ $system->last_seen->format('M j, Y') }}</span>
                                        <br><small class="text-muted">{{ $system->last_seen->format('H:i:s') }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 align-items-center">
                                    @if($system->api_enabled)
                                        <button class="btn btn-primary" 
                                                onclick="syncSystem('{{ $system->id }}')"
                                                title="Sync Data"
                                                style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <iconify-icon icon="solar:refresh-outline" style="font-size: 16px;"></iconify-icon>
                                        </button>
                                        <span class="badge bg-success-light text-success" 
                                              title="API Connected"
                                              style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                            <iconify-icon icon="solar:link-outline" style="font-size: 14px;"></iconify-icon>
                                        </span>
                                    @else
                                        <span class="badge bg-warning-light text-warning" 
                                              title="API Not Connected"
                                              style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                            <iconify-icon icon="solar:link-broken-outline" style="font-size: 14px;"></iconify-icon>
                                        </span>
                                    @endif
                                    <a href="/dashboard/system/{{ $system->id }}" 
                                       class="btn btn-primary" 
                                       title="View Details"
                                       style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                        <iconify-icon icon="solar:eye-outline" style="font-size: 16px;"></iconify-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <iconify-icon icon="solar:panel-outline" class="fs-2 mb-2"></iconify-icon>
                                    <p>No systems found. <a href="/dashboard/system">Add your first system</a></p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Alerts & Weather Summary -->
    <div class="col-xxl-4">
        <!-- Recent Alerts -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="fw-bold mb-0">Recent Alerts</h6>
            </div>
            <div class="card-body">
                @forelse($recentAlerts ?? [] as $alert)
                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                    <div>
                        <h6 class="mb-1 text-sm">{{ $alert->system->system_id ?? 'Unknown System' }}</h6>
                        <p class="mb-0 text-xs text-muted">{{ $alert->message ?? 'No message' }}</p>
                        <small class="text-muted">{{ $alert->created_at?->diffForHumans() ?? 'Unknown time' }}</small>
                    </div>
                    <span class="badge bg-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'warning' ? 'warning' : 'info') }}">
                        {{ ucfirst($alert->severity ?? 'info') }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <iconify-icon icon="solar:shield-check-outline" class="fs-2 mb-2"></iconify-icon>
                    <p class="mb-0">No recent alerts</p>
                </div>
                @endforelse
                
                @if(($recentAlerts ?? collect())->count() > 0)
                <div class="text-center mt-3">
                    <a href="/dashboard/alert" class="btn btn-outline-primary btn-sm">View All Alerts</a>
                </div>
                @endif
            </div>
        </div>

        <!-- System Status Summary -->
        <div class="card">
            <div class="card-header">
                <h6 class="fw-bold mb-0">System Status Summary</h6>
            </div>
            <div class="card-body">
                @php
                    $statusCounts = ($systemStatusCounts ?? []);
                    $totalSystemsCount = array_sum($statusCounts);
                @endphp
                
                @if($totalSystemsCount > 0)
                    @foreach(['active' => 'success', 'warning' => 'warning', 'critical' => 'danger', 'offline' => 'secondary', 'maintenance' => 'info'] as $status => $color)
                        @if(isset($statusCounts[$status]) && $statusCounts[$status] > 0)
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-12-px rounded-circle bg-{{ $color }}"></span>
                                <span class="text-sm fw-medium">{{ ucfirst($status) }}</span>
                            </div>
                            <span class="text-sm fw-semibold">{{ $statusCounts[$status] }}</span>
                        </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center text-muted py-3">
                        <iconify-icon icon="solar:database-outline" class="fs-2 mb-2"></iconify-icon>
                        <p class="mb-0">No system data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Initialize chart
        let chart;

        // Real-time data refresh function
        function refreshData() {
            const refreshBtn = document.querySelector('button[onclick="refreshData()"]');
            const originalContent = refreshBtn.innerHTML;
            
            // Show loading state
            refreshBtn.innerHTML = '<iconify-icon icon="solar:loading-outline" class="me-1"></iconify-icon>Loading...';
            refreshBtn.disabled = true;
            
            fetch('/dashboard/api/dashboard/realtime')
                .then(response => response.json())
                .then(data => {
                    // Update statistics
                    document.getElementById('totalSystems').textContent = data.total_systems;
                    document.getElementById('activeSystems').textContent = data.active_systems;
                    document.getElementById('energyToday').textContent = parseFloat(data.total_energy_today).toFixed(1);
                    document.getElementById('currentPower').textContent = parseFloat(data.total_power_current).toFixed(1);
                    document.getElementById('avgEfficiency').textContent = parseFloat(data.avg_efficiency).toFixed(1) + '%';
                    document.getElementById('totalLifetime').textContent = parseInt(data.total_lifetime).toLocaleString();
                    document.getElementById('apiConnected').textContent = data.api_enabled_systems;
                    document.getElementById('avgTemperature').textContent = parseFloat(data.avg_temperature).toFixed(1) + '°C';
                    document.getElementById('lastUpdated').textContent = 'Last updated: ' + data.last_updated;
                    
                    // Show success notification
                    showNotification('Data refreshed successfully', 'success');
                })
                .catch(error => {
                    console.error('Error refreshing data:', error);
                    showNotification('Failed to refresh data', 'error');
                })
                .finally(() => {
                    // Restore button state
                    refreshBtn.innerHTML = originalContent;
                    refreshBtn.disabled = false;
                });
        }

        // Auto-refresh every 5 minutes
        setInterval(refreshData, 300000);

        // Notification function
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
            
            console.log(`${type.toUpperCase()}: ${message}`);
        }

        // Sync individual system
        function syncSystem(systemId) {
            fetch(`/dashboard/solar-api/sync-system/${systemId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`System sync initiated successfully`, 'success');
                    // Refresh dashboard data after sync
                    setTimeout(refreshData, 2000);
                } else {
                    showNotification(`Failed to sync system: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                console.error('Error syncing system:', error);
                showNotification(`Error syncing system`, 'error');
            });
        }

        // Sync all systems
        function syncAllSystems() {
            const syncBtn = document.querySelector('button[onclick="syncAllSystems()"]');
            const originalContent = syncBtn.innerHTML;
            
            // Show loading state
            syncBtn.innerHTML = '<iconify-icon icon="solar:loading-outline" class="me-1"></iconify-icon>Syncing...';
            syncBtn.disabled = true;
            
            fetch('/dashboard/solar-api/sync-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Sync initiated for all systems', 'success');
                    // Refresh dashboard data after sync
                    setTimeout(refreshData, 3000);
                } else {
                    showNotification('Failed to sync all systems', 'error');
                }
            })
            .catch(error => {
                console.error('Error syncing all systems:', error);
                showNotification('Error syncing all systems', 'error');
            })
            .finally(() => {
                // Restore button state
                syncBtn.innerHTML = originalContent;
                syncBtn.disabled = false;
            });
        }

        // Production trends data (replace with actual data from controller)
        const productionTrendsData = @json($productionTrends ?? []);
        
        // Sample data for different time periods
        const data = {
            daily: {
                categories: productionTrendsData.map(item => item.date) || ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                series: [
                    {
                        name: 'Total Production',
                        data: productionTrendsData.map(item => parseFloat(item.total_energy || 0)) || [30, 40, 35, 50, 49, 60, 70]
                    },
                    {
                        name: 'Expected Output',
                        data: productionTrendsData.map(item => parseFloat(item.total_energy || 0) * 1.1) || [40, 45, 40, 55, 60, 65, 75]
                    }
                ]
            },
            weekly: {
                categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                series: [
                    {
                        name: 'Total Production',
                        data: [210, 280, 315, 400]
                    },
                    {
                        name: 'Expected Output',
                        data: [280, 300, 350, 420]
                    }
                ]
            },
            monthly: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                series: [
                    {
                        name: 'Total Production',
                        data: [1200, 1500, 1800, 2100, 2400, 2700]
                    },
                    {
                        name: 'Expected Output',
                        data: [1500, 1600, 1900, 2200, 2500, 2800]
                    }
                ]
            }
        };
		/*
                series: [
                    {
                        name: 'Total Production',
                        data: [30, 40, 35, 50, 49, 60, 70]
                    },
                    {
                        name: 'Expected Output',
                        data: [40, 45, 40, 55, 60, 65, 75]
                    }
                ]
            },
            weekly: {
                categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                series: [
                    {
                        name: 'Total Production',
                        data: [210, 280, 315, 400]
                    },
                    {
                        name: 'Expected Output',
                        data: [280, 300, 350, 420]
                    }
                ]
            },
            monthly: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                series: [
                    {
                        name: 'Total Production',
                        data: [1200, 1500, 1800, 2100, 2400, 2700]
                    },
                    {
                        name: 'Expected Output',
                        data: [1500, 1600, 1900, 2200, 2500, 2800]
                    }
                ]
            }
        };
		*/
        // Function to render chart
        function renderChart(period) {
            const chartData = data[period];

            const options = {
                series: chartData.series,
                chart: {
                    type: 'area',
                    stacked: true,
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                    sparkline: {
                        enabled: false
                    },
                    parentHeightOffset: 0,
                },
                colors: ['#2B3674', '#D9D9D9'], // Matches your dot colors
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        inverseColors: false,
                        opacityFrom: 0.45,
                        opacityTo: 0.1,
                        stops: [0, 100]
                    }
                },
                legend: {
                    show: false // We're using custom legend in HTML
                },
                grid: {
                    show: true,
                    borderColor: '#e5e7eb',
                    strokeDashArray: 4,
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    }
                },
                xaxis: {
                    categories: chartData.categories,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#6b7280',
                            fontSize: '12px',
                            fontFamily: 'inherit'
                        }
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6b7280',
                            fontSize: '12px',
                            fontFamily: 'inherit'
                        },
                        formatter: function (val) {
                            return val.toFixed(0);
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'inherit'
                    },
                    y: {
                        formatter: function (val) {
                            return val + " units";
                        }
                    }
                }
            };

            if (chart) {
                chart.updateOptions(options);
            } else {
                chart = new ApexCharts(document.querySelector("#systemPerformanceAreaChart"), options);
                chart.render();
            }
        }

        // Initialize with daily data
        document.addEventListener('DOMContentLoaded', function() {
            renderChart('daily');

            // Add event listener to select dropdown
            document.getElementById('periodSelect').addEventListener('change', function() {
                const period = this.value;
                renderChart(period);
            });
        });
    </script>

    <!-- Enhanced Temperature Chart Script -->
    <script>
        // Temperature chart for weather dashboard with period switching
        let temperatureChart = null;
        
        // Sample data for different periods
        const weatherData = {
            daily: {
                labels: ['6AM', '9AM', '12PM', '3PM', '6PM', '9PM', '12AM', '3AM'],
                temperatures: [14, 18, 24, 28, 26, 22, 18, 15],
                humidity: [85, 78, 65, 58, 62, 72, 80, 88]
            },
            weekly: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                temperatures: [22, 25, 23, 26, 24, 27, 25],
                humidity: [70, 65, 72, 68, 71, 66, 69]
            },
            monthly: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                temperatures: [23, 25, 22, 24],
                humidity: [68, 71, 75, 70]
            }
        };
        
        function renderTemperatureChart(period = 'daily') {
            const canvas = document.getElementById('temperatureChart');
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            const data = weatherData[period];
            
            // Responsive canvas sizing
            const container = canvas.parentElement;
            canvas.width = container.offsetWidth - 32; // Account for padding
            canvas.height = 240;
            
            const width = canvas.width;
            const height = canvas.height;
            const padding = 40;
            const chartWidth = width - (padding * 2);
            const chartHeight = height - (padding * 2);
            
            // Clear canvas
            ctx.clearRect(0, 0, width, height);
            
            // Calculate temperature points
            const maxTemp = Math.max(...data.temperatures);
            const minTemp = Math.min(...data.temperatures);
            const tempRange = maxTemp - minTemp || 1;
            
            const points = data.temperatures.map((temp, index) => {
                const x = padding + (index * chartWidth / (data.temperatures.length - 1));
                const y = padding + chartHeight - ((temp - minTemp) / tempRange * chartHeight);
                return { x, y, temp };
            });
            
            // Draw grid lines
            ctx.strokeStyle = '#e5e7eb';
            ctx.lineWidth = 1;
            ctx.setLineDash([5, 5]);
            
            // Horizontal grid lines
            for (let i = 0; i <= 5; i++) {
                const y = padding + (i * chartHeight / 5);
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(width - padding, y);
                ctx.stroke();
            }
            
            // Vertical grid lines
            data.labels.forEach((label, index) => {
                const x = padding + (index * chartWidth / (data.labels.length - 1));
                ctx.beginPath();
                ctx.moveTo(x, padding);
                ctx.lineTo(x, height - padding);
                ctx.stroke();
            });
            
            ctx.setLineDash([]);
            
            // Draw gradient background under the line
            const gradient = ctx.createLinearGradient(0, padding, 0, height - padding);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
            gradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.15)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');
            
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);
            points.forEach(point => ctx.lineTo(point.x, point.y));
            ctx.lineTo(points[points.length - 1].x, height - padding);
            ctx.lineTo(points[0].x, height - padding);
            ctx.closePath();
            ctx.fillStyle = gradient;
            ctx.fill();
            
            // Draw main temperature line
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);
            points.forEach(point => ctx.lineTo(point.x, point.y));
            ctx.strokeStyle = '#3b82f6';
            ctx.lineWidth = 3;
            ctx.stroke();
            
            // Draw data points
            points.forEach(point => {
                ctx.beginPath();
                ctx.arc(point.x, point.y, 5, 0, 2 * Math.PI);
                ctx.fillStyle = '#ffffff';
                ctx.fill();
                ctx.strokeStyle = '#3b82f6';
                ctx.lineWidth = 2;
                ctx.stroke();
            });
            
            // Draw temperature labels
            ctx.font = 'bold 12px system-ui';
            ctx.textAlign = 'center';
            ctx.fillStyle = '#1f2937';
            
            points.forEach((point, index) => {
                // Temperature value above point
                const bgWidth = 32;
                const bgHeight = 20;
                const bgX = point.x - bgWidth / 2;
                const bgY = point.y - 35;
                
                // Background for temperature label
                ctx.fillStyle = '#3b82f6';
                ctx.roundRect(bgX, bgY, bgWidth, bgHeight, 10);
                ctx.fill();
                
                // Temperature text
                ctx.fillStyle = '#ffffff';
                ctx.fillText(point.temp + '°', point.x, point.y - 22);
            });
            
            // Draw time/period labels
            ctx.font = '11px system-ui';
            ctx.fillStyle = '#6b7280';
            ctx.textAlign = 'center';
            
            data.labels.forEach((label, index) => {
                const x = padding + (index * chartWidth / (data.labels.length - 1));
                ctx.fillText(label, x, height - 10);
            });
            
            // Draw y-axis temperature scale
            ctx.font = '10px system-ui';
            ctx.fillStyle = '#9ca3af';
            ctx.textAlign = 'right';
            
            for (let i = 0; i <= 5; i++) {
                const temp = Math.round(minTemp + (tempRange * i / 5));
                const y = height - padding - (i * chartHeight / 5);
                ctx.fillText(temp + '°C', padding - 10, y + 3);
            }
        }
        
        // Add support for CanvasRenderingContext2D.roundRect if not available
        if (!CanvasRenderingContext2D.prototype.roundRect) {
            CanvasRenderingContext2D.prototype.roundRect = function(x, y, width, height, radius) {
                if (width < 2 * radius) radius = width / 2;
                if (height < 2 * radius) radius = height / 2;
                this.beginPath();
                this.moveTo(x + radius, y);
                this.arcTo(x + width, y, x + width, y + height, radius);
                this.arcTo(x + width, y + height, x, y + height, radius);
                this.arcTo(x, y + height, x, y, radius);
                this.arcTo(x, y, x + width, y, radius);
                this.closePath();
                return this;
            };
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            let currentPeriod = 'daily';
            
            // Initialize chart
            renderTemperatureChart(currentPeriod);
            
            // Weather period button handlers
            const periodButtons = document.querySelectorAll('.weather-period-btn');
            periodButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    periodButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Update chart
                    currentPeriod = this.dataset.period;
                    renderTemperatureChart(currentPeriod);
                });
            });
            
            // Redraw chart on window resize
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    renderTemperatureChart(currentPeriod);
                }, 250);
            });
        });
    </script>
    
    <style>
        /* Custom scrollbar for table */
        .custom-scrollbar::-webkit-scrollbar {
            height: 12px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Custom background colors for card icons */
        .bg-primary-100 {
            background-color: rgba(67, 24, 255, 0.1) !important;
        }
        
        .bg-success-100 {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }
        
        .bg-warning-100 {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }
        
        .bg-info-100 {
            background-color: rgba(23, 162, 184, 0.1) !important;
        }
        
        .border-primary-200 {
            border-color: rgba(67, 24, 255, 0.2) !important;
        }
        
        .border-success-200 {
            border-color: rgba(40, 167, 69, 0.2) !important;
        }
        
        .border-warning-200 {
            border-color: rgba(255, 193, 7, 0.2) !important;
        }
        
        .border-info-200 {
            border-color: rgba(23, 162, 184, 0.2) !important;
        }
    </style>
@endpush
