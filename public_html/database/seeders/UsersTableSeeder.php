<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Carbon\Carbon;

    class UsersTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            DB::table('users')->insert([
                [
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.j@brightcurrent.com',
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => Carbon::now(),
                    'last_login' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => Carbon::now(),
                    'last_login' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);
        }
    }
