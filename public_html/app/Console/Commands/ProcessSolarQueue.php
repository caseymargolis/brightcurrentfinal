<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessSolarQueue extends Command
{
    protected $signature = 'solar:queue-work';
    protected $description = 'Process solar data sync queue jobs';

    public function handle()
    {
        $this->info('📋 Processing solar data sync queue...');
        
        // Process queue jobs for solar data synchronization
        $this->call('queue:work', [
            '--once' => true,
            '--timeout' => 300,
            '--tries' => 3
        ]);

        return 0;
    }
}