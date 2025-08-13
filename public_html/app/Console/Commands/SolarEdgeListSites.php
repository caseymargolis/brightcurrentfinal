<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\SolarEdgeApiService;

class SolarEdgeListSites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solaredge:list-sites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all accessible SolarEdge sites with their IDs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Retrieving SolarEdge sites...');

        $solarEdgeService = new SolarEdgeApiService();
        $sites = $solarEdgeService->getSites();

        if (empty($sites)) {
            $this->warn('⚠️  No sites found. This could mean:');
            $this->line('• Your API key is invalid or expired');
            $this->line('• Your account doesn\'t have access to any sites');
            $this->line('• There\'s a connection issue with the SolarEdge API');
            $this->newLine();
            
            $this->info('🧪 Running connection test...');
            $testResult = $solarEdgeService->testConnection();
            if ($testResult['success']) {
                $this->info('✅ ' . $testResult['message']);
            } else {
                $this->error('❌ ' . $testResult['message']);
                if (isset($testResult['guidance'])) {
                    $this->newLine();
                    $this->warn('💡 ' . $testResult['guidance']);
                }
            }
            
            return 1;
        }

        $this->info("✅ Found " . count($sites) . " accessible sites:");
        $this->newLine();

        $headers = ['Site ID', 'Name', 'Status', 'Peak Power (W)', 'Installation Date', 'Location'];
        $rows = [];

        foreach ($sites as $site) {
            $rows[] = [
                $site['id'] ?? 'N/A',
                $site['name'] ?? 'Unnamed',
                $site['status'] ?? 'Unknown',
                isset($site['peakPower']) ? number_format($site['peakPower']) : 'N/A',
                isset($site['installationDate']) ? date('Y-m-d', strtotime($site['installationDate'])) : 'N/A',
                $this->formatLocation($site)
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        $this->info('💡 Usage Tips:');
        $this->line('• Use the Site ID to configure your systems in the application');
        $this->line('• You can get detailed information for any site using: php artisan solaredge:site-details {site_id}');
        $this->line('• Make sure to add these Site IDs to your solar systems configuration');

        return 0;
    }

    /**
     * Format location information from site data
     */
    private function formatLocation(array $site): string
    {
        $location = [];
        
        if (!empty($site['location']['city'])) {
            $location[] = $site['location']['city'];
        }
        
        if (!empty($site['location']['state'])) {
            $location[] = $site['location']['state'];
        }
        
        if (!empty($site['location']['country'])) {
            $location[] = $site['location']['country'];
        }

        return !empty($location) ? implode(', ', $location) : 'N/A';
    }
}