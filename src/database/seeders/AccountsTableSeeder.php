<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = DB::table('users')->where('email', 'test@example.com')->value('id');

        if ($userId === null) {
            return;
        }

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
