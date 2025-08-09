<?php

namespace App\Console\Commands;

use App\Jobs\SyncSolarDataJob;
use App\Models\System;
use Illuminate\Console\Command;

class SyncSolarData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solar:sync {--system= : Sync specific system by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync solar production data from APIs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $systemId = $this->option('system');

        if ($systemId) {
            $system = System::find($systemId);
            if (!$system) {
                $this->error("System with ID {$systemId} not found.");
                return 1;
            }

            if (!$system->api_enabled) {
                $this->error("System {$system->system_id} does not have API enabled.");
                return 1;
            }

            $this->info("Syncing data for system: {$system->system_id}");
            SyncSolarDataJob::dispatch($systemId);
            $this->info("Sync job dispatched for system {$systemId}");
        } else {
            $apiEnabledCount = System::apiEnabled()->count();
            $this->info("Syncing data for {$apiEnabledCount} API-enabled systems...");
            SyncSolarDataJob::dispatch();
            $this->info("Sync job dispatched for all systems");
        }

        return 0;
    }
}
