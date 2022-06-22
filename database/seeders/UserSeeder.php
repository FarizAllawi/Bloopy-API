<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'user_name' => 'Bloopy Admin',
            'email' => 'support@bloopy.com',
            'password' => Hash::make('bloopy-admin-1234'),
            'user_role' => 'admin',
            'email_verified_at' => date(now())
        ]);

        DB::table('users')->insert([
            'user_name' => 'Bloopy Developer',
            'email' => 'developer@bloopy.com',
            'password' => Hash::make('bloopy-developer-1234'),
            'user_role' => 'developer',
            'email_verified_at' => date(now())
        ]);
    }
}
