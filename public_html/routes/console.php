<?php

use App\Jobs\SyncSolarDataJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule solar data sync every 15 minutes
Schedule::job(new SyncSolarDataJob())->everyFifteenMinutes();

// Schedule weather data sync every 30 minutes (independent of solar data)
Schedule::command('solar:sync-weather')->everyThirtyMinutes()
    ->description('Sync weather data for all solar systems')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule daily report generation (if needed)
// Schedule::command('reports:generate-daily')->dailyAt('06:00');
