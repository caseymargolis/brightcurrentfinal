<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\TeslaOAuthService;

class TeslaAuthenticate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tesla:authenticate {--show-status : Show current authentication status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Authenticate with Tesla API using OAuth 2.0';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oauthService = new TeslaOAuthService();

        if ($this->option('show-status')) {
            $this->showAuthenticationStatus($oauthService);
            return;
        }

        $this->line('🚗 Tesla API OAuth 2.0 Authentication');
        $this->line('');

        if (!$oauthService->hasValidCredentials()) {
            $this->error('❌ Tesla OAuth credentials not configured!');
            $this->line('');
            $this->line('Please add the following to your .env file:');
            $this->line('TESLA_CLIENT_ID=your_client_id_here');
            $this->line('TESLA_CLIENT_SECRET=your_client_secret_here');
            $this->line('');
            $this->line('Get your credentials from: https://developer.tesla.com/en_US/dashboard');
            return 1;
        }

        // Generate authorization URL
        $authData = $oauthService->getAuthorizationUrl();
        
        $this->line('🔐 To authenticate with Tesla API, please:');
        $this->line('');
        $this->line('1. Open this URL in your browser:');
        $this->info($authData['auth_url']);
        $this->line('');
        $this->line('2. Log in with your Tesla account and authorize the application');
        $this->line('3. After authorization, you will be redirected to a URL like:');
        $this->line('   http://localhost:8001/api/tesla/callback?code=AUTHORIZATION_CODE&state=' . $authData['state']);
        $this->line('');
        $this->line('4. Copy the AUTHORIZATION_CODE from the URL and paste it below:');
        $this->line('');

        $code = $this->ask('Enter the authorization code');

        if (empty($code)) {
            $this->error('❌ No authorization code provided');
            return 1;
        }

        try {
            $this->line('🔄 Exchanging authorization code for tokens...');
            $tokens = $oauthService->exchangeCodeForTokens($code);
            
            $this->line('');
            $this->info('✅ Tesla API authentication successful!');
            $this->line('');
            $this->line('Token Information:');
            $this->line('- Access Token: ' . substr($tokens['access_token'], 0, 20) . '...');
            $this->line('- Token Type: ' . $tokens['token_type']);
            $this->line('- Expires At: ' . date('Y-m-d H:i:s', $tokens['expires_at']));
            $this->line('- Refresh Token: Available');
            $this->line('');
            $this->info('🎉 You can now use Tesla API endpoints!');
            $this->line('');
            $this->line('Test the connection with: php artisan solar:test-apis');

        } catch (\Exception $e) {
            $this->error('❌ Authentication failed: ' . $e->getMessage());
            $this->line('');
            $this->line('Please try again or check:');
            $this->line('- The authorization code is correct and not expired');
            $this->line('- Your client credentials are valid');
            $this->line('- Your redirect URI matches Tesla app configuration');
            return 1;
        }

        return 0;
    }

    /**
     * Show current authentication status
     */
    private function showAuthenticationStatus(TeslaOAuthService $oauthService)
    {
        $this->line('🚗 Tesla API Authentication Status');
        $this->line('');

        $status = $oauthService->getAuthorizationStatus();

        $this->line('Credentials Configuration:');
        $this->line('- Client ID: ' . ($status['credentials_configured'] ? '✅ Configured' : '❌ Missing'));
        $this->line('- Client Secret: ' . ($status['credentials_configured'] ? '✅ Configured' : '❌ Missing'));
        $this->line('');

        $this->line('Token Status:');
        $this->line('- Access Token: ' . ($status['access_token_cached'] ? '✅ Cached' : '❌ Not Available'));
        $this->line('- Refresh Token: ' . ($status['refresh_token_cached'] ? '✅ Cached' : '❌ Not Available'));
        $this->line('- Token Valid: ' . ($status['token_valid'] ? '✅ Valid' : '❌ Invalid/Expired'));
        
        if ($status['token_expires_at']) {
            $this->line('- Expires At: ' . date('Y-m-d H:i:s', $status['token_expires_at']));
        }

        $this->line('');

        if (!$status['credentials_configured']) {
            $this->error('⚠️  Tesla OAuth credentials not configured');
            $this->line('Run: php artisan tesla:authenticate (without --show-status) to set up');
        } elseif (!$status['token_valid']) {
            $this->warn('⚠️  Authentication required');
            $this->line('Run: php artisan tesla:authenticate (without --show-status) to authenticate');
        } else {
            $this->info('✅ Tesla API is ready to use!');
        }
    }
}