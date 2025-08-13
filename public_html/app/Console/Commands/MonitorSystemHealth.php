<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System;
use App\Models\ProductionData;
use App\Models\Alert;
use Carbon\Carbon;

class MonitorSystemHealth extends Command
{
    protected $signature = 'solar:monitor-health';
    protected $description = 'Monitor system health and generate alerts for issues';

    public function handle()
    {
        $this->info('🔍 Monitoring system health...');

        $systems = System::where('api_enabled', true)->get();
        $alertsGenerated = 0;

        foreach ($systems as $system) {
            $this->checkSystemHealth($system, $alertsGenerated);
        }

        $this->info("✅ Health monitoring complete. Generated {$alertsGenerated} alerts.");
        
        return 0;
    }

    private function checkSystemHealth(System $system, &$alertsGenerated)
    {
        $this->line("Checking system: {$system->system_id}");

        // Check 1: No data in last 24 hours
        $lastData = ProductionData::where('system_id', $system->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->latest()
            ->first();

        if (!$lastData) {
            $this->createAlert($system, 'no_data', 'No production data received in the last 24 hours', 'high');
            $alertsGenerated++;
        }

        // Check 2: Low energy production (if we have recent data)
        if ($lastData) {
            $avgEnergyLast7Days = ProductionData::where('system_id', $system->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->avg('energy_today');

            if ($avgEnergyLast7Days > 0 && $lastData->energy_today < ($avgEnergyLast7Days * 0.5)) {
                $this->createAlert($system, 'low_production', 
                    "Energy production significantly below average: {$lastData->energy_today} kWh vs {$avgEnergyLast7Days} kWh average", 
                    'medium');
                $alertsGenerated++;
            }

            // Check 3: Low efficiency
            if ($lastData->efficiency && $lastData->efficiency < 60) {
                $this->createAlert($system, 'low_efficiency', 
                    "System efficiency below threshold: {$lastData->efficiency}%", 
                    'medium');
                $alertsGenerated++;
            }

            // Check 4: System offline (current power = 0 during daylight hours)
            $isDaylight = now()->hour >= 6 && now()->hour <= 20;
            if ($isDaylight && $lastData->power_current == 0) {
                $this->createAlert($system, 'system_offline', 
                    'System appears to be offline during daylight hours', 
                    'high');
                $alertsGenerated++;
            }
        }

        // Check 5: System status warnings
        if ($system->status === 'warning') {
            $this->createAlert($system, 'system_warning', 
                'System marked with warning status', 
                'medium');
            $alertsGenerated++;
        }
    }

    private function createAlert(System $system, $type, $message, $severity)
    {
        // Check if we already have this alert in the last 24 hours to avoid spam
        $existingAlert = Alert::where('system_id', $system->id)
            ->where('alert_type', $type)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();

        if (!$existingAlert) {
            Alert::create([
                'system_id' => $system->id,
                'alert_type' => $type,
                'message' => $message,
                'severity' => $severity,
                'status' => 'open',
                'created_at' => now()
            ]);

            $this->warn("   🚨 Alert created: {$message}");
        }
    }
}