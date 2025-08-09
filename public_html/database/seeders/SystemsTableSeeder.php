<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    class SystemsTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {        DB::table('systems')->insert([
            [
                'system_id' => 'BC-2025-0142',
                'customer_name' => 'Alice Johnson',
                'customer_type' => 'residential',
                'manufacturer' => 'solaredge',
                'status' => 'active',
                'location' => '123 Main St, Toronto, ON',
                'latitude' => 43.651070,
                'longitude' => -79.347015,
                'capacity' => 10.5, // kW
                'install_date' => Carbon::now()->subMonths(6),
                'last_seen' => Carbon::now(),
                'api_enabled' => true,
                'external_system_id' => 'SE123456789',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'system_id' => 'BC-2025-0265',
                'customer_name' => 'Bob Smith Inc.',
                'customer_type' => 'commercial',
                'manufacturer' => 'enphase',
                'status' => 'warning',
                'location' => '456 Business Ave, Vancouver, BC',
                'latitude' => 49.282729,
                'longitude' => -123.120738,
                'capacity' => 50.0, // kW
                'install_date' => Carbon::now()->subYear(),
                'last_seen' => Carbon::now()->subMinutes(30),
                'api_enabled' => true,
                'external_system_id' => 'ENP987654321',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'system_id' => 'BC-2025-0387',
                'customer_name' => 'Tesla Solar Customer',
                'customer_type' => 'residential',
                'manufacturer' => 'tesla',
                'status' => 'active',
                'location' => '789 Innovation Dr, Calgary, AB',
                'latitude' => 51.049999,
                'longitude' => -114.066666,
                'capacity' => 15.2, // kW
                'install_date' => Carbon::now()->subMonths(3),
                'last_seen' => Carbon::now(),
                'api_enabled' => true,
                'external_system_id' => 'TSL456789123',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
        }
    }
