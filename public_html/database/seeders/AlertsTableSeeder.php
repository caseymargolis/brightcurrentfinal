<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    class AlertsTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            $system1 = DB::table('systems')->where('system_id', 'BC-2025-0142')->first();
            $system2 = DB::table('systems')->where('system_id', 'BC-2025-0265')->first();

            if ($system1) {
                DB::table('alerts')->insert([
                    [
                        'system_id' => $system1->id,
                        'severity' => 'warning',
                        'alert_type' => 'Low Production',
                        'message' => 'System production is lower than expected for the day.',
                        'status' => 'pending',
                        'resolved_at' => null,
                        'created_at' => Carbon::now()->subHours(5),
                        'updated_at' => Carbon::now()->subHours(5),
                    ],
                ]);
            }

            if ($system2) {
                DB::table('alerts')->insert([
                    [
                        'system_id' => $system2->id,
                        'severity' => 'critical',
                        'alert_type' => 'Offline',
                        'message' => 'System has been offline for more than 24 hours.',
                        'status' => 'pending',
                        'resolved_at' => null,
                        'created_at' => Carbon::now()->subDays(2),
                        'updated_at' => Carbon::now()->subDays(2),
                    ],
                    [
                        'system_id' => $system2->id,
                        'severity' => 'info',
                        'alert_type' => 'Communication Issue',
                        'message' => 'Brief communication loss detected, now restored.',
                        'status' => 'resolved',
                        'resolved_at' => Carbon::now()->subHours(1),
                        'created_at' => Carbon::now()->subHours(2),
                        'updated_at' => Carbon::now()->subHours(1),
                    ],
                ]);
            }
        }
    }
