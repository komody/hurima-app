<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'profile_image' => null,
            'postal_code' => '100-0001',
            'address' => '東京都千代田区千代田1-1',
            'building' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
