<?php

namespace App\Console\Commands;

use App\Services\Api\EnphaseOAuthService;
use App\Services\Api\TeslaOAuthService;
use Illuminate\Console\Command;

class ProcessOAuthCode extends Command
{
    protected $signature = 'oauth:process-code {provider} {code}';
    protected $description = 'Process OAuth authorization code for a provider';

    public function handle()
    {
        $provider = $this->argument('provider');
        $code = $this->argument('code');
        
        $this->info("🔐 Processing OAuth code for {$provider}");
        $this->info("Code: {$code}");
        
        try {
            switch (strtolower($provider)) {
                case 'enphase':
                    return $this->processEnphaseCode($code);
                    
                case 'tesla':
                    return $this->processTeslaCode($code);
                    
                default:
                    $this->error("❌ Unknown provider: {$provider}");
                    $this->error("Available providers: enphase, tesla");
                    return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error processing OAuth code: " . $e->getMessage());
            return 1;
        }
    }

    private function processEnphaseCode($code)
    {
        $this->info("🔆 Processing Enphase OAuth code...");
        
        $oauthService = new EnphaseOAuthService();
        $result = $oauthService->exchangeCodeForTokens($code);
        
        if ($result['success']) {
            $this->info("✅ Enphase authentication successful!");
            $this->info("✅ Access token obtained and cached");
            $this->line("You can now test the Enphase API with: php artisan solar:test-apis");
            return 0;
        } else {
            $this->error("❌ Token exchange failed: " . $result['message']);
            return 1;
        }
    }

    private function processTeslaCode($code)
    {
        $this->info("🚗 Processing Tesla OAuth code...");
        
        $oauthService = new TeslaOAuthService();
        $tokens = $oauthService->exchangeCodeForTokens($code);
        
        $this->info("✅ Tesla authentication successful!");
        $this->info("✅ Access token obtained and cached");
        $this->line("You can now test the Tesla API with: php artisan solar:test-apis");
        return 0;
    }
}