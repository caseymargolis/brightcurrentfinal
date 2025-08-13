<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\EnphaseApiService;

class EnphaseAuthenticate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enphase:authenticate {--username=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Authenticate with Enphase API using OAuth 2.0';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->option('username');
        $password = $this->option('password');

        if (!$username) {
            $username = $this->ask('Enter your Enphase Enlighten username/email');
        }

        if (!$password) {
            $password = $this->secret('Enter your Enphase Enlighten password');
        }

        if (empty($username) || empty($password)) {
            $this->error('Username and password are required for Enphase OAuth authentication.');
            return 1;
        }

        $this->info('🔐 Authenticating with Enphase API...');

        $enphaseService = new EnphaseApiService();
        $result = $enphaseService->authenticate($username, $password);

        if ($result['success']) {
            $this->info('✅ Authentication successful!');
            $this->line("Access Token: " . substr($result['access_token'], 0, 20) . "...");
            $this->line("Token Type: " . ($result['token_type'] ?? 'bearer'));
            $this->line("Expires In: " . ($result['expires_in'] ?? 'unknown') . " seconds");
            
            if (!empty($result['refresh_token'])) {
                $this->line("Refresh Token: Available");
            }

            $this->newLine();
            $this->info('🧪 Testing API connection...');
            
            $testResult = $enphaseService->testConnection();
            if ($testResult['success']) {
                $this->info('✅ ' . $testResult['message']);
            } else {
                $this->error('❌ ' . $testResult['message']);
            }

        } else {
            $this->error('❌ Authentication failed: ' . $result['message']);
            
            if (isset($result['status_code'])) {
                $this->line("HTTP Status: " . $result['status_code']);
            }

            $this->newLine();
            $this->warn('💡 Troubleshooting tips:');
            $this->line('• Verify your Enphase Enlighten account credentials');
            $this->line('• Ensure your account has the necessary permissions');
            $this->line('• Check that your OAuth credentials (Client ID/Secret) are correct');
            $this->line('• Make sure you have an active Enphase Partner account');

            return 1;
        }

        return 0;
    }
}