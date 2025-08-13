<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\SolarEdgeApiService;

class SolarEdgeSiteDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solaredge:site-details {site_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get detailed information for a specific SolarEdge site';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $siteId = $this->argument('site_id');

        if (empty($siteId)) {
            $this->error('Site ID is required. Use: php artisan solaredge:site-details {site_id}');
            return 1;
        }

        $this->info("🔍 Retrieving details for SolarEdge site: {$siteId}");

        $solarEdgeService = new SolarEdgeApiService();
        $details = $solarEdgeService->getSiteDetails($siteId);

        if (empty($details)) {
            $this->error("❌ Could not retrieve details for site {$siteId}");
            $this->line('This could mean:');
            $this->line('• The site ID doesn\'t exist or is incorrect');
            $this->line('• Your API key doesn\'t have access to this site');
            $this->line('• There\'s a connection issue with the SolarEdge API');
            return 1;
        }

        $this->info("✅ Site Details for {$siteId}:");
        $this->newLine();

        // Display basic information
        $this->displaySection('Basic Information', [
            'Site ID' => $details['id'] ?? 'N/A',
            'Name' => $details['name'] ?? 'N/A',
            'Account ID' => $details['accountId'] ?? 'N/A',
            'Status' => $details['status'] ?? 'N/A',
            'Peak Power (W)' => isset($details['peakPower']) ? number_format($details['peakPower']) : 'N/A',
            'Installation Date' => isset($details['installationDate']) ? date('Y-m-d', strtotime($details['installationDate'])) : 'N/A',
            'Currency' => $details['currency'] ?? 'N/A',
            'Notes' => $details['notes'] ?? 'None',
        ]);

        // Display location information
        if (isset($details['location'])) {
            $location = $details['location'];
            $this->displaySection('Location', [
                'Country' => $location['country'] ?? 'N/A',
                'State' => $location['state'] ?? 'N/A',
                'City' => $location['city'] ?? 'N/A',
                'Address' => $location['address'] ?? 'N/A',
                'Address 2' => $location['address2'] ?? 'N/A',
                'ZIP Code' => $location['zip'] ?? 'N/A',
                'Time Zone' => $location['timeZone'] ?? 'N/A',
            ]);
        }

        // Display primary module information
        if (isset($details['primaryModule'])) {
            $module = $details['primaryModule'];
            $this->displaySection('Primary Module', [
                'Manufacturer' => $module['manufacturerName'] ?? 'N/A',
                'Model' => $module['modelName'] ?? 'N/A',
                'Maximum Power (W)' => $module['maximumPower'] ?? 'N/A',
                'Temperature Coefficient' => $module['temperatureCoef'] ?? 'N/A',
            ]);
        }

        // Display inverter information
        if (isset($details['inverters']) && !empty($details['inverters'])) {
            $this->line('<info>🔌 Inverters:</info>');
            foreach ($details['inverters'] as $inverter) {
                $this->line("  • {$inverter['name']} (Serial: {$inverter['serialNumber']})");
            }
            $this->newLine();
        }

        // Display public settings
        if (isset($details['publicSettings'])) {
            $publicSettings = $details['publicSettings'];
            $this->displaySection('Public Settings', [
                'Public' => $publicSettings['isPublic'] ? 'Yes' : 'No',
                'Name' => $publicSettings['name'] ?? 'N/A',
            ]);
        }

        return 0;
    }

    /**
     * Display a section with key-value pairs
     */
    private function displaySection(string $title, array $data): void
    {
        $this->line("<info>{$title}:</info>");
        foreach ($data as $key => $value) {
            if ($value !== 'N/A' && $value !== '' && $value !== null) {
                $this->line("  {$key}: {$value}");
            }
        }
        $this->newLine();
    }
}