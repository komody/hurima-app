<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        
        // ユーザーデータを最初にシード
        $this->call([
            UsersTableSeeder::class,
        ]);
        
        // マスターデータをシード
        $this->call([
            ConditionsTableSeeder::class,
            CategoriesTableSeeder::class,
        ]);
        
        // 商品データをシード（ユーザー、コンディション、カテゴリーに依存）
        $this->call([
            ItemTableSeeder::class,
        ]);
    }
}
