<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    class SettingsTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            DB::table('settings')->insert([
                [
                    'key' => 'solar_edge_api_key',
                    'value' => 'your_solaredge_api_key_here',
                    'description' => 'API key for SolarEdge integration.',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'key' => 'email_notifications_enabled',
                    'value' => 'true',
                    'description' => 'Enable or disable email notifications for alerts.',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'key' => 'dashboard_refresh_interval',
                    'value' => '60', // seconds
                    'description' => 'Interval in seconds to refresh dashboard data.',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);
        }
    }
