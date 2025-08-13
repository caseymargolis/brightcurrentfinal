<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sync solar data every 15 minutes during daylight hours (6 AM - 8 PM)
        $schedule->command('solar:sync-all')
                ->cron('*/15 6-20 * * *')
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/solar-sync.log'));

        // Process queue jobs every 5 minutes
        $schedule->command('solar:queue-work')
                ->everyFiveMinutes()
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/queue-processing.log'));

        // Clean up old production data (keep last 90 days)
        $schedule->call(function () {
            \App\Models\ProductionData::where('created_at', '<', now()->subDays(90))->delete();
        })->weekly()->sundays()->at('02:00');

        // Generate daily reports
        $schedule->command('solar:daily-report')
                ->dailyAt('23:30')
                ->appendOutputTo(storage_path('logs/daily-reports.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
