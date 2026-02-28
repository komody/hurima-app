<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 10. 商品購入機能
 */
class ItemPurchaseTest extends TestCase
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
            'postal_code' => '100-0001',
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

        $item = Item::create(array_merge($base, array_diff_key($attributes, array_flip(['condition_id', 'seller_id', 'category_ids']))));
        $categoryIds = $attributes['category_ids'] ?? null;
        if ($categoryIds !== null) {
            $item->categories()->attach($categoryIds);
        } else {
            $category = Category::firstOrCreate(['name' => 'テストカテゴリ']);
            $item->categories()->attach($category->id);
        }
        return $item;
    }

    /**
     * 10.1 購入が完了する
     */
    public function test_purchase_can_be_completed(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => '購入テスト商品']);

        $response = $this->actingAs($user)->post(route('purchase.complete-convenience', ['item_id' => $item->id]), [
            'payment_method' => 'コンビニ支払い',
        ]);

        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('message', '購入が完了しました。');
        $this->assertTrue($item->fresh()->sold_out);
        $this->assertEquals($user->id, $item->fresh()->buyer_id);
        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * 10.2 購入済み商品は商品一覧で「Sold」と表示される
     */
    public function test_purchased_item_is_displayed_as_sold_on_item_list(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => 'Sold表示テスト商品']);

        $this->actingAs($user)->post(route('purchase.complete-convenience', ['item_id' => $item->id]), [
            'payment_method' => 'コンビニ支払い',
        ]);

        $response = $this->get(route('items.index'));
        $response->assertStatus(200);
        $response->assertSee('SOLD');
        $response->assertSee('Sold表示テスト商品');
    }

    /**
     * 10.3 購入した商品がプロフィールの購入一覧に追加される
     */
    public function test_purchased_item_is_added_to_profile_purchased_list(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => 'マイページ購入テスト商品']);

        $this->actingAs($user)->post(route('purchase.complete-convenience', ['item_id' => $item->id]), [
            'payment_method' => 'コンビニ支払い',
        ]);

        $response = $this->actingAs($user)->get(route('mypage.index', ['page' => 'buy']));
        $response->assertStatus(200);
        $response->assertSee('マイページ購入テスト商品');
    }
}
