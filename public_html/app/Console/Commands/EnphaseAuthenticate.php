<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\EnphaseOAuthService;

class EnphaseAuthenticate extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'enphase:authenticate 
                            {--show-status : Show current authentication status}
                            {--username= : Username for password grant (DEPRECATED)}
                            {--password= : Password for password grant (DEPRECATED)}';

    /**
     * The console command description.
     */
    protected $description = 'Authenticate with Enphase API using OAuth 2.0 Authorization Code Grant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oauthService = new EnphaseOAuthService();

        if ($this->option('show-status')) {
            $this->showAuthenticationStatus($oauthService);
            return;
        }

        // Check for deprecated usage
        if ($this->option('username') || $this->option('password')) {
            $this->error('❌ Password grant authentication is no longer supported by Enphase API v4!');
            $this->line('');
            $this->warn('🔄 Enphase has deprecated the password grant type for security reasons.');
            $this->line('');
            $this->line('Please use the Authorization Code Grant flow instead:');
            $this->line('1. Run: php artisan enphase:authenticate (without username/password)');
            $this->line('2. Follow the browser-based authentication flow');
            return 1;
        }

        $this->line('🔆 Enphase API OAuth 2.0 Authentication');
        $this->line('');

        if (!$oauthService->hasValidCredentials()) {
            $this->error('❌ Enphase OAuth credentials not configured!');
            $this->line('');
            $this->line('Please add the following to your .env file:');
            $this->line('ENPHASE_CLIENT_ID=your_client_id_here');
            $this->line('ENPHASE_CLIENT_SECRET=your_client_secret_here');
            $this->line('ENPHASE_API_KEY=your_api_key_here');
            $this->line('');
            $this->line('Get your credentials from: https://developer-v4.enphase.com/');
            return 1;
        }

        // Generate authorization URL
        $authData = $oauthService->getAuthorizationUrl();
        
        $this->line('🔐 To authenticate with Enphase API, please:');
        $this->line('');
        $this->line('1. Open this URL in your browser:');
        $this->info($authData['auth_url']);
        $this->line('');
        $this->line('2. Log in with your Enphase Enlighten account');
        $this->line('3. Authorize the application to access your data');
        $this->line('4. After authorization, you will be redirected to a URL like:');
        $this->line('   http://localhost:8001/api/enphase/callback?code=AUTHORIZATION_CODE&state=' . $authData['state']);
        $this->line('');
        $this->line('5. Copy the AUTHORIZATION_CODE from the URL and paste it below:');
        $this->line('');

        $code = $this->ask('Enter the authorization code');

        if (empty($code)) {
            $this->error('❌ No authorization code provided');
            return 1;
        }

        try {
            $this->line('🔄 Exchanging authorization code for tokens...');
            $result = $oauthService->exchangeCodeForTokens($code);
            
            if ($result['success']) {
                $this->line('');
                $this->info('✅ Enphase API authentication successful!');
                $this->line('');
                $this->line('Token Information:');
                $this->line('- Access Token: ' . substr($result['access_token'], 0, 20) . '...');
                $this->line('- Token Type: ' . $result['token_type']);
                $this->line('- Expires In: ' . $result['expires_in'] . ' seconds');
                if ($result['refresh_token']) {
                    $this->line('- Refresh Token: Available');
                }
                $this->line('');
                $this->info('🎉 You can now use Enphase API endpoints!');
                $this->line('');
                $this->line('Test the connection with: php artisan solar:test-apis');
            } else {
                $this->error('❌ Authentication failed: ' . $result['message']);
                if (isset($result['status_code'])) {
                    $this->line('HTTP Status: ' . $result['status_code']);
                }
                if (isset($result['response_body'])) {
                    $this->line('Response: ' . $result['response_body']);
                }
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ Authentication failed: ' . $e->getMessage());
            $this->line('');
            $this->line('Please try again or check:');
            $this->line('- The authorization code is correct and not expired');
            $this->line('- Your client credentials are valid');
            $this->line('- Your redirect URI matches Enphase app configuration');
            return 1;
        }

        return 0;
    }

    /**
     * Show current authentication status
     */
    private function showAuthenticationStatus(EnphaseOAuthService $oauthService)
    {
        $this->line('🔆 Enphase API Authentication Status');
        $this->line('');

        $status = $oauthService->getAuthorizationStatus();

        $this->line('Credentials Configuration:');
        $this->line('- Client ID: ' . ($status['credentials_configured'] ? '✅ Configured' : '❌ Missing'));
        $this->line('- Client Secret: ' . ($status['credentials_configured'] ? '✅ Configured' : '❌ Missing'));
        $this->line('- API Key: ' . (!empty(config('solar.enphase.api_key')) ? '✅ Configured' : '❌ Missing'));
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
            $this->error('⚠️  Enphase OAuth credentials not configured');
            $this->line('Add credentials to .env and run: php artisan enphase:authenticate');
        } elseif (!$status['token_valid']) {
            $this->warn('⚠️  Authentication required');
            $this->line('Run: php artisan enphase:authenticate (without --show-status) to authenticate');
        } else {
            $this->info('✅ Enphase API is ready to use!');
        }

        $this->line('');
        $this->warn('⚠️  Note: Password grant authentication is deprecated by Enphase API v4');
        $this->line('Use Authorization Code Grant flow for new authentications.');
    }
}