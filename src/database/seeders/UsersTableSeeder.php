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
        $userId = DB::table('users')->insertGetId([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('accounts')->insert([
            'user_id' => $userId,
            'name' => 'テストユーザー',
            'postal_code' => '100-0001',
            'address' => '東京都千代田区',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
