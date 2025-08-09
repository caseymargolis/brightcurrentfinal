<?php

namespace Database\Seeders;

use App\Models\System;
use Illuminate\Database\Seeder;

class SolarSystemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systems = [
            [
                'system_id' => 'SYS-ENP-001',
                'external_system_id' => '123456', // Replace with actual Enphase system ID
                'customer_name' => 'John Smith',
                'customer_type' => 'residential',
                'manufacturer' => 'enphase',
                'status' => 'active',
                'location' => '123 Solar Street, Austin, TX',
                'latitude' => 30.2672,
                'longitude' => -97.7431,
                'capacity' => 8.5,
                'install_date' => now()->subMonths(6),
                'last_seen' => now(),
                'api_enabled' => true,
            ],
            [
                'system_id' => 'SYS-SE-002',
                'external_system_id' => '789012', // Replace with actual SolarEdge site ID
                'customer_name' => 'Jane Doe',
                'customer_type' => 'residential',
                'manufacturer' => 'solaredge',
                'status' => 'active',
                'location' => '456 Green Avenue, Phoenix, AZ',
                'latitude' => 33.4484,
                'longitude' => -112.0740,
                'capacity' => 12.3,
                'install_date' => now()->subMonths(8),
                'last_seen' => now(),
                'api_enabled' => true,
            ],
            [
                'system_id' => 'SYS-TSL-003',
                'external_system_id' => '345678', // Replace with actual Tesla energy site ID
                'customer_name' => 'Mike Johnson',
                'customer_type' => 'residential',
                'manufacturer' => 'tesla',
                'status' => 'active',
                'location' => '789 Energy Lane, San Diego, CA',
                'latitude' => 32.7157,
                'longitude' => -117.1611,
                'capacity' => 15.7,
                'install_date' => now()->subMonths(4),
                'last_seen' => now(),
                'api_enabled' => true,
            ],
            [
                'system_id' => 'SYS-ENP-004',
                'external_system_id' => '567890',
                'customer_name' => 'Sarah Wilson',
                'customer_type' => 'commercial',
                'manufacturer' => 'enphase',
                'status' => 'active',
                'location' => '321 Business Park Dr, Denver, CO',
                'latitude' => 39.7392,
                'longitude' => -104.9903,
                'capacity' => 25.2,
                'install_date' => now()->subYear(),
                'last_seen' => now()->subHours(2),
                'api_enabled' => true,
            ],
            [
                'system_id' => 'SYS-SE-005',
                'external_system_id' => '234567',
                'customer_name' => 'Robert Brown',
                'customer_type' => 'residential',
                'manufacturer' => 'solaredge',
                'status' => 'maintenance',
                'location' => '654 Sunset Blvd, Los Angeles, CA',
                'latitude' => 34.0522,
                'longitude' => -118.2437,
                'capacity' => 9.8,
                'install_date' => now()->subMonths(10),
                'last_seen' => now()->subDays(1),
                'api_enabled' => false,
            ],
        ];

        foreach ($systems as $systemData) {
            System::updateOrCreate(
                ['system_id' => $systemData['system_id']],
                $systemData
            );
        }

        $this->command->info('Solar systems seeded successfully!');
    }
}
