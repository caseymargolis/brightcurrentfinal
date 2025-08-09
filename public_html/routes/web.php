<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceScheduleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SolarApiController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard routes
Route::group(['prefix' => 'dashboard','middleware' => 'auth'], function() {
    Route::get('cache-clear', function () {
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        return redirect()->back()->with(['msg' => 'Cache Cleared', 'type' => 'success']);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // System routes - Adjusted order for specific routes first
    Route::get('/system/create', [SystemController::class, 'create'])->name('system.create');
    Route::post('/system', [SystemController::class, 'store'])->name('system.store');
    Route::get('/system/{id}/edit', [SystemController::class, 'edit'])->name('system.edit');
    Route::put('/system/{id}', [SystemController::class, 'update'])->name('system.update');
    Route::delete('/system/{id}', [SystemController::class, 'destroy'])->name('system.destroy');
    Route::get('/system/{id}', [SystemController::class, 'show'])->name('system.show');
    Route::get('/system', [SystemController::class, 'index'])->name('system.index');

    // Use Route::resource for standard CRUD operations for Alerts
    Route::resource('alert', AlertController::class);

    Route::resource('service-schedules', ServiceScheduleController::class);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/production-intensity-data', [ReportController::class, 'getProductionIntensityData'])->name('reports.productionIntensityData');
    Route::post('/reports/export', [ReportController::class, 'exportReport'])->name('reports.export');

    // Settings routes - includes Users and all API Integrations
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');

        // SolarEdge API
        Route::post('/update-solaredge', [SettingController::class, 'updateSolarEdgeSettings'])->name('updateSolarEdge');
        Route::post('/test-solaredge-connection', [SettingController::class, 'testSolarEdgeConnection'])->name('testSolarEdgeConnection');

        // Housecall API
        Route::post('/update-housecall', [SettingController::class, 'updateHousecallSettings'])->name('updateHousecall');
        Route::post('/test-housecall-connection', [SettingController::class, 'testHousecallConnection'])->name('testHousecallConnection');

        // Weather API
        Route::post('/update-weather', [SettingController::class, 'updateWeatherSettings'])->name('updateWeather');
        Route::post('/test-weather-connection', [SettingController::class, 'testWeatherConnection'])->name('testWeatherConnection');

        // Notifications
        Route::post('/update-notifications', [SettingController::class, 'updateNotificationSettings'])->name('updateNotifications');
    });

    // Solar API Management routes
    Route::group(['prefix' => 'solar-api', 'as' => 'solar-api.'], function () {
        Route::get('/', [SolarApiController::class, 'index'])->name('index');
        Route::post('/test-connections', [SolarApiController::class, 'testConnections'])->name('testConnections');
        Route::post('/sync-all', [SolarApiController::class, 'syncAll'])->name('syncAll');
        Route::post('/sync-system/{system}', [SolarApiController::class, 'syncSystem'])->name('syncSystem');
        Route::put('/update-system/{system}', [SolarApiController::class, 'updateSystemApi'])->name('updateSystem');
        Route::get('/available-systems', [SolarApiController::class, 'getAvailableSystems'])->name('availableSystems');
    });

    // AJAX routes for real-time data
    Route::get('/api/dashboard/realtime', [DashboardController::class, 'getRealTimeData'])->name('dashboard.realtime');

    Route::resource('users', UserController::class);


});

Route::middleware('auth')->group(function () {

});

require __DIR__.'/auth.php';
