<?php

namespace App\Console\Commands;

use App\Services\Api\EnphaseOAuthService;
use App\Services\Api\TeslaOAuthService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestOAuthFlow extends Command
{
    protected $signature = 'oauth:test {provider : The OAuth provider (enphase|tesla)}';
    protected $description = 'Test OAuth flow by checking callback URL accessibility';

    public function handle()
    {
        $provider = $this->argument('provider');
        
        switch ($provider) {
            case 'enphase':
                return $this->testEnphaseOAuth();
            case 'tesla':
                return $this->testTeslaOAuth();
            default:
                $this->error('Invalid provider. Use: enphase or tesla');
                return 1;
        }
    }

    private function testEnphaseOAuth()
    {
        $this->info('🔆 Testing Enphase OAuth Flow');
        
        $oauthService = new EnphaseOAuthService();
        $authData = $oauthService->getAuthorizationUrl();
        
        $this->info("✅ Authorization URL generated successfully:");
        $this->line($authData['auth_url']);
        
        // Test callback URL accessibility
        $callbackUrl = 'http://localhost:8001/api/enphase/callback';
        try {
            $response = Http::timeout(5)->get($callbackUrl, ['test' => 'true']);
            if ($response->status() == 400) { // Expected since no code provided
                $this->info("✅ Callback URL is accessible");
            } else {
                $this->warn("⚠️ Callback URL returned status: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("❌ Callback URL not accessible: " . $e->getMessage());
        }
        
        return 0;
    }

    private function testTeslaOAuth()
    {
        $this->info('🚗 Testing Tesla OAuth Flow');
        
        $oauthService = new TeslaOAuthService();
        $authData = $oauthService->getAuthorizationUrl();
        
        $this->info("✅ Authorization URL generated successfully:");
        $this->line($authData['auth_url']);
        
        // Test callback URL accessibility
        $callbackUrl = 'http://localhost:8001/api/tesla/callback';
        try {
            $response = Http::timeout(5)->get($callbackUrl, ['test' => 'true']);
            if ($response->status() == 400) { // Expected since no code provided
                $this->info("✅ Callback URL is accessible");
            } else {
                $this->warn("⚠️ Callback URL returned status: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("❌ Callback URL not accessible: " . $e->getMessage());
        }
        
        return 0;
    }
}