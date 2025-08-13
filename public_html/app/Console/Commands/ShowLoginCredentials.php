<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ShowLoginCredentials extends Command
{
    protected $signature = 'auth:show-credentials {--create-test-user}';
    protected $description = 'Show available login credentials or create a test user';

    public function handle()
    {
        if ($this->option('create-test-user')) {
            return $this->createTestUser();
        }

        $this->info('🔐 Available Login Credentials for Solar Monitoring App');
        $this->info(str_repeat('=', 60));
        
        $users = User::all(['id', 'name', 'email']);
        
        if ($users->isEmpty()) {
            $this->warn('No users found in database. Creating a test user...');
            return $this->createTestUser();
        }

        $this->info('Based on the seeders, here are the available accounts:');
        $this->newLine();

        // Show users from database
        foreach ($users as $user) {
            $this->line("👤 User: {$user->name}");
            $this->line("   📧 Email: {$user->email}");
            $this->line("   🔑 Password: 12345678 (default from seeder)");
            $this->newLine();
        }

        $this->info('🌐 Login URL: http://localhost:8001/login');
        $this->info('📊 Dashboard URL: http://localhost:8001/dashboard');
        
        $this->newLine();
        $this->comment('Note: If these passwords don\'t work, run this command with --create-test-user flag');
        
        return 0;
    }

    private function createTestUser()
    {
        $email = 'test@solar.com';
        $password = 'password123';
        
        // Check if test user already exists
        if (User::where('email', $email)->exists()) {
            $this->info('✅ Test user already exists!');
        } else {
            User::create([
                'name' => 'Test User',
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            $this->info('✅ Test user created successfully!');
        }

        $this->newLine();
        $this->info('🔐 Test User Login Credentials:');
        $this->info(str_repeat('=', 40));
        $this->line("👤 Name: Test User");
        $this->line("📧 Email: {$email}");
        $this->line("🔑 Password: {$password}");
        $this->newLine();
        $this->info('🌐 Login URL: http://localhost:8001/login');
        $this->info('📊 Dashboard URL: http://localhost:8001/dashboard');
        
        return 0;
    }
}