<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // コンディション名からIDを取得するためのマッピング
        $conditions = DB::table('conditions')->pluck('id', 'name')->toArray();

        // ユーザーIDを取得（最初のユーザーを使用、存在しない場合は1をデフォルトとする）
        $sellerId = DB::table('users')->value('id') ?? 1;

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand_name' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_url' => '/storage/items/dummy1.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand_name' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_url' => '/storage/items/dummy2.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_url' => '/storage/items/dummy3.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand_name' => null,
                'description' => 'クラシックなデザインの革靴',
                'image_url' => '/storage/items/dummy4.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => null,
                'description' => '高性能なノートパソコン',
                'image_url' => '/storage/items/dummy5.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand_name' => null,
                'description' => '高音質のレコーディング用マイク',
                'image_url' => '/storage/items/dummy6.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image_url' => '/storage/items/dummy7.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand_name' => null,
                'description' => '使いやすいタンブラー',
                'image_url' => '/storage/items/dummy8.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image_url' => '/storage/items/dummy9.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => null,
                'description' => '便利なメイクアップセット',
                'image_url' => '/storage/items/dummy10.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        foreach ($items as $item) {
            // コンディション名からIDを取得
            $conditionId = null;
            $itemCondition = $item['condition'];

            // 完全一致を優先して検索
            if (isset($conditions[$itemCondition])) {
                $conditionId = $conditions[$itemCondition];
            } else {
                // 部分一致で検索
                foreach ($conditions as $name => $id) {
                    if (strpos($name, $itemCondition) !== false || strpos($itemCondition, $name) !== false) {
                        $conditionId = $id;
                        break;
                    }
                }
            }

            // コンディションが見つからない場合は最初のコンディションを使用
            if ($conditionId === null && !empty($conditions)) {
                $conditionId = reset($conditions);
            }

            DB::table('items')->insert([
                'name' => $item['name'],
                'price' => $item['price'],
                'brand_name' => $item['brand_name'],
                'description' => $item['description'],
                'image_url' => $item['image_url'],
                'condition_id' => $conditionId,
                'seller_id' => $sellerId,
                'buyer_id' => null,
                'sold_out' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
