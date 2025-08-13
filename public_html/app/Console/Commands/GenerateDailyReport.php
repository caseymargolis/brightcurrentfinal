<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SolarApiService;
use App\Models\System;
use App\Models\ProductionData;
use Carbon\Carbon;

class GenerateDailyReport extends Command
{
    protected $signature = 'solar:daily-report {--date= : Specific date (Y-m-d format)}';
    protected $description = 'Generate daily solar production report';

    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::yesterday();
        
        $this->info("📊 Generating daily report for {$date->format('Y-m-d')}...");

        $systems = System::all();
        $totalSystems = $systems->count();
        $activeSystems = $systems->where('status', 'active')->count();

        // Get production data for the date
        $productionData = ProductionData::whereDate('date', $date)->get();
        
        $totalEnergy = $productionData->sum('energy_today');
        $avgEfficiency = $productionData->avg('efficiency');
        $avgTemperature = $productionData->avg('weather_temperature');

        // Systems with data vs without data
        $systemsWithData = $productionData->pluck('system_id')->unique()->count();
        $systemsWithoutData = $totalSystems - $systemsWithData;

        $this->newLine();
        $this->info("=== DAILY SOLAR REPORT - {$date->format('M j, Y')} ===");
        $this->newLine();
        
        $this->line("🏗️  SYSTEM STATUS:");
        $this->line("   Total Systems: {$totalSystems}");
        $this->line("   Active Systems: {$activeSystems}");
        $this->line("   Systems with Data: {$systemsWithData}");
        $this->line("   Systems without Data: {$systemsWithoutData}");
        $this->newLine();

        $this->line("⚡ ENERGY PRODUCTION:");
        $this->line("   Total Energy: " . number_format($totalEnergy, 2) . " kWh");
        $this->line("   Average Efficiency: " . number_format($avgEfficiency, 1) . "%");
        $this->newLine();

        $this->line("🌤️  WEATHER CONDITIONS:");
        $this->line("   Average Temperature: " . number_format($avgTemperature, 1) . "°C");
        $this->newLine();

        // Top performing systems
        $topPerformers = $productionData->sortByDesc('energy_today')->take(3);
        if ($topPerformers->isNotEmpty()) {
            $this->line("🏆 TOP PERFORMING SYSTEMS:");
            foreach ($topPerformers as $index => $data) {
                $system = System::find($data->system_id);
                $position = $index + 1;
                $this->line("   {$position}. {$system->system_id} - " . number_format($data->energy_today, 2) . " kWh");
            }
            $this->newLine();
        }

        // Systems with low performance or issues
        $lowPerformers = $productionData->where('efficiency', '<', 70)->sortBy('efficiency');
        if ($lowPerformers->isNotEmpty()) {
            $this->line("⚠️  SYSTEMS NEEDING ATTENTION:");
            foreach ($lowPerformers->take(5) as $data) {
                $system = System::find($data->system_id);
                $this->line("   {$system->system_id} - Efficiency: " . number_format($data->efficiency, 1) . "%");
            }
            $this->newLine();
        }

        $this->info("✅ Daily report generated successfully!");
        
        // Log the report
        \Log::info("Daily solar report generated", [
            'date' => $date->format('Y-m-d'),
            'total_systems' => $totalSystems,
            'total_energy' => $totalEnergy,
            'avg_efficiency' => $avgEfficiency
        ]);

        return 0;
    }
}