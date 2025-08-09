<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    class ProductionDataTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            $system1 = DB::table('systems')->where('customer_name', 'Alice Johnson')->first();
            $system2 = DB::table('systems')->where('customer_name', 'Bob Smith Inc.')->first();
            $system3 = DB::table('systems')->where('customer_name', 'Tesla Solar Customer')->first();

            if ($system1) {
                DB::table('production_data')->insert([
                    [
                        'system_id' => $system1->id,
                        'date' => Carbon::now()->subDay(),
                        'energy_today' => 25.5, // kWh
                        'energy_yesterday' => 28.1, // kWh
                        'power_current' => 3.2, // kW
                        'efficiency' => 85.0, // percentage
                        'weather_temperature' => 22.5,
                        'weather_condition' => 'Partly Cloudy',
                        'weather_humidity' => 65,
                        'weather_wind_speed' => 3.2,
                        'weather_wind_direction' => 'SW',
                        'weather_pressure' => 1013.2,
                        'weather_uv_index' => 6,
                        'weather_cloud_cover' => 45,
                        'weather_solar_irradiance' => 750,
                        'weather_icon_url' => 'https://cdn.weatherapi.com/weather/64x64/day/116.png',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'system_id' => $system1->id,
                        'date' => Carbon::now(),
                        'energy_today' => 15.0, // kWh
                        'energy_yesterday' => 25.5, // kWh
                        'power_current' => 2.5, // kW
                        'efficiency' => 88.0, // percentage
                        'weather_temperature' => 26.0,
                        'weather_condition' => 'Sunny',
                        'weather_humidity' => 55,
                        'weather_wind_speed' => 2.8,
                        'weather_wind_direction' => 'W',
                        'weather_pressure' => 1015.1,
                        'weather_uv_index' => 8,
                        'weather_cloud_cover' => 15,
                        'weather_solar_irradiance' => 920,
                        'weather_icon_url' => 'https://cdn.weatherapi.com/weather/64x64/day/116.png',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ]);
            }

            if ($system2) {
                DB::table('production_data')->insert([
                    [
                        'system_id' => $system2->id,
                        'date' => Carbon::now()->subDay(),
                        'energy_today' => 120.0, // kWh
                        'energy_yesterday' => 115.5, // kWh
                        'power_current' => 15.0, // kW
                        'efficiency' => 90.0, // percentage
                        'weather_temperature' => 18.5,
                        'weather_condition' => 'Light Rain',
                        'weather_humidity' => 75,
                        'weather_wind_speed' => 4.1,
                        'weather_wind_direction' => 'NW',
                        'weather_pressure' => 1008.5,
                        'weather_uv_index' => 3,
                        'weather_cloud_cover' => 85,
                        'weather_solar_irradiance' => 450,
                        'weather_icon_url' => 'https://cdn.weatherapi.com/weather/64x64/day/296.png',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'system_id' => $system2->id,
                        'date' => Carbon::now(),
                        'energy_today' => 95.0, // kWh
                        'energy_yesterday' => 120.0, // kWh
                        'power_current' => 12.5, // kW
                        'efficiency' => 85.0, // percentage
                        'weather_temperature' => 21.0,
                        'weather_condition' => 'Cloudy',
                        'weather_humidity' => 70,
                        'weather_wind_speed' => 3.5,
                        'weather_wind_direction' => 'W',
                        'weather_pressure' => 1010.2,
                        'weather_uv_index' => 4,
                        'weather_cloud_cover' => 70,
                        'weather_solar_irradiance' => 580,
                        'weather_icon_url' => 'https://cdn.weatherapi.com/weather/64x64/day/119.png',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ]);
            }

            if ($system3) {
                DB::table('production_data')->insert([
                    [
                        'system_id' => $system3->id,
                        'date' => Carbon::now(),
                        'energy_today' => 35.2, // kWh
                        'energy_yesterday' => 38.5, // kWh
                        'power_current' => 8.1, // kW
                        'efficiency' => 92.0, // percentage
                        'weather_temperature' => 14.2,
                        'weather_condition' => 'Snow',
                        'weather_humidity' => 85,
                        'weather_wind_speed' => 5.2,
                        'weather_wind_direction' => 'N',
                        'weather_pressure' => 1005.8,
                        'weather_uv_index' => 2,
                        'weather_cloud_cover' => 95,
                        'weather_solar_irradiance' => 280,
                        'weather_icon_url' => 'https://cdn.weatherapi.com/weather/64x64/day/338.png',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ]);
            }
        }
    }
