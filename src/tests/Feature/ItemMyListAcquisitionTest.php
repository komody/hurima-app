<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 5. マイリスト一覧取得
 */
class ItemMyListAcquisitionTest extends TestCase
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
     * 5.1 いいねした商品だけが表示される
     */
    public function test_only_liked_items_are_displayed_in_my_list(): void
    {
        $user = $this->createVerifiedUser();
        $likedItem = $this->createItem(['name' => 'いいねした商品']);
        $this->createItem(['name' => 'いいねしてない商品']);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']));

        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしてない商品');
    }

    /**
     * 5.2 購入済み商品は「Sold」と表示される（マイリスト）
     */
    public function test_purchased_items_are_displayed_as_sold_in_my_list(): void
    {
        $user = $this->createVerifiedUser();
        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer2@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);
        $soldItem = $this->createItem([
            'name' => 'マイリストの購入済み商品',
            'sold_out' => true,
            'buyer_id' => $buyer->id,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']));

        $response->assertStatus(200);
        $response->assertSee('SOLD');
        $response->assertSee('マイリストの購入済み商品');
    }

    /**
     * 5.3 未認証の場合は商品が表示されない（メッセージ以外）
     */
    public function test_no_items_displayed_when_not_authenticated_in_my_list(): void
    {
        $this->createItem(['name' => '誰かの商品']);

        $response = $this->get(route('items.index', ['tab' => 'mylist']));

        $response->assertStatus(200);
        $response->assertDontSee('誰かの商品');
    }
}
