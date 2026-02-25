<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 4. 商品一覧取得
 */
class ItemListAcquisitionTest extends TestCase
{
    use RefreshDatabase;

    private function createVerifiedUser(): User
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);
        Account::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'postal_code' => '1000001',
            'address' => '東京都千代田区',
        ]);
        return $user;
    }

    private function createItem(array $attributes = []): Item
    {
        $conditionId = $attributes['condition_id'] ?? Condition::create(['name' => '良好'])->id;
        $sellerId = $attributes['seller_id'] ?? User::create([
            'name' => '出品者',
            'email' => 'seller' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ])->id;

        $base = [
            'name' => 'テスト商品',
            'price' => 1000,
            'brand_name' => null,
            'description' => '説明文',
            'image_url' => '/storage/items/dummy.jpg',
            'condition_id' => $conditionId,
            'seller_id' => $sellerId,
            'buyer_id' => null,
            'sold_out' => false,
        ];

        return Item::create(array_merge($base, array_diff_key($attributes, array_flip(['condition_id', 'seller_id']))));
    }

    /**
     * 4.1 全商品を取得できる
     */
    public function test_all_items_are_displayed_on_product_list_page(): void
    {
        $this->createItem(['name' => '商品A']);
        $this->createItem(['name' => '商品B']);
        $this->createItem(['name' => '商品C']);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('商品A');
        $response->assertSee('商品B');
        $response->assertSee('商品C');
    }

    /**
     * 4.2 購入済み商品は「Sold」と表示される
     */
    public function test_purchased_items_are_displayed_as_sold(): void
    {
        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);
        $this->createItem([
            'name' => '購入済み腕時計',
            'sold_out' => true,
            'buyer_id' => $buyer->id,
        ]);
        $this->createItem(['name' => '販売中ノートPC']);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('SOLD');
        $response->assertSee('購入済み腕時計');
        $response->assertSee('販売中ノートPC');
    }

    /**
     * 4.3 自分が出品した商品は表示されない
     */
    public function test_own_listed_items_are_not_displayed_in_product_list(): void
    {
        $user = $this->createVerifiedUser();
        $condition = Condition::create(['name' => '良好']);
        $otherSeller = User::create([
            'name' => '他人',
            'email' => 'other@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);

        Item::create([
            'name' => '自分の出品商品',
            'price' => 1000,
            'brand_name' => null,
            'description' => '説明',
            'image_url' => '/storage/items/dummy.jpg',
            'condition_id' => $condition->id,
            'seller_id' => $user->id,
            'buyer_id' => null,
            'sold_out' => false,
        ]);
        Item::create([
            'name' => '他人の出品商品',
            'price' => 1000,
            'brand_name' => null,
            'description' => '説明',
            'image_url' => '/storage/items/dummy.jpg',
            'condition_id' => $condition->id,
            'seller_id' => $otherSeller->id,
            'buyer_id' => null,
            'sold_out' => false,
        ]);

        $response = $this->actingAs($user)->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertDontSee('自分の出品商品');
        $response->assertSee('他人の出品商品');
    }
}
