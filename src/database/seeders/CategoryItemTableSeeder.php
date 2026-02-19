<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = DB::table('items')->pluck('id', 'name')->toArray();
        $categories = DB::table('categories')->pluck('id', 'name')->toArray();

        // 商品名 => カテゴリ名の配列（1商品に複数カテゴリを紐付け可能）
        $itemCategoryMap = [
            '腕時計' => ['アクセサリー', 'メンズ'],
            'HDD' => ['家電'],
            '玉ねぎ3束' => ['キッチン'],
            '革靴' => ['ファッション', 'メンズ'],
            'ノートPC' => ['家電'],
            'マイク' => ['家電'],
            'ショルダーバッグ' => ['ファッション', 'レディース'],
            'タンブラー' => ['キッチン'],
            'コーヒーミル' => ['キッチン'],
            'メイクセット' => ['コスメ'],
        ];

        foreach ($itemCategoryMap as $itemName => $categoryNames) {
            if (!isset($items[$itemName])) {
                continue;
            }

            $itemId = $items[$itemName];

            foreach ($categoryNames as $categoryName) {
                if (!isset($categories[$categoryName])) {
                    continue;
                }

                $categoryId = $categories[$categoryName];

                DB::table('category_item')->insert([
                    'item_id' => $itemId,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
