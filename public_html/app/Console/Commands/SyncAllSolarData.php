<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System;
use App\Jobs\SyncSolarDataJob;

class SyncAllSolarData extends Command
{
    protected $signature = 'solar:sync-all';
    protected $description = 'Sync solar data for all systems';

    public function handle()
    {
        $this->info('🔄 Starting solar data sync for all systems...');

        $systems = System::where('api_enabled', true)->get();
        
        if ($systems->isEmpty()) {
            $this->warn('No API-enabled systems found.');
            return 0;
        }

        $this->info("Found {$systems->count()} API-enabled systems");

        foreach ($systems as $system) {
            $this->line("Dispatching sync job for: {$system->system_id} ({$system->manufacturer})");
            SyncSolarDataJob::dispatch($system->id);
        }

        $this->info('✅ All sync jobs dispatched successfully!');
        $this->line('Jobs will be processed by the queue worker.');

        return 0;
    }
}