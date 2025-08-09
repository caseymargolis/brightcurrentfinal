<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    class ServiceSchedulesTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            $system1 = DB::table('systems')->where('customer_name', 'Alice Johnson')->first();
            $system2 = DB::table('systems')->where('customer_name', 'Bob Smith Inc.')->first();

            if ($system1) {
                DB::table('service_schedules')->insert([
                    [
                        'system_id' => $system1->id,
                        'scheduled_date' => Carbon::now()->addDays(7),
                        'scheduled_time' => '10:00 AM',
                        'service_type' => 'Annual Maintenance',
                        'notes' => 'Check all panels and inverter.',
                        'status' => 'scheduled',
                        'assigned_technician' => 'Technician A',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ]);
            }

            if ($system2) {
                DB::table('service_schedules')->insert([
                    [
                        'system_id' => $system2->id,
                        'scheduled_date' => Carbon::now()->addDays(2),
                        'scheduled_time' => '02:00 PM',
                        'service_type' => 'Inverter Repair',
                        'notes' => 'Inverter showing error code 34.',
                        'status' => 'in_progress',
                        'assigned_technician' => 'Technician B',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ]);
            }
        }
    }
